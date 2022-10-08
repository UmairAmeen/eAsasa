<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;

use App\Http\Controllers\Controller;

use App\Products;
use App\Purchase;
use App\Warehouse;
use App\Customer;
use App\SupplierPriceRecord;
use App\StockManage;
use App\Supplier;
use App\Invoice;
use App\Transaction;
use App\Order;
use App\Rates;
use App\Unit;
use App\ProductView;
use App\ProductCategory;
use App\ProductGroup;
// use App\StockManage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cache;
use Exception;
use Storage;


use Yajra\Datatables\Facades\Datatables;

class ProductController extends Controller
{
    private $products;

    public function __construct()
    {
        // if (!Cache::has('products')) {
        // 	CacheController::rebuildAllCache();
        // }
        \View::share('title', "Product");
        \View::share('load_head', true);
        \View::share('product_menu', true);
        $units = Unit::all();
        \View::share('units', $units);
        \View::share('product_categories', ProductCategory::all());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $q)
    {
        if (!is_allowed('product-list')) {
            return redirect('/');
        }
        // $products = Products::where('name','like','%'.$q->q."%")->orderBy('id','asc')->paginate(10);
        $warehouse = Warehouse::all();
        $suppliers = Supplier::all();
        $category = ProductCategory::all();
        $products_count = Products::count();
        return view('products.index', compact('category', 'products_count', 'warehouse', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(ProductRequest $request)
    {
        if (!is_allowed('product-create')) {
            return response(['message'=>'Unauthorized'], 500);
        }
        DB::beginTransaction();
        foreach ($request->name as $key => $value) {
            try {

                //create a new product
                $product_p = new Products();
                $product_p->name = $request->name[$key];
                $product_p->size = $request->size[$key];
                $product_p->color = $request->color[$key];
                $product_p->pattern = $request->pattern[$key];
                $product_p->length = $request->length[$key];
                $product_p->width = $request->width[$key];
                $product_p->height = $request->height[$key];
                if ($request->barcode[$key]) {
                    $product_p->barcode	= $request->barcode[$key];
                }
                $product_p->brand  = $request->brand[$key];
                $product_p->pct_code  = $request->pct_code[$key];
                $product_p->tax_rate  = $request->tax_rate[$key];
                $product_p->status  = true;
                $product_p->type = ($request->type[$key])?'raw':'final';
                $product_p->category_id = $request->product_category[$key];
                $product_p->translation = $request->translation[$key];

                $product_p->salePrice = $request->sale_price[$key]?$request->sale_price[$key]:0;
                $product_p->min_sale_price = $request->min_sale_price[$key]?$request->min_sale_price[$key]:0;
                $product_p->notify = $request->notify_quantity[$key]?$request->notify_quantity[$key]:1;
                $product_p->unit_id = $request->unit[$key];
                $product_p->description=$request->description[$key];
                $product_p->added_by = \Auth::id();
                $can_view_purchase_price = is_allowed('product-show-purchase-price');
                if ($can_view_purchase_price){
                    $product_p->next_purchase_price = $request->next_purchase_price[$key];
                }
                $product_p->save();

                $croped_image = $request->image[$key];
                if ($croped_image) {
                    list($type, $croped_image) = explode(';', $croped_image);
                    list(, $croped_image)      = explode(',', $croped_image);
                    $croped_image = base64_decode($croped_image);

                    $image_path = Storage::put($product_p->id.".png", $croped_image);
                    $product_p->image_path = $product_p->id.".png";
                    $product_p->save();
                }

                if (session()->get('settings.barcode.is_enable') && empty($product_p->barcode)) {
                    $product_p->barcode = $product_p->id;
                }
                if (session()->get('settings.products.enable_advance_fields')) {
                    if ($request->purchase_price[$key] && $request->initial_stock[$key]) {
                        if (!$request->supplier[$key]) {
                            $row = $key + 1;
                            throw new \Exception("Purchase require a supplier, or remove purchase price at row: ". $row, 1);
                        }
                        // $latestProduct = Purchase::orderBy('id', 'DESC')->first();
                        // if (!$latestProduct) {
                        // 	$pid = 1;
                        // } else {
                        // 	$pid = $latestProduct->id +1;
                        // }
                        // $purchase = new Purchase();
                        // $purchase->product_id = $product_p->id;
                        // $purchase->stock = $request->initial_stock[$key];
                        // $purchase->price = $request->purchase_price[$key];
                        // $purchase->pid = $pid;
                        // $purchase->warehouse_id = $request->warehouse[$key];
                        // $purchase->date = date('Y-m-d');
                        // $purchase->supplier_id = $request->supplier[$key];
                        // $purchase->save();
                        // try{
                        // DB::beginTransaction();
                        $total = $request->purchase_price[$key]*$request->initial_stock[$key];
                        $invoice = new Invoice();
                        $invoice->description = " ";
                        $invoice->shipping = 0;
                        $invoice->supplier_id = $request->supplier[$key];
                        $invoice->discount = 0;
                        $invoice->total = $total;
                        $invoice->type="purchase";
                        $invoice->date = date('Y-m-d');
                        $invoice->added_by = \Auth::id();
                        $invoice->save();
                        $invoice_id = $invoice->id;
                        // $transaction = new Transaction;
                        // $transaction->date = date('Y-m-d');
                        // $transaction->supplier_id = $request->supplier[$key];
                        // $transaction->amount = $total;
                        // $transaction->type = "in";//debit
                        // $transaction->invoice_id = $invoice_id;
                        // $transaction->added_by = \Auth::id();
                        // $transaction->save();
                        $order = new Order();
                        $order->invoice_id = $invoice_id;
                        $order->product_id = $product_p->id;
                        $order->salePrice = $request->purchase_price[$key];
                        $order->quantity = $request->initial_stock[$key];
                        $order->save();
                        $batch = supplier_price_record(date('Y-m-d'), $request->purchase_price[$key], $request->supplier[$key], $product_p->id);
                        $sale = new StockManage();
                        $sale->date = date('Y-m-d');
                        $sale->type = "purchase";
                        $sale->supplier_id = $request->supplier[$key];
                        $sale->sale_id = $order->id;
                        $sale->batch_id = (session()->get('settings.sales.use_stock'))? $batch : null;
                        $sale->product_id = $product_p->id;
                        $sale->warehouse_id = $request->warehouse[$key];
                        $sale->quantity = $request->initial_stock[$key];
                        $sale->added_by = \Auth::id();
                        $sale->save();

                    //     DB::commit();
                    // } catch(Exception $e) {
                    //     echo "\n".$e->getLine()."\n".$e->getMessage()."\n\n";
                    //     DB::rollBack();
                    // }
                    } elseif ($request->initial_stock[$key]) {
                        $stockManage = new StockManage;
                        $stockManage->warehouse_id = $request->warehouse[$key];
                        $stockManage->product_id=$product_p->id;
                        $stockManage->type="in";
                        $stockManage->date = date('Y-m-d');
                        $stockManage->quantity = $request->initial_stock[$key];
                        $stockManage->added_by = \Auth::id();
                        $stockManage->save();
                    } elseif ($request->purchase_price[$key] && $request->supplier[$key]) {
                        supplier_price_record(date('Y-m-d'), $request->purchase_price[$key], $request->supplier[$key], $product_p->id);
                    }
                }

                //create a new purchase for initial stock
                // $purchase = new Purchase();
                // $purchase->prid = $prid;
                // $purchase->product_id = $pid;
                // $purchase->stock = $request->initial_stock[$key];
                // $purchase->warehouse = $request->warehouse[$key];
                // $purchase->price = -1;
                // $purchase->save();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()], 403);
            }
        }
        DB::commit();
        if ($request->modal_redirection) {
            return response(['message'=>'Product Added successfully', 'action'=>'dismiss','do'=>'#product', 'val'=>$product_p->id, 'text'=>$product_p->name." ".$product_p->brand, 'extra_script'=>'updateProducts']);
        }
        // Cache::forget('products');
        return response()->json(['message' => 'Product is successfully added','action'=>'redirect','do'=>url('/products')], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        if (!is_allowed('product-list')) {
            return redirect('/');
        }
        $product = Products::whereId($id)->withTrashed()->first();
        $warehouses = Warehouse::all();
        $stocks = StockManage::with('warehouse', 'order', 'sale')->where('product_id', $id)->orderBy('id', 'desc')->get();
        $batches = DB::table('inventory_view')->where('id', $id)->groupBy('batch_id')
        ->selectRaw('stockIn - stockOut as balance, batch_id')->pluck('balance', 'batch_id');
        $show_purchase_price = is_allowed('product-show-purchase-price');
        if (!$product) {
            return redirect()->back();
        }
        $can_view_purchase_price = is_allowed('product-show-purchase-price');
        if (!$can_view_purchase_price){
            $product->next_purchase_price = 0;
        }
        return view('products.show', compact('batches', 'product', 'warehouses', 'stocks', 'show_purchase_price'));
    }

    public function barcodeprint($id, Request $request)
    {
        if (!is_allowed('product-list')) {
            return redirect('/');
        }
        $product = Products::whereId($id)->withTrashed()->first();
        if (!$product) {
            return redirect()->back();
        }
        $code = @\DNS1D::getBarcodeSVG($product->barcode, "C128", 1, 40);
        return view('products.barcodeprint', compact('product', 'code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if (!is_allowed('product-edit')) {
            return redirect('/');
        }
        $product = Products::whereId($id)->withTrashed()->first();
        if (!$product) {
            return redirect()->back();
        }
        $purchasePrices = null;
        if (is_allowed('product-show-purchase-price')) {
            $purchasePrices = SupplierPriceRecord::where(['product_id' => $id])->select('id', 'date', 'supplier_id', 'price')
            ->orderBy('date', 'DESC')->get();
        }
        $suppliers = Supplier::withTrashed()->pluck('name', 'id')->toArray();
        $units = Unit::all();
        return view('products.edit', compact('product', 'purchasePrices', 'suppliers', 'units'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param Request $request
     * @return Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        if (!is_allowed('product-edit')) {
            return response(['message'=>'Unauthorized'], 500);
        }
        $update_pricing = false;
        $product = Products::findOrFail($id);
        if ($request->barcode) {
            $product->barcode = $request->barcode;
        } elseif (session()->get('settings.barcode.is_enable') && empty($product->barcode)) {
            $product->barcode = $product->id;
        }
        if ($product->salePrice != $request->sale_price) {
            $update_pricing = true;
            //////////////////////////////////////////////////////////////////////////////
            //update product group pricing when single product price
            //present in the specific group is manipulated
            $difference =  $request->sale_price - $product->salePrice;
            $prod_groups = ProductGroup::all();
            foreach($prod_groups as $value){
                foreach(unserialize($value->products) as $prod_id){
                    if($id == $prod_id){$value->price += $difference;$value->save();}
                }
            }
            /////////////////////////////////////////////////////////////////////////////
        }
        $product->salePrice = $request->sale_price?:0;
        $product->min_sale_price = $request->min_sale_price?:0;
        $product->size = $request->size;
        $product->color = $request->color;
        $product->pattern = $request->pattern;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->height = $request->height;
        $product->name = $request->name;
        $product->notify = $request->notify_quantity?:1;
        $product->unit_id = $request->unit;
        $product->brand = $request->brand;
        $product->edited_by = \Auth::id();
        $product->category_id = $request->product_category;
        $product->description = $request->description;
        $product->pct_code = $request->pct_code;
        $product->tax_rate = $request->tax_rate;
        $can_view_purchase_price = is_allowed('product-show-purchase-price');
        if ($can_view_purchase_price){
            $product->next_purchase_price = $request->next_purchase_price;
        }
        $croped_image = $request->image;
        if ($croped_image) {
            list($type, $croped_image) = explode(';', $croped_image);
            list(, $croped_image)      = explode(',', $croped_image);
            $croped_image = base64_decode($croped_image);
            $image_path = Storage::put($product->id.".png", $croped_image);
            $product->image_path = $product->id.".png";
            // $product_p->save();
        }
        if (session()->get('settings.products.enable_advance_fields')) {
            // $prod_supp = SupplierPriceRecord::where('product_id', $id)->orderBy('id','DESC')->first();
            // dd($prod_supp);
            if ($request->supplier > 0 ) {
                $supp_prod_new = new SupplierPriceRecord;
                $supp_prod_new->supplier_id = $request->supplier;
                $supp_prod_new->product_id = $id;
                $supp_prod_new->date = date('Y-m-d');
                $supp_prod_new->price = $request->purchase_price;
                $supp_prod_new->save();
            }
            try {
                if ($update_pricing) {
                    Rates::where('product_id', $product->id)->delete();
                }
                // $product->inventory->save();
            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403);
            }
        }
        
        $product->save();
        


        // Cache::forget('products');
        return response()->json(['message' => 'Product is successfully updated','action'=>'redirect','do'=>url('/products')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (!is_allowed('product-delete')) {
            return response(['message'=>'Unauthorized'], 500);
        }
        // DB::beginTransaction();
        try {
            $product = Products::whereId($id)->first();
            $product->delete();
            // DB::commit();
        } catch (\Exception $e) {
            // DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 401);
        }
        // Cache::forget('products');
        return response()->json(['message' => 'Product is successfully deleted','action'=>'redirect','do'=>url('/products')], 200);
    }

    public function outofstock()
    {
        return view('products.outofstock');
    }

    public function returnJson(Request $req)
    {
        if (isset($req->q)) {
            $tables = Products::Where('name', 'like', '%' . $req->q.'%')->get();
            $products = $tables->merge(Products::Where('barcode', 'like', '%' . $req->q.'%')->get());
        } elseif (isset($req->id)) {
            $product = Product::Where('id', '=', $req->id)->get();
            $sale_price = $product[0]->sale_price;
            return $sale_price;
        } else {
            # code...
            $products = Products::orderBy('id', 'asc')->paginate(10);
        }
        $returnArray = [];
        foreach ($products as $key => $value) {
            $returnArray[] = ['id'=>$value->id, 'text'=>$value->name ." - " .$value->barcode];
        }
        return $returnArray;
    }

    public function returnJsonCustomized(Request $req)
    {
        $prod = Products::all();
        $count = 0;
        // $prod = ;
        $page = $req->page;
        if (!$page) {
            $page = 1;
        }
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;
        // $product = array_slice($prod->toArray(), $offset, $perPage, true);
        if (isset($req->q)) {
            // $products = $prod->where('name', 'like', '%' . $req->q.'%')->orWhere('barcode', 'like', '%' . $req->q.'%')->orWhere('brand', 'like', '%' . $req->q.'%')->paginate(10);
            // $products = $tables->merge(Product::Where('barcode', 'like', '%' . $req->q.'%')->paginate(10));
            $this->q = $req->q;
            $data = $prod->toArray();
            // $query = explode(' ', $req->q);
            // $products_p = [];
            // foreach ($query as $key => $value) {
            // $this->q = $value;
            $products_p = array_where($data, function ($value, $key) {
                if (!$this->q || mb_stripos($key['name'], $this->q) !== false || mb_stripos($key['brand'], $this->q) !== false || mb_stripos($key['barcode'], $this->q) !== false || mb_stripos($key['id'], $this->q) !== false) {
                    return $key;
                }
            });
            // }
            $count = count($products_p);
            $products = array_slice($products_p, $offset, $perPage, true);
        } elseif (isset($req->id)) {
            $product = $prod->where('id', '=', $req->id)->paginate(10);
            $sale_price = $product[0]->salePrice;
            return $sale_price;
        } else {
            # code...
            // $products = $prod->all();
            // $products = Products::orderBy('id','asc')->paginate(4);
            // var_dump($prod);\
            $products = array_slice($prod->toArray(), $offset, $perPage, true);
            $count = count($prod);
        }
        $returnArray = [];

        $size_enable = strpos(session()->get('settings.products.optional_items'), 'size');

        foreach ($products as $key => $value) {
            $returnArray['items'][] = ['id'=>$value['id'], 'text'=>$value['name'] ." - " .$value['brand'].($size_enable!==false ? " - ".$value['size'] : '')];
            // $returnArray['items'][] = ['id'=>$value->id, 'text'=>$value->name ." - " .$value->brand];
        }
        $returnArray['page']=$page;
        // $returnArray['page'] = $products->currentPage();
        $returnArray['total_count']=$count;
        // $returnArray['total_count']=$products->total();
        return $returnArray;
        // return $products;
    }

    public function productPrice(Request $req)
    {
        $product = Products::whereId($req->product_id)->first();
        $customer = Customer::whereId($req->customer_id)->first();
        if (!$product) {
            return 0;
        }
        return getCustomerRate($customer, $product);
    }

    public function productBatch(Request $req)
    {
        $batches = StockManage::where('product_id', $req->product_id)
        ->whereNotNull('batch_id')
        // ->where('type', 'purchase')
        ->get();
        // ->where('type','in')->orWhere('type','purchase')->get();
        $batches = $batches->pluck('batch_id');
        return $batches;
    }

    public function productPurchasePrice(Request $req)
    {
        $product = Products::whereId($req->product_id)->first();
        $supplier = Supplier::whereId($req->customer_id)->first();
        if (!$product) {
            return 0;
        }
        if (!$supplier) {
            return $product->salePrice;
        }
        return getSupplierRate($supplier, $product);
    }

    public function returnChoiceJson(Request $req)
    {
        if (isset($req->q)) {
            $tables = Products::Where('name', 'like', '%' . $req->q.'%')->get();
            $products = $tables->merge(Products::Where('barcode', 'like', '%' . $req->q.'%')->get());
        } elseif (isset($req->id)) {
            $product = Products::Where('id', '=', $req->id)->get();
            $sale_price = $product[0]->sale_price;
            return $sale_price;
        } else {
            # code...
            $products = Products::all();
        }
        // $returnArray = (object)[];
        // $returnArray = [];
        $returnArray = [];
        foreach ($products as $key => $value) {
            if ($value->barcode) {
                $returnArray[] = ['id'=>$value->id, 'text'=>$value->name ." - " .$value->barcode];
            } else {
                $returnArray[] = ['id'=>$value->id, 'text'=>$value->name];
            }
        }
        return $returnArray;
    }

    public function suggestions(Request $req)
    {
        $prod = Products::selectRaw("id, name, barcode, brand")->get()->toArray();
        $processed = [];
        foreach ($prod as $key => $value) {
            # code...
            if (mb_stripos($value['name'], $req->q) !== false || mb_stripos($value['brand'], $req->q) || mb_stripos($value['barcode'], $req->q) !== false) {
                $processed[] = $value;
            }
            // $processed[] = [$value['id'], $value['barcode'], $value['name'], ];
        }
        // $response['draw'] = $req->draw;
        // $response['recordsFiltered'] = count($processed);
        // $response['recordsTotal']= count($prod);
        return $processed;
    }

    // public function process_json(Request $req)
    // {
    // 	\Debugbar::disable();
    // 	$prod = unserialize(Cache::get('products'));
    // 	$processor = [];
    // 	foreach ($prod as $key => $value) {
    // 		# code...
    // 		$processor[] = ["id"=>$value['id'], "text"=>$value['name']." ".$value['brand']];
    // 	}
    // 	return "var products_json_d=".json_encode($processor);
    // }

    public function update_pricing_globally(Request $request)
    {
        if (!is_allowed('product-edit')) {
            return response(['message'=>'Unauthorized'], 500);
        }
        DB::beginTransaction();
        try {
            if ($request->value > 0) {
                $rp = Rates::where('product_id', $request->product_id)->increment('salePrice', abs($request->value));
                $p = Products::whereId($request->product_id)->increment('salePrice', abs($request->value));
            } else {
                $rp = Rates::where('product_id', $request->product_id)->decrement('salePrice', abs($request->value));
                $p = Products::whereId($request->product_id)->decrement('salePrice', abs($request->value));
            }
            $pk = Products::whereId($request->product_id)->first();
            entry_price_record(date("Y-m-d"), $request->product_id, $pk->salePrice, "Sale Price Update");
            // Sale Price Update;
            DB::commit();
            // CacheController::rebuildAllCache();
            return response(["message"=>"success",'action'=>"reload"], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response(["error"=>$e->getMessage()], 500);
        }
    }

    public function datatables()
    {
        // return Datatables::of(Products::selectRaw("id, category_id, brand,status, min_sale_price, description, barcode, name, salePrice, calculate_stock(products.id) as stock, last_purchase_price(products.id) as purchase_price, notify, unit_id, edited_by, added_by")->with('unit')->with('category'))
        // ->editColumn('purchase_price',function($row){
        // 	if (!is_allowed('product-show-purchase-price'))
        // 		return 0;
        // 	return $row->purchase_price;
        // })
        // ->editColumn('salePrice',function($row){
        // 	return number_format($row->salePrice)."<small><br> Min:".number_format($row->min_sale_price)."</small>";
        // })
        // ->editColumn('stock',function($row){
        // 	$st_ht= '<a class="hover_stock '.(($row->stock < $row->notify)?'low_stock':'').'" title="--Loading Stock--" data-id="'.$row->id.'" style="cursor:pointer;">'.$row->stock.'<sup>'.$row->unit->name.'</sup></a>';
        // 	if ($row->notify >0)
        // 	{
        // 	$st_ht .=	'<br><sup>Notify: '.$row->notify.'</sup>';
        // 	}
        // 	return $st_ht;
        // })
        //  ->filterColumn('name', function($query, $keyword) {
        //  	$keyword = explode(' ', $keyword);
        //  	foreach ($keyword as $key => $value) {
        //  		$value = trim($value);
        //  		if (!$value)
        //  		{
        //  			continue;
        //  		}
        //  		$this->search_val = $value;
        //  		 $query->where(function ($sub_query) {
        //  			$sub_query->where('name','LIKE','%'.$this->search_val.'%')
        //  			->orWhere('brand','LIKE','%'.$this->search_val.'%')
        //  			->orWhere('barcode','LIKE','%'.$this->search_val.'%');
        //            });
        //  		// });
        //  	}
        // 	 //href="'. url('products').'/'. $row->id.'/switch' .'"
        //     // $query->whereIn('name', $keyword)->orWhereIn('brand',$keyword)->orWhereIn('barcode',$keyword);
        // })
        // ->addColumn("options",function($row){
        // 	if($row->status == true)
        // 	{
        // 		$statuss = "Active";
        // 		$classs = '<li><a class="btn btn-xs btn-success switchStatus" onclick="switchStatus(this,'.$row->id.')" >'.$statuss.'</a></li>';
        // 	}
        // 	else{
        // 		$statuss = "In-Active";
        // 		$classs = '<li><a class="btn btn-xs btn-danger switchStatus" onclick="switchStatus(this,'.$row->id.')" >'.$statuss.'</a></li>';
        $can_view_purchase_price = is_allowed('product-show-purchase-price');
        \Log::info($can_view_purchase_price);
        return Datatables::of(Products::selectRaw("id,landing_cost,next_purchase_price, image_path,brand, size, color, pattern, length, width, height, status,min_sale_price, barcode, itemcode, name, description, salePrice, calculate_stock(products.id) as stock, last_purchase_price(products.id) as purchase_price, notify, unit_id,category_id, edited_by, added_by")->with('unit')->with('category'))
            ->editColumn('purchase_price', function ($row) use ($can_view_purchase_price) {
                if (!$can_view_purchase_price) {
                    return 0;
                }
                return $row->purchase_price + 0;
            })->editColumn('salePrice', function ($row) {
                return $row->salePrice + 0;
            })->editColumn('next_purchase_price', function ($row)  use ($can_view_purchase_price) {
                \Log::info($can_view_purchase_price);
                if (!$can_view_purchase_price) {
                    return 0;
                }
                return $row->next_purchase_price + 0;
            })->editColumn('itemcode', function ($row) {
                return $row->itemcode;
            })->editColumn('category', function ($row) {
                return $row->category->name;
            })->editColumn('min_sale_price', function ($row) {
                return $row->min_sale_price + 0;
            })->editColumn('landing_cost', function ($row) {
                return $row->landing_cost + 0;
            })
            ->editColumn('stock', function ($row) {
                $st_ht= '<a class="hover_stock '.(($row->stock < $row->notify)?'low_stock':'').'" title="--Loading Stock--" data-id="'.$row->id.'" style="cursor:pointer;">'.($row->stock + 0).'<sup> '.$row->unit->name.'</sup></a>';
                if ($row->notify >0) {
                    $st_ht .=	'<br><sup>Notify: '.$row->notify.'</sup>';
                }
                return $st_ht;
            })->filterColumn('name', function ($query, $keyword) {
                $keyword = explode(' ', $keyword);
                foreach ($keyword as $key => $value) {
                    $value = trim($value);
                    if (!$value) {
                        continue;
                    }
                    $this->search_val = $value;
                    $query->where(function ($sub_query) {
                        $sub_query->where('name', 'LIKE', '%'.$this->search_val.'%')
                        ->orWhere('brand', 'LIKE', '%'.$this->search_val.'%')
                        ->orWhere('barcode', 'LIKE', '%'.$this->search_val.'%');
                    });
                    // });
                }
                //href="'. url('products').'/'. $row->id.'/switch' .'"
                // $query->whereIn('name', $keyword)->orWhereIn('brand',$keyword)->orWhereIn('barcode',$keyword);
            })->addColumn('image', function ($row) {
                if (!session()->get('settings.products.is_image_enable') || !$row->image_path) {
                    return "-";
                }
                return "<image width='100px' src='data:image/jpeg;base64,".base64_encode(file_get_contents(storage_path("app/public/".$row->image_path)))."' />";
            })->addColumn("options", function ($row) {
                if ($row->status == true) {
                    $statuss = "Active";
                    $classs = '<li><a class="btn btn-xs btn-success switchStatus" onclick="switchStatus(this,'.$row->id.')" >'.$statuss.'</a></li>';
                } else {
                    $statuss = "In-Active";
                    $classs = '<li><a class="btn btn-xs btn-danger switchStatus" onclick="switchStatus(this,'.$row->id.')" >'.$statuss.'</a></li>';
                }
                if (session()->get('settings.barcode.is_enable')) {
                    $classs .= "<li><a class='btn btn-xs btn-success' href='".url('barcode_print/'.$row->id) ."' target='_blank'><i class='glyphicon glyphicon-print'></i> Barcode</a></li>";
                }
                return ' <ul class="list-inline">
			<li><a class="btn btn-xs btn-success" onclick="displaypricing(this)" data-id="'.$row->id.'" data-pricing="'.$row->salePrice.'">Pricing Update</a></li>
    	 	<li><a class="btn btn-xs btn-default" href="'.url('supplier_reporting/product_record').'?product_id='.$row->id.'">View Suppliers</a></li>
    	 	<li><a class="btn btn-xs btn-primary" href="'. url('products').'/'. $row->id .'"><i class="glyphicon glyphicon-eye-open"></i> In/Out Log</a></li>
    	 	<li><a class="btn btn-xs btn-warning" href="'. url('products').'/'. $row->id.'/edit' .'"><i class="glyphicon glyphicon-edit"></i> Edit</a></li>
    	 	<li><form action="'. url('products').'/'. $row->id .'" method="POST" style="display: inline;"><div id="log"></div><input type="hidden" name="_method" value="DELETE"><input type="hidden" name="_token" value="'. csrf_token() .'"> <button type="submit" class="btn btn-xs btn-danger delete_the_product"><i class="glyphicon glyphicon-trash"></i> Delete</button></form></li>
			.'.$classs.'</ul>';
            })->add_column('added_by', function ($row) {
                return ($row->added_user)?$row->added_user->name:"-";
            })->add_column('updated_by', function ($row) {
                return ($row->edited_user)?$row->edited_user->name:"-";
            })->make(true);
    }


    

    public function process_json(Request $req)
    {
        \Debugbar::disable();
        $prod = Products::where('status', 1)->get();
        $processor = [];
        foreach ($prod as $key => $value) {
            $name = "{$value->name} {$value->brand} {$value->urdu}";
            if (session()->get('settings.barcode.is_enable')) {
                $name = "{$value->barcode} {$name}";
            }
            // $name = $value->name." ".$value->brand." ".$value->urdu;
            $unit_name = Unit::whereId($value->unit_id)->pluck('name')->first();
            if (!empty(session()->get('settings.products.optional_items'))) {
                $fields = explode(",", session()->get('settings.products.optional_items'));
                foreach ($fields as $field) {
                    if (!empty($value->$field) && strpos(session()->get('settings.products.optional_items'), $field) !== false) {
                        $name .= " " . (($field == 'category')?$value->$field->name:$value->$field);
                    }
                }
            }
            $processor[] = [
                "id" => $value->id,
                "text" => $name,
                "barcode" => " ". $value->barcode,
                "price" => $value->salePrice,
                "notify" => $value->notify,
                "unit" => $unit_name,
                "min_sale_price" => $value->min_sale_price
            ];
        }
        return "var products_json_d=".json_encode($processor);
    }


    public function products_full(Request $req)
    {
        \Debugbar::disable();
        $prod = ProductView::all();
        $processor = [];
        $customer = false;
        if ($req->customer_id) {
            $customer = Customer::whereId($req->customer_id)->first();
        }
        foreach ($prod as $key => $value) {
            // id, name, brand, quantity, price, barcode
            $processor[] = [
                "id"=>$value->id,
                "name"=>$value->name,
                "brand"=>$value->brand,
                "barcode"=>$value->barcode,
                "notify"=>$value->notify,
                "urdu"=>$value->urdu,
                "price"=>getCustomerRate($customer, $value),
                "stock"=>$value->stock,
                "purchase"=>(is_allowed('product-show-purchase-price'))? 0 : $value->purchase_price,
                "unit"=>$value->unit_name];
        }
        return "var products_json_d=".json_encode($processor);
    }

    public function downloadExcel()
    {
        if (!is_allowed('product-import-export')) {
            return redirect('/');
        }
        //  	//password protected
        // $validated = is_password_validated();

        // 	if (!$validated) {
        // 	  header('WWW-Authenticate: Basic realm="My Realm"');
        // 	  header('HTTP/1.0 401 Unauthorized');
        // 	  die ("Not authorized");
        // 	}
        // 	//password protected
        // dd();
        $order_by = env('ORDER_BY_NAME') ? : 'id';
        $sheet_records = collect(Products::leftJoin('units', 'units.id', '=', 'products.unit_id')->leftJoin('supplier_price_records' , 'supplier_price_records.product_id', '=', 'products.id')->leftJoin('product_categories' , 'product_categories.id', '=', 'products.category_id')
        ->selectRaw("products.id, products.name, barcode, brand, size, color, pattern, length, width, height, status, min_sale_price, calculate_stock(products.id) as qty, notify, TRIM(salePrice)+0 as sale_price, supplier_price_records.price as purchase_price , product_categories.name as category, units.name as unit_name")->orderBy('supplier_price_records.date', 'DESC')->get());
        $this->sheetX = $sheet_records->unique('id')->sortBy($order_by)->toArray();

        foreach ($this->sheetX as $key => $value) {
            $this->sheetX[$key]['delete']="";
        }
        @\Excel::create('Products', function ($excel) {
            $excel->sheet('Products', function ($sheet) {
                $sheet->fromArray($this->sheetX);
            });
        })->export('xlsx');
    }


    public function uploadExcel(Request $request)
    {
        if (!is_allowed('product-import-export')) {
            return redirect('/');
        }
        $this->product_count = 0;
        $p = Storage::put(
            'importexcel.xlsx',
            file_get_contents($request->file('importexcel')->getRealPath())
        );
        DB::beginTransaction();
        try {
            $this::importExcel(storage_path("app/public/importexcel.xlsx"));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect("products")->with('error', 'Unable to Import: '.$e->getMessage());
        }
        // Cache::forget('products');
        return redirect('products')->with('message', 'Succesfully '.$this->product_count.' Imported');
    }


    private function importExcel($path)
    {
        @\Excel::load($path, function ($reader) {
            // Loop through all sheets
            $reader->each(function ($sheet) {
                // dd($sheet);
                if ($sheet->delete && $sheet->delete == "1") {
                    $product = Products::whereId($sheet->id)->delete();
                } elseif ($sheet->id) {
                    $pr = Products::withTrashed()->firstOrNew(['id'=>$sheet->id]);
                    $pr->name = $sheet->name;
                    $pr->brand = ($sheet->brand) ? : null;
                    $pr->landing_cost = ($sheet->landing_cost) ? : null;
                    $pr->size = ($sheet->size) ? : null;
                    $pr->color = ($sheet->color) ? : null;
                    $pr->pattern = ($sheet->pattern) ? : null;
                    $pr->length = ($sheet->length) ? : null;
                    $pr->width = ($sheet->width) ? : null;
                    $pr->height = ($sheet->height) ? : null;
                    $pr->barcode = ($sheet->barcode) ? : null;
                    $pr->description = ($sheet->description) ? : null;
                    $pr->min_sale_price = ($sheet->min_sale_price) ? : null;
                    $pr->category_id = ($sheet->category) ? : 1 ;
                    $pr->notify = ($sheet->notify) ? : 0 ;
                    $pr->salePrice = ($sheet->sale_price) ? : 0 ;
                    if (!$sheet->unit_name) {
                        $sheet->unit_name = "pcs";
                    }
                    $unit = Unit::firstOrNew(['name'=>$sheet->unit_name]);
                    $unit->save();
                    $pr->unit_id = $unit->id;
                    if (empty($pr->added_by)) {
                        $pr->added_by = \Auth::id();
                    } else {
                        $pr->edited_by = \Auth::id();
                    }
                    $pr->save();
                    if (!empty($sheet->supplier) && !empty($sheet->purchase_price)) {
                        $supplier_price = new SupplierPriceRecord;
                        $supplier_price->date = date('Y-m-d');
                        $supplier_price->product_id = $sheet->id;
                        $supplier_price->supplier_id = $sheet->supplier;
                        $supplier_price->price = $sheet->purchase_price;
                        $supplier_price->save();

                        if (!empty($sheet->quantity) && !empty($sheet->warehouse)) {
                            $stock_m = new StockManage;
                            $stock_m->date = date('Y-m-d');
                            $stock_m->product_id = $sheet->id;
                            $stock_m->supplier_id = $sheet->supplier;
                            $stock_m->warehouse_id = $sheet->warehouse;
                            $stock_m->type = ($sheet->quantity > 0) ? 'in' : 'out';
                            $stock_m->quantity = abs($sheet->quantity);
                            $stock_m->save();
                        }
                    }
                    $this->product_count++;
                }
            });
        })->get();
    }


    public function productWarehouseStock(Request $request)
    {
        \Debugbar::disable();
        $warehouses= Warehouse::all();
        $product= Products::whereId($request->id)->first();
        $html = '<ul class="list-group list-group-flush" style="font-size: 15px">';

        $no_stock = "--No Inventory --";
        foreach ($warehouses as $warehouse) {
            $st = warehouse_stock($product, $warehouse->id);
            if ($st) {
                $no_stock ="";
                $html .= '<li class="list-group-item"><b>'.$warehouse->name.':</b> <span style="'.(($st < 0)?'color:red':'').'">'.$st.'</span></li>';
            }
        }
        return $html .$no_stock. '</ul>';
    }

    public function switchStatus($id)
    {
        $prod = Products::where('id', $id)->first();
        $prod->status = !$prod->status;
        $prod->save();
        return response()->json(['success'=>'successfully updated']);
    }
    public function minimumSalePrice(Request $req)
    {
        $prod = Products::where('id', $req->product_id)->first();
        return $prod->min_sale_price + 0;
    }
}
