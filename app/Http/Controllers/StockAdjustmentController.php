<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\StockAdjustmentRequest;
use App\Http\Requests\UpdateStockAdjustmentRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\MyPDF;
use App\StockManage;
use App\Warehouse;
use App\Products;
use App\Supplier;
use App\Inventory;
use App\Purchase;
use App\Invoice;
use App\Order;
use App\Customer;
use App\SupplierPriceRecord;
use App\User;
use Yajra\Datatables\Facades\Datatables;

use DB;
use View;
use Cache;
use Exception;

class StockAdjustmentController extends Controller
{
    public function __construct()
    {
        // Cache::forget('stockmanages');
        // if (!Cache::has('stockmanages'))
        // {
        // 	CacheController::rebuildAllCache();
        // }
        \View::share('title', "Inventory");
        View::share('load_head', true);
        View::share('stock_adjustments', true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if (!is_allowed('stocks-list')) {
            return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }
        $warehouse = Warehouse::all();
        $supplier = Supplier::all();
        $is_allowed_pp = is_allowed('product-show-purchase-price');
        return view('stock_adjustments.index', compact(['warehouse', 'supplier', 'is_allowed_pp', 'request']));
    }

    // public function updateView()
    // {
    // 	$stock_adjustments = StockManage::all();
    // 	$warehouse = Warehouse::all();
    // 	$product = Products::all();
    // 	$supplier = Supplier::all();

    // 	return view('stock_adjustments.index', compact(['stock_adjustments','warehouse','product','supplier']))->render();
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if (!is_allowed('stocks-create')) {
            return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }
        return view('stock_adjustments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(StockAdjustmentRequest $request, $boolean = false)
    {
        // dd($request->all());
        // $check_zero_quantity = false;
        foreach ($request->quantity as $quantity) {
            if ($quantity <= 0) {
                return response()->json(['message' => "Not All Quantity can be ZERO"], 400);
                // $check_zero_quantity = true;
            }
        }

        $product_ids = $request->product_id;
        $is_allowed_pp = is_allowed('product-show-purchase-price');
        if (!is_allowed('stocks-create')) {
            return response(['message'=>'Unauthorised'], 500);
        }
        DB::beginTransaction();
        $is_purchase = ($request->is_purchase > 0)?"in":"out";
        $warehouse_id = $request->warehouse_id;
        $customer_id = $request->customer_id;
        $supplier_id = $request->supplier_id;
        $date =  date('Y-m-d', strtotime($request->date));
        $is_transfer = ($request->is_transfer > 0)?true:false;
        $loop = 0; // $loop = 1; what the hell is done by imranyahya, no logic at all!
        $invoice_id = $order = false;
        $total = 0;

        foreach ($product_ids as $key => $value) {
            $order = false;
            $batch_id = null;
            try {
              
                    $stock_adjustment = new StockManage();
                    $stock_adjustment->type = $is_purchase;
                    $stock_adjustment->product_id = $value;
                    $stock_adjustment->warehouse_id = $warehouse_id;
                    $stock_adjustment->notes = $request->notes[$key];
                    $stock_adjustment->quantity = $request->quantity[$key];
                    $stock_adjustment->date = $date;
                    $stock_adjustment->delivery_ChallanNo = $request->next_deliveryChallan?$request->next_deliveryChallan:null;
                    $stock_adjustment->added_by = \Auth::id();
                    $stock_adjustment->save();

                    if ($is_transfer)
                    {
                        $stock_adjustment = new StockManage();
                        $stock_adjustment->type = "in";
                        $stock_adjustment->product_id = $value;
                        $stock_adjustment->warehouse_id = $request->to_warehouse;
                        $stock_adjustment->notes = $request->notes[$key];
                        $stock_adjustment->quantity = $request->quantity[$key];
                        $stock_adjustment->date = $date;
                        // $stock_adjustment->delivery_ChallanNo = $request->next_deliveryChallan?$request->next_deliveryChallan:null;
                        $stock_adjustment->added_by = \Auth::id();
                        $stock_adjustment->save();
                    }
                // }
            } catch (\Exception $e) {
                DB::rollBack();
                if ($boolean) {
                    return false;
                }
                return response()->json(['message' => $e->getMessage()], 403);
            }
        }//end of foreach loop

        // if ($is_purchase && !$is_transfer && 0==1) { // don't enter this code block, what the hell is done by imranyahya 
        //     $invoice = new Invoice();
        //     $invoice->description = " ";
        //     $invoice->shipping = 0;
        //     if ($customer_id) {
        //         $invoice->customer_id = $customer_id;
        //     }
        //     if ($supplier_id) {
        //         $invoice->supplier_id = $supplier_id;
        //     }
        //     $invoice->discount = 0;
        //     $invoice->total = $total;
        //     $invoice->type="purchase";
        //     $invoice->date = $date;
        //     $invoice->added_by = \Auth::id();
        //     $invoice->save();
        //     $invoice_id = $invoice->id;
        // }
        //if it is a transfer then we will deduct from warehouse_id and add to to_warehouse
        // if ($is_transfer) {
        //     // $loop = 2;
        // }
        // while ($loop > 0) {
        //     foreach ($product_ids as $key => $value) {
        //         $order = false;
        //         $batch_id = null;
        //         try {
        //             if ($customer_id && $request->purchase_price[$key]) {// this case is for ali shan repo only
        //                 $is_purchase = "purchase";
        //                 if ($invoice_id) {
        //                     $total += $request->purchase_price[$key]*$request->quantity[$key];
        //                     $order = new Order();
        //                     $order->invoice_id = $invoice_id;
        //                     $order->product_id = $value;
        //                     $order->salePrice = $request->purchase_price[$key];
        //                     $order->quantity = $request->quantity[$key];
        //                     $order->save();
        //                 }
        //             } elseif ($supplier_id && $request->is_purchase) {
        //                 $productPrice = SupplierPriceRecord::where([
        //                     'supplier_id' => $supplier_id,
        //                     'product_id' => $value
        //                 ])->orderBy('id', 'DESC')->first();
        //                 if ($is_allowed_pp == false) {
        //                     // if user is not allowed to show purchase price then only add product if its price is already given
        //                     //  else exception is thrown
        //                     if (empty($productPrice)) {
        //                         throw new Exception('Missing Product Price, Contact Administrator to Add Purchase First');
        //                     } else {
        //                         $batch_id = $productPrice->id;
        //                         $purchassePrice = $productPrice->price;
        //                     }
        //                 } else {
        //                     if (empty($productPrice)  || $productPrice->price != $request->purchase_price[$key]) {
        //                         $purchase  = SupplierPriceRecord::create([
        //                             'supplier_id' => $supplier_id,
        //                             'product_id' => $value,
        //                             'price' => $request->purchase_price[$key]
        //                         ]);
        //                         $batch_id = $purchase->id;
        //                         $purchassePrice = $purchase->price;
        //                     } else {
        //                         $batch_id = $productPrice->id;
        //                         $purchassePrice = $request->purchase_price[$key];
        //                     }
        //                 }
        //                 $total += $request->purchase_price[$key]*$request->quantity[$key];
        //                 $order = new Order();
        //                 $order->invoice_id = $invoice_id;
        //                 $order->product_id = $value;
        //                 $order->salePrice = $purchassePrice;
        //                 $order->quantity = $request->quantity[$key];
        //                 $order->save();
        //             }
        //             if ($request->quantity[$key] > 0) {
        //                 $stock_adjustment = new StockManage();
        //                 $stock_adjustment->type = $is_purchase;
        //                 $stock_adjustment->product_id = $value;
        //                 $stock_adjustment->warehouse_id = $warehouse_id;
        //                 $stock_adjustment->notes = $request->notes[$key];
        //                 $stock_adjustment->quantity = $request->quantity[$key];
        //                 $stock_adjustment->date = $date;
        //                 $stock_adjustment->delivery_ChallanNo = $request->next_deliveryChallan?$request->next_deliveryChallan:null;
        //                 $stock_adjustment->added_by = \Auth::id();
        //                 $stock_adjustment->batch_id = $batch_id;
        //                 if ($customer_id) {
        //                     $stock_adjustment->customer_id = $customer_id;
        //                 }
        //                 if ($request->sale_orders_id) {
        //                     $stock_adjustment->sale_orders_id = $request->sale_orders_id;
        //                 }
        //                 if ($order) {
        //                     $stock_adjustment->purchase_id = $order->id;
        //                 }
        //                 $stock_adjustment->save();
        //             }
        //         } catch (\Exception $e) {
        //             DB::rollBack();
        //             if ($boolean) {
        //                 return false;
        //             }
        //             return response()->json(['message' => $e->getMessage()], 403);
        //         }
        //     }//end of foreach loop
        //     if (isset($invoice)) {
        //         $invoice->total = $total;
        //         $invoice->save();
        //     }
        //     //reduce the count
        //     $loop--;
        //     //work only if the warehouse transfer is triggered
        //     if ($is_transfer) {
        //         $is_purchase = "in";
        //         $warehouse_id = $request->to_warehouse;
        //     }
        // }//end of while loop
        // \Artisan::call("purchase:move");
        DB::commit();
        // Update Product Count
        // Cache::forget('products');
        if ($boolean) {
            return true;
        }
        return response()->json(['message' => 'Stock is successfully updated','action'=>'update','do'=>'.stocks_listing'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    // public function show($id)
    // {
    //     if (!is_allowed('stocks-list')) {
    //         return redirect('/');
    //         // return response(['message'=>'Unauthorised'],500);
    //     }
    //     $stock_adjustment = StockManage::findOrFail($id);

    //     return view('stock_adjustments.show', compact('stock_adjustment'));
    // }

    public function show($id)
    {
        View::share('load_head', false);
        $stock = StockManage::with('customer', 'supplier', 'warehouse', 'product', 'sale')->findOrFail($id);

        $background = storage_path('app/public/inventory_bg.jpg')?:'';
        $pdf = new MyPDF($title= "Receipt",$background);
        $pdf->addPage();
        $pdf->SetFont(PDF_FONT_NAME_MAIN, '', 10);
        // $pdf->SetY(intval(session()->get('settings.misc.content_position') * 3));

        $receipt_head =  view('stock_adjustments.pdf.receipt_header')->render();
        $pdf->SetY(20);
        $pdf->writehtml($receipt_head);

        $receipt_details =  view('stock_adjustments.pdf.receipt_details', compact('stock'))->render();
        $pdf->SetY(30);
        $pdf->writehtml($receipt_details);
        
        $receipt_footer =  view('stock_adjustments.pdf.receipt_footer', compact('stock'))->render();
        $pdf->writehtml($receipt_footer);

        $receipt_signature =  view('stock_adjustments.pdf.receipt_signature')->render();
        $pdf->SetY(253);
        $pdf->writehtml($receipt_signature);

        $receipt_print_by =  view('stock_adjustments.pdf.receipt_print_on')->render();
        $pdf->SetY(272);
        $pdf->writehtml($receipt_print_by);
        return response($pdf->output('receipt.pdf', 'S'))->header('Content-Type', 'application/pdf');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if (!is_allowed('stocks-edit')) {
            return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
        }
        $stock_adjustment = StockManage::findOrFail($id);
        $warehouses = Warehouse::all();
        return view('stock_adjustments.edit', compact('stock_adjustment', 'warehouses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param Request $request
     * @return Response
     */
    public function update(UpdateStockAdjustmentRequest $request, $id)
    {
        if (!is_allowed('stocks-edit')) {
            // return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }
        $date = $request->date;

        DB::beginTransaction();
        try {
            $stock_adjustment = StockManage::findOrFail($id);
            $old_qty = $stock_adjustment->quantity;
            $stock_adjustment->quantity = $request->quantity;
            $stock_adjustment->warehouse_id = $request->warehouse;
            $stock_adjustment->notes = $request->notes;
            $stock_adjustment->date = date('Y-m-d', strtotime($date));
            $stock_adjustment->edited_by = \Auth::id();
            if ($old_qty !=  $request->quantity) {
                if ($stock_adjustment->quantity == "purchase" || $stock_adjustment->quantity == "sale" || $stock_adjustment->quantity == "refund") {
                    throw new \Exception("You cannot modify stock quantity of ".$stock_adjustments->type." directly", 1);
                }
            }

            if ($stock_adjustment->purchase_id) {
                $order = Order::whereId($stock_adjustment->purchase_id)->first();
                if ($order) {
                    $order->quantity = $request->quantity;
                }
                $order->save();
                fix_invoice_total($order->invoice_id);
            }
                

            $stock_adjustment->save();
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json(['message' => $e->getMessage()." Product:". $stock_adjustment->product->name], 403);
        }

        DB::commit();
        // Cache::forget('stockmanages');
        Cache::forget('products');

        return response()->json(['message' => 'Stock is successfully updated','action'=>'redirect','do'=>url('/stock')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (!is_allowed('stocks-delete')) {
            // return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }

        try {
            $this::deleteRequest($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
        // Cache::forget('stockmanages');
        // Cache::forget('products');

        return response()->json(['message' => 'Updated','action'=>'redirect','do'=>url('/stock')], 200);
    }

    public function bulkOperation(Request $r)
    {
        if (!is_allowed('stocks-delete')) {
            // return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }
        DB::beginTransaction();
        $all_stock = $r->operation_id;
        $operation = $r->operation;
        if (!$all_stock || !$operation) {
            return response()->json(['message' => 'Please Select item to delete'], 403);
        }
        foreach ($all_stock as $key => $value) {
            try {
                switch ($operation) {
                case 'delete':
                        $this::deleteRequest($value);
                    break;
                
                default:
                    # code...
                    break;
                }
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json(['message' => $e->getMessage(). " - No Item Deleted *Item Code: ".$value." *"], 403);
            }
        }
        DB::commit();
        // Cache::forget('stockmanages');
        Cache::forget('products');

        return response()->json(['message' => 'Updated','action'=>'redirect','do'=>url('/stock')], 200);
    }
    private function deleteRequest($id)
    {
        if (!is_allowed('stocks-delete')) {
            // return redirect('/');
            return false;
        }
        $stock_adjustment = StockManage::findOrFail($id);

        $stock_adjustment->delete();
        // Cache::put('stock_adjustments_index',$this::updateView(),3600);

        return true;
    }
    public function datatables_old()
    {
        // echo "abc";
        
        // $all = unserialize(Cache::get('stockmanages'));

        return Datatables::of(StockManage::with('customer')->with('supplier')->with('warehouse')->with('products'))
        ->add_column('id_display', function ($row) {
            return "<input type=\"checkbox\" value=\"{$row->id}\" class=\"form-control\" name=\"operation_id[]\">";
        })

        ->make(true);
    }

    public function datatables()
    {
        $date_format = session()->get('settings.misc.date_format');
        $query = StockManage::with('customer', 'supplier', 'warehouse', 'product', 'sale');
        $users = User::pluck('name', 'id')->toArray();
        $is_allowed_pp = is_allowed('product-show-purchase-price');
        return Datatables::of($query)
        ->add_column('id_display', function ($row) {
            return "<input type=\"checkbox\" value=\"{$row->id}\" class=\"form-control\" name=\"operation_id[]\">";
        })
        ->edit_column('stocklog.date', function ($row) use ($date_format) {
            return (strtotime($row->date))?date($date_format, strtotime($row->date)):"-";
        })->edit_column('product.barcode', function ($row) {
            return ($row->product)?"<a href='/products/{$row->product->id}'>{$row->product->barcode}</a>":"N/A";
        }, 5)
        ->edit_column('product.name', function ($row) {
            return ($row->product)?"<a href='/products/{$row->product->id}'>{$row->product->name}</a>":"N/A";
        }, 5)
        ->edit_column('product.brand', function ($row) {
            return ($row->product)?"<a href='/products/{$row->product->id}'>{$row->product->brand}</a>":"N/A";
        })
        ->edit_column('stocklog.batch_id', function ($row) {
            return empty($row->batch_id) ? "-" : $row->batch_id;
        })
        ->edit_column('customer.name', function ($row) {
            return ($row->customer)?"<a href='/customers/{$row->customer->id}'>{$row->customer->name}<br><small>{$row->customer->city}</small></a>":"N/A";
        })
        ->edit_column('warehouse.name', function ($row) {
            return ($row->warehouse)?"<a href='/warehouses/{$row->warehouse->id}'>{$row->warehouse->name}</a>":"N/A";
        })
        ->edit_column('supplier.name', function ($row) {
            return ($row->supplier)?"<a href='/suppliers/{$row->supplier->id}'>{$row->supplier->name}<br><small>{$row->supplier->address}</small></a>":"N/A";
        })
        ->edit_column('notes', function ($row) {
            return ($row->notes)?:'-';
        })
        ->edit_column('stocklog.quantity', function ($row) {
            return ($row->type == "in" || $row->type == "purchase")
            ?"<span class='label label-success'>+".floatval($row->quantity + 0)."</span>":
            "<span class='label label-danger'>-".floatval($row->quantity + 0)."</span>";
        })
        ->edit_column('stocklog.sale_id', function ($row) use ($is_allowed_pp) {
            switch ($row->type) {
                case "purchase":
                    if ($is_allowed_pp) {
                        $invoice_id = $row->sale->invoice_id;
                    }
                    break;
                case "sale":
                    $invoice_id = $row->sale->invoice_id;
                    break;
                default:
                    $invoice_id = null;
            }
            if ($invoice_id != null) {
                return '<a target="_blank" href="'. route('invoices.show', $invoice_id) .'">'.($invoice_id).'</a> ';
            }
            return '-';
        })->edit_column('stocklog.type', function ($row) {
            return $row->type;
        })
        ->edit_column('stocklog.updated_at', function ($row) use ($date_format) {
            return ($row->updated_at) ? date($date_format . ' H:i', strtotime($row->updated_at)) : "N/A";
        })->add_column('options', function ($row) {
            return '<a class="btn btn-xs btn-primary" href="'. route('stock.show', $row->id) .'"><i class="glyphicon glyphicon-eye-open"></i> Print</a>
            <a class="btn btn-xs btn-warning" href="'. route('stock.edit', $row->id) .'"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
        })
        ->remove_column('id')
        ->add_column('added_by', function ($row) use ($users) {
            return (!empty($users[$row->added_by])) ? $users[$row->added_by] : "-";
        })
        ->add_column('updated_by', function ($row) use ($users) {
            return (!empty($users[$row->edited_by])) ? $users[$row->edited_by] : "-";
        })
        ->edit_column('stocklog.id', function ($row) {
            return $row->id;
        })
        ->make(true);
    }
}
