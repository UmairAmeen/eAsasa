<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\Invoice;
use App\Transaction;
use App\StockManage;
use App\MyPDF;
use App\Setting;
use \NumberFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Exception;
// use Schema;

class InvoiceController extends Controller
{
    private $items_per_invoice;

    public function __construct()
    {
        View::share('title', "Invoice");
        $this->items_per_invoice = session()->get('settings.sales.items_per_page');
        if ($this->items_per_invoice > Setting::MAX_ITEMS_PER_INVOICE) {
            $this->items_per_invoice = Setting::MAX_ITEMS_PER_INVOICE;
        } elseif ($this->items_per_invoice < Setting::MIN_ITEMS_PER_INVOICE || empty($this->items_per_invoice)) {
            $this->items_per_invoice = Setting::MIN_ITEMS_PER_INVOICE;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $invoices = Invoice::orderBy('id', 'desc')->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('invoices.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $invoice = new Invoice();
        $invoice->save();
        return redirect()->route('invoices.index')->with('message', 'Item created successfully.');
    }

    public function smallInvoice($id)
    {
        $transaction_cash = Transaction::where('invoice_id', $id)->where('payment_type', '=', 'cash')->where('type', 'in')->first();
        $transaction_card = Transaction::where('invoice_id', $id)->where('payment_type', '!=', 'cash')->where('type', 'in')->first();
        $invoice = Invoice::findOrFail($id);
        if (!is_allowed('product-show-purchase-price') && $invoice->type == 'purchase') {
            return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }
        // return view('invoices.small_invoice',compact('invoice'));
        if (session()->get('settings.misc.eng_urdu')) {
            return view('invoices.rtl_small_invoice', compact('invoice', 'transaction_cash', 'transaction_card'));
        } else {
            return view('invoices.small_invoice', compact('invoice', 'transaction_cash', 'transaction_card'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function purchaseInvoice($id, $is_purchase = 0)
    {
        $this->show($id, $is_purchase);
    }

    public function show($id, $is_purchase = 0)
    {
        $image_enable = 0;
        if ($is_purchase) {
            $image_enable = session()->get('settings.sales.is_image_enable_in_purchase_order')?:0;
        } else {
            $image_enable = session()->get('settings.sales.is_image_enable_in_sale_invoice');
        }
        $terms_on_back = session()->get('settings.sales.terms_on_back')?:0;
        $terms_background = storage_path('app/public/terms_invoice.jpg');
        $seprate_prod_fields = session()->get('settings.products.seprate_prod_fields')?:0;
        // if($image_enable == 1){$this->items_per_invoice = 8;}

        $total = 0;
        $min_price_discount = 0;
        $items = $summary =[];
        $invoice = Invoice::findOrFail($id);
        if (!is_allowed('product-show-purchase-price') && $invoice->type == 'purchase') {
            return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }
        $out_transaction = Transaction::select('payment_type', 'bank', 'transaction_id', 'amount')
            ->where(['type' => 'out', 'invoice_id' => $invoice->id])
            ->orderBy('id', 'asc')
            ->first();
        $transaction = Transaction::select('payment_type', 'bank', 'transaction_id', 'amount')
            ->where(['type' => 'in', 'invoice_id' => $invoice->id])
            ->orderBy('id', 'asc')
            ->first();
        if ($invoice->customer) {
            $user = $invoice->customer;
        } else {
            $user = $invoice->supplier;
        }
        $manual_title = ($invoice->sale_order->status == 3) ? "QUOTATION" : "INVOICE";
        $background_image_select = ($manual_title == 'QUOTATION') ? 'invoice_quotation.jpg' : (($is_purchase == 1) ? 'purchase_invoice_bg.jpg':'invoice_bg.jpg');
        $background = session()->get('settings.misc.custom_header_footer') ? false : storage_path('app/public/'.$background_image_select.'');
        $pdf = @new MyPDF($title = $manual_title, $background);
        $pdf->addPage();
        $pdf->SetFont(PDF_FONT_NAME_MAIN, '', 10);
        $pdf->SetY(intval(session()->get('settings.misc.content_position') * 2));
        if (session()->get('settings.misc.eng_urdu')) {
            $invoice_top = view('invoices.print.rtl_default_invoice_top', compact('id', 'user', 'invoice', 'is_purchase'));
        } else {
            $invoice_top = view('invoices.print.default_invoice_top', compact('id', 'user', 'invoice', 'is_purchase'));
        }
        
        $pdf->writeHTML($invoice_top);

        foreach ($invoice->orders as $key => $value) {
            $arr_key = (session('settings.misc.item_combine', true)?$value->product_id:$key);
           
            $name = "";// $value->product->name . " ". $value->product->brand." ".$value->product->itemcode;
            if (env('SHOES_COMPANY') != 1 && $seprate_prod_fields != 1) {
                if (!empty(session()->get('settings.products.invoice_fields'))) {
                    $fields = explode(",", session()->get('settings.products.invoice_fields'));
                    foreach ($fields as $field) {
                        if (!empty($value->product->$field) && strpos(session()->get('settings.products.invoice_fields'), $field) !== false) {
                            $name .= " " . (($field == 'category')?$value->product->$field->name:$value->product->$field);
                        }
                    }
                }
            } else {
                $name = $value->product->name;
            }
            $items[$arr_key]['name'] = ltrim($name .($value->note ?  ' ('. $value->note.')' : ''));
            if (!array_key_exists('quantity', $items[$arr_key])) {
                $items[$arr_key]['quantity'] = 0;
            }
            $items[$arr_key]['quantity'] += $value->quantity;
            if (session()->get('settings.products.is_image_enable')) {
                if (file_exists(storage_path('app/public/'.$value->product->image_path.''))) {
                    $items[$arr_key]['image'] = $value->product->image_path;
                }
            }
            $items[$arr_key]['category'] = $value->product->category->name;
            $items[$arr_key]['description'] = $value->product->description;
            $items[$arr_key]['unit'] = $value->product->unit->name;
            $items[$arr_key]['size'] = $value->product->size;
            $items[$arr_key]['color'] = $value->product->color;
            $items[$arr_key]['min_sale_price'] = $value->original_price;
            $items[$arr_key]['sale_price'] = $value->salePrice + 0;
            $items[$arr_key]['brand'] = $value->brand;
            $items[$arr_key]['full'] = $value;
            if ($items[$arr_key]['quantity'] > 0) {
                $return = false;
            }
            // if(auth()->user()->fixed_discount == 1 && $invoice->type != 'purchase'){
            //     $total += $value->quantity * $value->product->min_sale_price;
            // }
            // else{
                $total += $value->quantity * $value->salePrice;
            // }
            $min_price_discount += ($value->original_price - $value->salePrice)*$value->quantity;

        }

        if ($invoice->is_manual && !empty($out_transaction->amount)) {
            $total = $out_transaction->amount - $invoice->tax;
        }
        // dd($items);
        if (session()->get('settings.sales.show_empty_rows')) {
            while (count($items) > 0 && (count($items) % $this->items_per_invoice) != 0) {
                array_push($items, ['name' => '', 'quantity' => '', 'sale_price' => '']);
            }
        }
        $paginated_items = array_chunk($items, $this->items_per_invoice, true);

        foreach ($paginated_items as $page => $items) {
            $serial_no = $page * $this->items_per_invoice;
            if (session()->get('settings.misc.eng_urdu')) {
                $contents = view('invoices.print.rtl_default_invoice', compact('image_enable', 'invoice', 'is_purchase', 'items', 'serial_no'));
            } else {
                $contents = view('invoices.print.default_invoice', compact('image_enable', 'invoice', 'is_purchase', 'items', 'serial_no'));
            }
            if ($page) {
                $pdf->SetY(intval(session()->get('settings.misc.content_position') * 3));
            }
            $pdf->writeHTML($contents);

            if (count($paginated_items) > 1 && $page < count($paginated_items) -1) {
                if($is_purchase != 1 && $terms_on_back == 1)
                {
                    $pdf->AddPage();
                    if(file_exists($terms_background))
                    {
                        // -- set terms page background ---
                        termsBackground($pdf,$terms_background);
                    }
                    $pdf->SetY(36);
                    $contents = view('invoices.print.terms', compact('terms_background'));
                    $pdf->writeHTML($contents);
                }
                elseif($is_purchase != 1 && $terms_on_back == 2 && $page == 0)
                {
                    $pdf->AddPage();
                    if(file_exists($terms_background))
                    {
                        // -- set terms page background ---
                        termsBackground($pdf,$terms_background);
                    }
                    $pdf->SetY(36);
                    $contents = view('invoices.print.terms', compact('terms_background'));
                    $pdf->writeHTML($contents);
                }
                $pdf->AddPage();
                $pdf->SetY(36);
            }
        }
        
        $summary['Total'] = abs($total+(($min_price_discount > 0)?$min_price_discount:0));
        // $summary['related_to'] = $invoice->related_to;
        if ($invoice->shipping) {
            //Environment variable set to true for RAZA TRACTOR client.
            //This variable is only added to the .env file of relevant instance.
            //For all other instances it will be null to implement generic code.
            //note: "This change eliminates the need to add a particular variable
            //in all instances to cater this change of a particular client".
            if (!env('RAZA')) {
                $summary['Transportation'] = $invoice->shipping;
            } else {
                $summary['Packaging'] = $invoice->shipping;
            }
        }
        if (($invoice->discount && $invoice->discount > 0) || ($min_price_discount && $min_price_discount > 0)) {
            $summary['Discount'] = $invoice->discount + ($min_price_discount > 0?$min_price_discount:'0');
        }
        if ($invoice->tax > 0) {
            $summary['Tax'] = $invoice->tax;
        }

        $summary['Grand Total'] = abs($total + $invoice->shipping - $invoice->discount+$invoice->tax);
        $payment = payment_for_invoice($invoice->id);
        if ($invoice->customer) {
            //Environment variable set to true for RAZA TRACTOR client.
            //This variable is only added to the .env file of relevant instance.
            //For all other instances it will be null to implement generic code.
            //note: "This change eliminates the need to add a particular variable
            //in all instances to cater this change of a particular client".

            if (!env('RAZA')) {
                if ($payment) {
                    $summary['Advance'] = first_payment_for_invoice($invoice->id);
                    $summary['Total Amount Paid'] = $payment;
                    $summary['Balance'] = abs($total + $invoice->tax + $invoice->shipping - $invoice->discount) - abs($payment);
                } else {
                    $summary['Balance'] = $total + $invoice->shipping - $invoice->discount + $invoice->tax;
                }
                if (session()->get('settings.sales.show_customer_previous_balance')) {
                    $pb = getCustomerBalance($invoice->customer_id);
                    $summary['Previous Balance'] = $pb - ($total + $invoice->shipping - $invoice->discount) + $payment;
                    // start => as per client previous balance should not be visible in negative value
                    if ($summary['Previous Balance'] < 0) {
                        $summary['Previous Balance'] = 0;
                    }
                    // => end
                    $summary['Total due'] = $pb;
                } else {
                    // $summary['Balance'] = getCustomerBalance($invoice->customer_id);
                }
            }
        }
        foreach($invoice->orders as $orders)
        {
            $sum_quantity += $orders->quantity;
        }
        $number = $summary['Grand Total'];
        $locale = 'en_US';
        $fmt = numfmt_create($locale, NumberFormatter::SPELLOUT);
        $amount_in_words = numfmt_format($fmt, $number);

        $summary['totalQuantity'] 	= $sum_quantity;
        $summary['amountInWords'] 	= $amount_in_words;

				$contents = null;
        if (session()->get('settings.misc.eng_urdu')) {
            $total_key = 'ٹوٹل';
            $advance_key = 'ایڈوانس';
            $grand_total_key = '`گرینڈ ٹوٹل';
        } else {
            $total_key = 'Total';
            $advance_key = 'Advance';
            $grand_total_key = 'Grand Total';
        }

        $summary_detail[$total_key] 		= $summary['Total'];
        $summary_detail['Transportation'] 	= ($summary['Transportation'] != 0) ? $summary['Transportation'] : '';
        $summary_detail['Packaging'] 		= ($summary['Packaging'] != 0) ? $summary['Packaging'] : '';
        $summary_detail['Discount'] 		= ($summary['Discount'] != 0) ? $summary['Discount'] : '';
        $summary_detail['Tax'] 				= ($summary['Tax'] != 0) ? $summary['Tax'] : '';
        $summary_detail[$grand_total_key] 	= $summary['Grand Total'];
        $summary_detail[$advance_key] 		= ($summary['Advance'] != 0) ? $summary['Advance'] : '';
        $summary_detail['Total Amount Paid'] = ($summary['Total Amount Paid'] != 0) ? $summary['Total Amount Paid'] : '0';
        $summary_detail['Balance'] 			= ($summary['Balance'] != 0) ? $summary['Balance'] : '0';
        $summary_detail['Previous Balance'] = ($summary['Previous Balance'] != 0) ? $summary['Previous Balance'] : '';
        $summary_detail['Total due'] 		= ($summary['Total due'] != 0) ? $summary['Total due'] : '';
        if (session()->get('settings.misc.eng_urdu')) {
					$contents = view('invoices.print.rtl_default_invoice_summary', compact('is_purchase', 'id', 'user', 'items', 'summary', 'summary_detail', 'invoice', 'transaction'));
				}else{
					$contents = view('invoices.print.default_invoice_summary', compact('is_purchase', 'id', 'user', 'items', 'summary', 'summary_detail', 'invoice', 'transaction'));
				}

        $pdf->writeHTML($contents);
        if($is_purchase != 1 && ($terms_on_back == 1 || $terms_on_back == 3))
            {
                $pdf->AddPage();
                if(file_exists($terms_background))
                    {
                        // -- set terms page background ---
                        termsBackground($pdf,$terms_background);
                    }
                $pdf->SetY(36);
                $contents = view('invoices.print.terms', compact('terms_background'));
                $pdf->writeHTML($contents);
            }
        elseif($is_purchase != 1 && $terms_on_back == 2 && count($paginated_items) <= 1)
                {
                    $pdf->AddPage();
                    if(file_exists($terms_background))
                    {
                        // -- set terms page background ---
                        termsBackground($pdf,$terms_background);
                    }
                    $pdf->SetY(36);
                    $contents = view('invoices.print.terms', compact('terms_background'));
                    $pdf->writeHTML($contents);
                }
        if (session()->get('settings.sales.is_invoice_qr_enable')) {
            $style = array(
                'vpadding' => 'auto',
                'hpadding' => 'auto',
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
            );
            $pdf->write2DBarcode($id, 'QRCODE,Q', 20, 230, 20, 20, $style, 'N');
        }
        $pdf->Output('invoice'.$invoice->id.'.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);

        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        

        $invoice->save();

        return redirect()->route('invoices.index')->with('message', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Schema::disableForeignKeyConstraints();
            $invoice = Invoice::whereId($id)->first();
            if (!$invoice) {
                throw new Exception("Error Processing Request", 1);
            }
            Transaction::where('invoice_id', $id)->delete();
            $orderIds = Order::where('invoice_id', $invoice->id)->pluck('id')->toArray();
            Order::where('invoice_id', $id)->delete();
            StockManage::whereIn('sale_id', $orderIds)->orWhereIn('purchase_id', $orderIds)->delete();
            $invoice->delete();
            // Schema::enableForeignKeyConstraints();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 401);
        }
        // Cache::forget('products');
        return response()->json(['message' => 'Invoice is successfully deleted', 'action'=>'reload'], 200);
    }
}
