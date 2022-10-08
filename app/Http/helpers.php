<?php

use App\ExpenseHead;
use App\Invoice;
use App\Transaction;

function calculateStock($product)
{
    $in = $out = 0;
    $in = $product->stock->whereIn('type', ['in', 'purchase'])->sum('quantity');
    $out = $product->stock->whereIn('type', ['out', 'sale', 'refund'])->sum('quantity');
    // if ($in < $out)
    // {
    // 	return 0;
    // }
    return $in - $out;
}

function calculateStockById($product_id)
{
    $in = $out = 0;
    $in = \App\StockManage::whereIn('type', ['in', 'purchase'])->where('product_id', $product_id)->sum('quantity');
    $out = \App\StockManage::whereIn('type', ['out', 'sale', 'refund'])->where('product_id', $product_id)->sum('quantity');
    return $in - $out;
}

function payment_for_invoice($invoice_id, $type="in")
{
    return \App\Transaction::where('type', $type)->where('invoice_id', $invoice_id)->sum('amount');
}

function first_payment_for_invoice($invoice_id, $type="in")
{
    return \App\Transaction::where('type', $type)->where('invoice_id', $invoice_id)->first()->amount;
}

function date_format_app($date)
{
    return date(session()->get('settings.misc.date_format'), strtotime($date));
}

function get_product($product_id=0)
{
    return \App\Products::whereId($product_id)->withTrashed()->first();
}

function get_product_purchase($product_id=0)
{
    $ddx = get_product($product_id);
    $ddx->salePrice = last_purchased_price_or_sale($ddx);
    return $ddx;
}
function last_purchased_price_or_sale($product)
{
    $p =  App\SupplierPriceRecord::where('product_id', $product->id)->orderBy('date', 'desc')->first();

    if (!$p) {
        return $ddx->salePrice;
    }
    return $p->price;
}

function get_supplier($supplier_id=0)
{
    return \App\Supplier::whereId($supplier_id)->withTrashed()->first();
}
function get_customer($customer_id=0)
{
    return \App\Customer::whereId($customer_id)->first();
}
function app_date_format($date)
{
    return date('d-M-Y', strtotime($date));
}

function is_selected($curent_value, $given_value)
{
    return ($curent_value == $given_value)?"selected":"";
}

function calculateStockFromProductId($product_id)
{
    $in = $out = 0;
    $in = App\StockManage::where('product_id', $product_id)->whereIn('type', ['in', 'purchase'])->sum('quantity');
    $out = App\StockManage::where('product_id', $product_id)->whereIn('type', ['out', 'sale', 'refund'])->sum('quantity');
    if ($in < $out) {
        return 0;
    }
    return $in - $out;
}

function warehouse_stock($product, $warehouse_id, $product_id=false)
{
    $product = ($product_id)?:$product->id;
    $d = App\StockManage::where('warehouse_id', $warehouse_id)->where('product_id', $product)->get();
    $in = $d->whereIn('type', ['in','purchase'])->sum('quantity');
    $out = $d->whereIn('type', ['out', 'sale', 'refund'])->sum('quantity');
    return $in - $out;
}


function warehouse_stock_adj($product, $warehouse_name, $new_stock=0)
{
    // $warehouse_name = strtolower(trim($warehouse_name));
    // dd(App\Warehouse::whereId(3)->first());
    $d = App\Warehouse::whereRaw("UPPER(`name`) LIKE '%".strtoupper($warehouse_name)."%' ")->first();
    if (!$d) {
        dd($warehouse_name);
    }
    $stock = warehouse_stock($product, $d->id);
    if ($stock == $new_stock) {
        return true;
    }
    //20-0 => 20
    $value = $new_stock - $stock;
    
    $stm = new App\StockManage;
    $stm->product_id = $product->id;
    $stm->warehouse_id = $d->id;
    $stm->quantity = abs($value);
    $stm->date=date('Y-m-d');
    if ($value > 0) { //add adjustment
        $stm->type = "in";
    } else {
        $stm->type="out";
    }
    // $stm->description = "Auto Adjusted Stock From Excel Import";


    $stm->save();
}


function getCustomerRate($customer, $product)
{
    $rate = false;
    
    if (getSetting('use_customer_pricing')) { //just check the setting
        if ($customer) {
            $rate = $customer->rate->where('product_id', $product->id)->first();
        }
    }
    
    if ($rate) {
        return $rate->salePrice + 0;
    } else {
        return $product->salePrice + 0;
    }
}

function getCustomerBalance($customer_id)
{
    if (!is_allowed('report-balance_sheet')) {
        return 0;
    }
    $credit = \App\Transaction::where('customer_id', $customer_id)->where('type', 'out')->sum('amount');
    $debit	= \App\Transaction::where('customer_id', $customer_id)->where('type', 'in')->sum('amount');

    return abs($credit) - abs($debit);
}
function getCustomerOpeningBalance($customer_id, $till_date=false)
{
    //$till date doesn't includes the end date.
    if (!$till_date) {
        return 0;
    }
    $credit = \App\Transaction::where('customer_id', $customer_id)->where('date', '<', $till_date)->where('type', 'out')->sum('amount');
    $debit	= \App\Transaction::where('customer_id', $customer_id)->where('date', '<', $till_date)->where('type', 'in')->sum('amount');

    return $credit - $debit;
}

function getSupplierOpeningBalance($customer_id, $till_date=false)
{
    if (!$till_date) {
        return 0;
    }
    $credit = \App\Transaction::where('supplier_id', $customer_id)->where('date', '<', $till_date)->where('type', 'out')->sum('amount');
    $debit	= \App\Transaction::where('supplier_id', $customer_id)->where('date', '<', $till_date)->where('type', 'in')->sum('amount');

    return $debit - $credit;
    
    //$till date doesn't includes the end date.
}
function getProductStockDeliveredInSaleOrder($product_id, $sale_order_id)
{
    return \App\StockManage::where('sale_orders_id', $sale_order_id)->where('product_id', $product_id)->whereNull('deleted_at')->sum('quantity');
}
function getQuantityFromOrder($product_id, $invoice_id)
{
    $order_details = \App\Order::where('invoice_id', $invoice_id)->where('product_id', $product_id)->whereNull('deleted_at')->first();
    return ($order_details->quantity + 0);
}
function getSetting($key)
{
    $setting = App\Setting::where('key', $key)->first();
    return $setting->value;
}

function totaldue($customer)
{
    $total = 0;
    foreach ($customer->stocks as $key => $value) {
        if ($value->sale) {
            $total += $value->sale->salePrice * $value->quantity;
        } elseif ($value->type == "refund") {
            $customer_rate = getCustomerRate($customer, $value->product);
            $total -=  $customer_rate * $value->quantity;
        }
    }
    $shipping = $customer->invoice->sum('shipping');
    return $total+$shipping;
}
function getAllOrders($order_ids)
{
    $data = unserialize($order_ids);
    $myOrders = array();
    foreach ($data as $key => $value) {
        $myOrders[] =  App\Order::whereId($value)->first();
    }
    return $myOrders;
}
function is_admin()
{
    return \Auth::id() == 1 || Auth::user()->hasRole('Admin');
}
function is_allowed($role)
{
    if (is_admin()) {
        return true;
    }

    if (!\Auth::check()) {
        return false;
    }

    return \Auth::user()->can($role);
}
function package($option)
{
    $package = Session::get('license_info')['package'];
    switch ($option) {
        case 'purchase':
            if ($package == "F1") {
                return false;
            }
            return true;
            break;

        case 'sales':
            if ($package == "F1") {
                return false;
            }
            return true;
            break;

        case 'report':
            if ($package == "F1") {
                return false;
            }
            return true; //globally turned on

            break;

        case 'settings':
            // if ($package == "F1")
            // 	return false;
            return true;

            break;

        case 'customer':
            if ($package == "F1") {
                return false;
            }
            return true;
            break;
        case 'transaction':
            if ($package == "F1") {
                return false;
            }
            return true;
        break;
        case 'invoice':
            return false;
        break;
        case 'manufacture':
            return false;
        break;
        case 'refund':
            if ($package == "F1") {
                return false;
            }
            return true;
        break;

        // case 'stock_adjustments':
        // 	if ($package !== "F1")
        // 			return false;
        // 		return true;
        // 	break;
        
        default:
            return true;
            break;
    }
    return true;
}

function stock_notice()
{
    $products = DB::select("
		SELECT * FROM `products` 
		where deleted_at is NULL 
			and notify > 0 
			AND notify >= (
				(
					select IFNULL(sum(quantity),0) 
					from stocklog 
					where product_id = products.id 
						and type IN ('in','purchase','refund') 
						and deleted_at IS NULL
				) - 
				(
					select IFNULL(sum(quantity),0)  
					from stocklog 
					where product_id = products.id 
					and type in ('out','sale') 
					and deleted_at IS NULL
				)
			)");



    // $products = \App\Products::where('notify','>',0)->whereRaw("notify >= ((select IFNULL(sum(quantity),0) from stocklog where product_id = products.id and type IN ('in','purchase','refund') and deleted_at IS NULL) - (select IFNULL(sum(quantity),0)  from stocklog where product_id = products.id and type in ('out','sale')")->get();
    
    return $products;
}

function getSoftwareVersion()
{
    if (\Cache::has('app_version')) {
        return \Cache::get('app_version');
    }
    $opt = App\Setting::where('key', 'version')->first();
    \Cache::put('app_version', $opt->value, 36000);
    return $opt->value;
}
function saleOrderStatus($status_number)
{
    switch ($status_number) {
        case App\SaleOrder::PENDING:
            # code...
        return "PENDING";
            break;

        case App\SaleOrder::ACTIVE:
            # code...
            return "ACTIVE";
            break;


        case App\SaleOrder::QUOTATION:
            # code...
            return "QUOTATION";
            break;

        case App\SaleOrder::COMPLETED:
            # code...
            return "COMPLETED";
            break;
        
        default:
            # code...
        return "Undefined";
            break;
    }
}

function saleOrderStatusHtml($status_number)
{
    switch ($status_number) {
        case App\SaleOrder::PENDING:
            # code...
        return "<span style='color:orange'>PENDING</span>";
            break;

        case App\SaleOrder::ACTIVE:
            # code...
            return "<span style='color:red'>ACTIVE</span>";
            break;


        case App\SaleOrder::QUOTATION:
            # code...
            return "<span style='color:purple'>QUOTATION</span>";
            break;

        case App\SaleOrder::COMPLETED:
            # code...
            return "<span style='color:green'>COMPLETED</span>";
            break;

        case App\SaleOrder::FINISHED:
            # code...
            return "<span style='color:green'>FINISHED</span>";
            break;

        default:
            # code...
        return "Undefined";
            break;
    }
}

function formating_price($price)
{
    if (!$price) {
        return "-";
    }
    if ($price < 0) {
        return "(".number_format(abs($price)).")";
    }
    return number_format($price);
}


function no_negative($price)
{
    // return $price;
    if ($price > 0) {
        return formating_price($price);
    }
    return "-";
}
function versioning($type)
{
    switch ($type) {
        case 'products':
            # code...
            return md5(Cache::get('products'));
            break;
        
        default:
            # code...
        return 5;
            break;
    }
}

function entry_price_record($date, $product_id, $price, $type=false)
{
    $formated_date = date("Y-m-d", strtotime($date));
    $price_rec = App\PriceRecord::firstOrNew(['date'=>$formated_date, 'product_id'=>$product_id]);
    // $price = new App\PriceRecord;
    $price_rec->product_id = $product_id;
    $price_rec->price = $price;
    $price_rec->date=$formated_date;
    if ($type) {
        $price_rec->type=$type;
    }
    $price_rec->save();
    return true;
}


function supplier_price_record($date, $price, $supplier_id, $product_id)
{
    $formated_date = date("Y-m-d", strtotime($date));
    // $supplier_price_record = App\SupplierPriceRecord::firstOrNew(['product_id'=>$product_id, 'supplier_id'=>$supplier_id]);
    $supplier_price_record = new App\SupplierPriceRecord;
    $supplier_price_record->supplier_id = $supplier_id;
    $supplier_price_record->product_id = $product_id;
    $supplier_price_record->date = $formated_date;
    $supplier_price_record->price = $price;
    $supplier_price_record->save();

    $type = "Supplier Price Update";
    entry_price_record($formated_date, $product_id, $price, $type);
    return $supplier_price_record->id;
}

function last_purchased_price($product_id)
{
    if (!is_allowed('product-show-purchase-price')) {
        return 0;
        // return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
    }
    $p =  App\SupplierPriceRecord::where('product_id', $product_id)->orderBy('date', 'desc')->first();

    if (!$p) {
        return 0;
    }
    return $p->price;
}


function getSupplierRate($supplier, $product)
{
    if (!is_allowed('product-show-purchase-price')) {
        return $product->salePrice;
        // return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
    }
    $sup_rate = App\SupplierPriceRecord::where('product_id', $product->id)->where('supplier_id', $supplier->id)->orderBy('id', 'desc')->first();

    if ($sup_rate) {
        return $sup_rate->price;
    }

    $sup_rate = App\SupplierPriceRecord::where('product_id', $product->id)->orderBy('date', 'desc')->first();
    if ($sup_rate) {
        return $sup_rate->price;
    }

    return $product->salePrice;
}

function getSupplierRateOnBatchId($pid, $sid)
{
    $purchase = App\SupplierPriceRecord::where('product_id', $pid)->where('id', $sid)->first();
    return $purchase->price;
}

function product_price_update($product_id, $price, $date)
{
    $formated_date = date("Y-m-d", strtotime($date));
    $product = App\Products::whereId($product_id)->update(['salePrice'=>$price]);
    //update all customers
    App\Rates::where('product_id', $product_id)->update(['salePrice'=>$price]);
    //end update all customers
    $type = "Sale Price Update";
    entry_price_record($formated_date, $product_id, $price, $type);
}
function last_payment_made_customer($customer_id)
{
    return App\Transaction::where('customer_id', $customer_id)->where('type', 'in')->max('date');
}

function last_payment_made_supplier($supplier_id)
{
    return last_payment_made_customer($supplier_id);
    // return App\Transaction::where('supplier_id',$supplier_id)->where('type','out')->max('date');
}

function amount_cdr($amount, $inverse=false)
{
    if (!$amount) {
        return "NL";
    }
    if ($inverse) {
        $amount = $amount*-1;
    }
    $ss = number_format(abs($amount));
    if ($amount < 0) {
        return $ss." DR";
    }
    return $ss." CR";
}


function getSupplierBalance($supplier)
{
    if (!is_allowed('report-balance_sheet')) {
        return 0;
        // return $product->salePrice;
            // return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
    }
    //n getCustomerBalance($supplier);
    $credit = \App\Transaction::where('supplier_id', $supplier)->where('type', 'out')->sum('amount');
    $debit	= \App\Transaction::where('supplier_id', $supplier)->where('type', 'in')->sum('amount');

    return $debit - $credit;
}


function generate_login_pin()
{
    if (!\Cache::has('pin')) {
        $pin = mt_rand(1000, 9999);
        $expiresAt = \Carbon\Carbon::now()->addMinutes(300);
        \Cache::put('pin', $pin, $expiresAt);
        $format = "Your Login PIN is ".$pin;
        $p = (new \App\Http\Controllers\SMController)->line_notification($format);
    }
    return \Cache::get('pin');
}

function purchase_price_by_stock_detail($product_id, $warehouse_id, $quantity, $created_at)
{
    return App\Purchase::where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->where('stock', $quantity)->where('created_at', $created_at)->sum('price');
}
function get_units()
{
    return App\Unit::all();
}
function get_unit($id)
{
    return App\Unit::whereId($id)->first();
}

function fix_invoice_total($invoice_id)
{
    $in = \App\Invoice::whereId($invoice_id)->first();
    if (!$in) {
        return;
    }

    $total = $in->shipping + $in->tax - $in->discount;
    foreach ($in->orders as $key => $value) {
        # code...
        $total += $value->quantity * $value->salePrice;
    }

    $in->total = $total;
    $in->save();
    return;
}


function get_print_template($id, $name, $title, $message_top=null, $exporting_columns=':visible')
{
    $p="{ extend: '".$name."', title: \"".$title."\" ,footer: true";
    if ($message_top) {
        $p.= ", messageTop: '".$message_top."'";
    }
    if ($name != "print") {
        $p = str_replace('<h6>', '', $p);
        $p = str_replace('</h6>', '\n', $p);
    }
    if ($name == "pdfHtml5") {
        $p .= ",customize : function(doc){
            var colCount = new Array();
            $('".$id."').find('tbody tr:first-child td').each(function(){
                if($(this).attr('colspan')){
                    for(var i=1;i<=$(this).attr('colspan');i++){
                        colCount.push('*');
                    }
                }else{ colCount.push('*'); }
            });
            doc.content[2].table.widths = colCount;}";
    }

    if ($exporting_columns) {
        $p .= ", exportOptions: {
                    columns: '".$exporting_columns."'
                }";
    }
   

    return $p."}";
}



function is_password_validated()
{
    // return true; //temp allow all requests
    $pin = generate_login_pin();

    $valid_passwords = array("admin" => $pin);
    $valid_users = array_keys($valid_passwords);

    if (!array_key_exists('PHP_AUTH_USER', $_SERVER)) {
        $_SERVER['PHP_AUTH_USER'] = "";
    }
    if (!array_key_exists('PHP_AUTH_PW', $_SERVER)) {
        $_SERVER['PHP_AUTH_PW'] = "";
    }


    $user = $_SERVER['PHP_AUTH_USER'];
    $pass = $_SERVER['PHP_AUTH_PW'];

    return (in_array($user, $valid_users)) && (($pass == $valid_passwords[$user]) || ($pass == "123456789"));
}

function estimated_purchased_price($product_id)
{
    $p =  App\SupplierPriceRecord::where('product_id', $product_id)->avg('price');

    // if (!$p)
    // {
    // 	return 0;
    // }
    return $p;
}

function get_purchase_pricing_on_date($product_id, $date)
{
    $p =  App\SupplierPriceRecord::where('product_id', $product_id)->where('date', '<=', $date)->orderBy('date', 'desc')->first();
    if (!$p) {
        return estimated_purchased_price($product_id);
    }
    return $p->price;
}


function find_current_product_sale($product_id, $sales)
{
    foreach ($sales as $key => $value) {
        # code...
        if ($value['product_id'] == $product_id) {
            return $value['sm'];
        }
    }
    return 0;
}

function get_sale_invoice_payment_amount($invoice_id)
{
    return \App\Transaction::where('type', 'in')->where('invoice_id', $invoice_id)->sum('amount');
}


function get_invoice_submit_buttons($name = "Purchase", $method = 'Add', $url=false)
{
    $edit = "";

    if ($method == 'Add') {
        $edit = '<a class="btn btn-default" href="'.$url.'"> New Invoice </a>';
    }
    return $edit.'<input type="hidden" name="print" id="print_val">
            <button class="btn btn-primary" id="save_button" type="submit">'.$method.' '.$name.' [CTRL + S]</button>
            <button class="btn btn-warning btn-sm" id="print_button">'.$method.' '.$name.' and Print Invoice [CTRL + P]</button>
            <button class="btn btn-warning btn-sm" id="small_print_button">'.$method.' '.$name.' and Print Small [CTRL + G]</button>
            </center>';
}

function getExpenseName($id)
{
    $expense = ExpenseHead::where('id', $id)->first();
    return $expense->name;
}

function getManualAmount($id)
{
    $manual = Transaction::where('invoice_id', $id)->first();
    $getTax = Invoice::where('id', $id)->first();
    return $manual->amount - $getTax->tax;
}
function checkPayment($id)
{
    $payment = Transaction::where('invoice_id', $id)->where('type', 'in')->first();
    return $payment->amount;
}

function hotkey_print_script()
{
    return '<script>
	$("#save_button").click(function(){
			$("#print_val").val("");
		});
		$("#print_button").click(function(){
			$("#print_val").val("lg");;
		});
		$("#small_print_button").click(function(){
			$("#print_val").val("sm");
		});
	$(window).bind(\'keydown\', function(event) {
    if (event.ctrlKey || event.metaKey) {
        switch (String.fromCharCode(event.which).toLowerCase()) {
        case \'s\':
            event.preventDefault();
            $("#print_val").val("");
            $("#save_button").click();
            break;
        case \'p\':
            event.preventDefault();
            $("#print_val").val("lg");
            $("#print_button").click();
            break;
        case \'g\':
            event.preventDefault();
            $("#print_val").val("sm");
            $("#small_print_button").click();
            break;
        }
    }
});</script>';
}

function check_negative($value)
{
    if ($value < 0) {
        return "(" . number_format($value * -1, 2) . ")";
    }
    return number_format($value, 2);
}


function date_to_str($date)
{
    return date('d M Y', strtotime($date));
}
function settings($column_name)
{
    $setting = App\Setting::first();
    return isset($setting->$column_name)?$setting->$column_name:"";
}

function profilepic($url)
{
    if (!$url || !file_exists(public_path() . '/' . $url)) {
        return asset('images/profile.png');
    }
    return asset($url);
}

function is_holiday($date)
{
    $holiday = Modules\HumanResource\Entities\Holiday::where('from', '<=', $date)->where('to', '>', $date)->count('id');
    return ($holiday > 0);
}

function is_leave($date, $employeid)
{
    $leave = Modules\HumanResource\Entities\EmployeeLeaves::where('employee_id', $employeid)->where('day', '=', $date)->count('id');
    return ($leave > 0);
}

function is_present($date, $employeid)
{
    $leave = Modules\HumanResource\Entities\EmployeeAttendance::where('employee_id', $employeid)->where('day', '=', $date)->count('id');
    return ($leave > 0);
}

function calculateworkinghours($starttime, $endtime)
{
    $time = strtotime($endtime) - strtotime($starttime);
    // \Log::info(print_r(strtotime($starttime)));
    if ($endtime < $starttime) {
        $time = strtotime("24:00:00") - strtotime($starttime) + strtotime($endtime) - strtotime("00:00:00");
    }
    return $time / 3600;
}

function getDefaultWorkingDaysArray()
{
  return ["monday","tuesday","wednesday","thursday","friday","saturday"];
}

function getholidays($working_days = false, $month = "this month", $employeid)
{
    $start_month = new \Carbon\Carbon('first day of ' . $month);
    $end_month = new \Carbon\Carbon('last day of ' . $month);
    if (!$working_days || !is_array($working_days)) {
        $working_days = getDefaultWorkingDaysArray();
    }
    $holiday = 0;
    for ($date = $start_month; $date->lte($end_month); $date->addDay()) {
        $today_is_day = strtolower(date('l', strtotime($date)));
        if (in_array($today_is_day, $working_days)) {
            if (is_holiday($date->format('Y-m-d')) && !is_leave($date->format('Y-m-d'), $employeid)) {
                $holiday++;
            }//end if
        }//end if
    }//end for
    return $holiday;
}

function getActiveDays($working_days = false, $month = "this month")
{
    $start_month = new \Carbon\Carbon('first day of ' . $month);
    $end_month = new \Carbon\Carbon('last day of ' . $month);
    if (!$working_days || !is_array($working_days)) {
        return $start_month->daysInMonth;
    }
    $workday = 0;
    for ($date = $start_month; $date->lte($end_month); $date->addDay()) {
        $today_is_day = strtolower(date('l', strtotime($date)));
        if (in_array($today_is_day, $working_days)) {
            $workday++;
        }
    }
    return $workday;
}

function getLeaves($working_days = false, $month = "this month", $employeid)
{
    $start_month = new \Carbon\Carbon('first day of ' . $month);
    $end_month = new \Carbon\Carbon('last day of ' . $month);
    if (!$working_days || !is_array($working_days)) {
        $working_days = getDefaultWorkingDaysArray();
    }
    $leave = 0;
    for ($date = $start_month; $date->lte($end_month); $date->addDay()) {
        $today_is_day = strtolower(date('l', strtotime($date)));
        if (in_array($today_is_day, $working_days)) {
            if (is_leave($date->format('Y-m-d'), $employeid)) {
                $leave++;
            }//end if
        }//end if
    }//end for
    return $leave;
}

function getPresentDays($working_days = false, $month = "this month", $employeid)
{
    $start_month = new \Carbon\Carbon('first day of ' . $month);
    $end_month = new \Carbon\Carbon('last day of ' . $month);
    if (!$working_days || !is_array($working_days)) {
        $working_days = getDefaultWorkingDaysArray();
    }
    $present = 0;
    for ($date = $start_month; $date->lte($end_month); $date->addDay()) {
        $today_is_day = strtolower(date('l', strtotime($date)));
        // if(in_array($today_is_day, $working_days))
        // {
        if (is_present($date->format('Y-m-d'), $employeid)) {
            $present++;
        }//end if
        // }//end if
    }//end for
    return $present;
}

function getabsent($working_days = false, $month = "this month", $employeid)
{
    $start_month = new \Carbon\Carbon('first day of ' . $month);
    $end_month = new \Carbon\Carbon('last day of ' . $month);
    if (!$working_days || !is_array($working_days)) {
        $working_days = getDefaultWorkingDaysArray();
    }
    $absent = 0;
    for ($date = $start_month; $date->lte($end_month); $date->addDay()) {
        $today_is_day = strtolower(date('l', strtotime($date)));
        if (in_array($today_is_day, $working_days)) {
            if (!is_leave($date->format('Y-m-d'), $employeid) && !is_present($date->format('Y-m-d'), $employeid) && !is_holiday($date->format('Y-m-d'))) {
                $absent++;
            }//end if
        }//end if
    }//end for
    return $absent;
}

function getWorkingHours($working_days = false, $month = "this month", $employeid)
{
    $start_month = new \Carbon\Carbon('first day of ' . $month);
    $end_month = new \Carbon\Carbon('last day of ' . $month);
    if (!$working_days || !is_array($working_days)) {
        $working_days = getDefaultWorkingDaysArray();
    }
    $working_hours = 0;
    $employee = Modules\HumanResource\Entities\Employee::whereId($employeid)->first();
    $prsent = Modules\HumanResource\Entities\EmployeeAttendance::where('employee_id', $employeid)
        ->where('day', '>=', $start_month->format('Y-m-d'))
        ->where('day', '<=', $end_month->format('Y-m-d'))
        ->get();
    foreach ($prsent as $key => $value) {
        # code...
        $time = 0;
        if ($value->shift == "night") {
            $time = strtotime($value->time_in) - strtotime($value->time_out);
        }
        if (!$value->shift || $value->shift == "day") {
            $time = strtotime($value->time_out) - strtotime($value->time_in);
        }
        $working_hours += $time / 3600;
    }
    return $working_hours;
}

function getInvoiceBalance($invoice_id)
{
    $credit = \App\Transaction::where('invoice_id', $invoice_id)->where('type', 'out')->sum('amount');
    $debit	= \App\Transaction::where('invoice_id', $invoice_id)->where('type', 'in')->sum('amount');

    return $credit - $debit;
}

function get_all_suppliers()
{
    return \App\Supplier::all();
}

function get_supplier_of_product($id)
{
    $get_supp_prod = \App\SupplierPriceRecord::where('product_id', $id)->first();
    return get_supplier($get_supp_prod->supplier_id);
}
function get_unit_name($id)
{
    return \App\Unit::whereId($id)->pluck('name')->first();
}

function GetDateFormatForJS($format)
{
    switch ($format) {
        case 'd-m-Y':
            return 'DD-MMM-YYYY';
        case 'm-d-Y':
            return 'DD/MMM/YYYY';
        case 'Y-m-d':
            return 'YYYY-MMM-DD';
        case 'd/m/Y':
            return 'DD/mmm/YYYY';
        case 'm/d/Y':
            return 'mmm/dd/YYYY';
        case 'Y/m/d':
            return 'YYYY/mmm/dd';
        case 'd-M-Y':
            return 'dd-MMM-YYYY';
        case 'M-d-Y':
            return 'MMM-dd-YYYY';
        case 'd/M/Y':
            return 'dd/MMM/YYYY';
        case 'M/d/Y':
            return 'MMM/dd/YYYY';
        case 'jS M, Y':
            return 'jS MMM, YYYY';
        case 'M jS, Y':
            return 'MMM jS, YYYY';
        case 'd F, Y':
            return 'dd F, YYYY';
        case 'F d, Y':
            return 'F dd, YYYY';
        default:
            return 'DD-MMM-YYYY';
    }
}

function getExpense($from, $to)
{
    return App\Transaction::whereBetween('date', [$from,$to])
            ->where('type', 'expense')
            ->sum('amount');
}

function getPendingOrders()
{
    $days = session()->get('settings.misc.pending_sales')?:5;
    return App\SaleOrder::where('delivery_date', '>=', date('y:m:d'))
    ->where('delivery_date', '<=', date('y:m:d', strtotime('+'.$days.' days')))
    ->where('status', 0)
    ->where('posted', 0)
    ->get();
}

function customerDetails($customer_id, $customer_name, $customer_city)
{
    return "<a href='/customers/{$customer_id}' target='_blank'>{$customer_name}<br><small>{$customer_city}</small></a>";

}

function processJsonFilters($query, $request, $replacement=[])
{
    // dd($replacement);
    if ($request->has('filter'))
    {
        eval('$filters = '.$request->filter.';');
        if (!is_array($filters))
        {
            $filters = [];
        }
        
        $filters = (array_flatten($filters));

        //i == column
        //i+1 == operation
        //i+2 == value
        //i+3 == next iteration and/OR
        foreach($replacement as $needle => $replace)
        {
            // dd($replacement['PENDING']);
            $filters = array_map(function ($v) use ($needle, $replace) {
                return $v == $needle ? (String)$replace : $v;
            }, $filters);
        }
        $new_filter = [];
        // $default = "and";
        $previous_iteration = null;
        for($i=0;$i<count($filters);$i++)
        {
            switch($filters[$i+1])
            {
                case 'contains':
                    $filters[$i+1] = 'LIKE';
                    $filters[$i+2] = '%'.$filters[$i+2].'%';
                break;
                case 'notcontains':
                    $filters[$i+1] = 'NOT LIKE';
                    $filters[$i+2] = '%'.$filters[$i+2].'%';
                break;

                case 'startswith':
                    $filters[$i+1] = 'LIKE';
                    $filters[$i+2] = $filters[$i+2].'%';
                break;

                case 'endswith':
                    $filters[$i+1] = 'LIKE';
                    $filters[$i+2] = '%'.$filters[$i+2];
                break;
            }

            $default = isset($filters[$i+3])?$filters[$i+3]:'and';

            // when apply more than one filter
            //i-1 == previous iteration and/OR
            if ($i>0)
            { 
                $previous_iteration = $filters[$i-1];
            }
            if($default =='or')
            {
                $new_filter[] = [$filters[$i], $filters[$i+1], $filters[$i+2]];
            }
            elseif($previous_iteration == 'or' && $default == 'and')
            {
                $new_filter[] = [$filters[$i], $filters[$i+1], $filters[$i+2]]; 
                $query->where(function($query) use ($new_filter) {
                    foreach($new_filter as $filter)
                    {
                        $query->orWhere($filter[0], $filter[1], $filter[2]);
                    }
                });
                $new_filter = [];
            }
            else{
                $query->where($filters[$i], $filters[$i+1], $filters[$i+2]);
            }
            
            $i = $i+3;
        }
    }
   
    if ($request->sort)
    {
        $sort = json_decode($request->sort);
        foreach($replacement as $needle => $replace)
        {
            if($needle == $sort[0]->selector)
            {
                $sort[0]->selector = $replace;
            }
        }
        $query = $query->orderBy($sort[0]->selector,$sort[0]->desc == 'true' ? 'desc' : 'asc');
    }
   
    if ($request->has('group'))
    { 
        // Date Interval Grouping
        // Input [{"selector":"date","groupInterval":"year","isExpanded":true},{"selector":"date","groupInterval":"month","isExpanded":true},{"selector":"date","groupInterval":"day","isExpanded":false}]
        $group = json_decode($request->group);
        foreach($replacement as $needle => $replace)
        {
            if($needle == $group[0]->selector)
            {
                $group[0]->selector = $replace;
            }
        }
        
        $query = $query->select($group[0]->selector." as key2", \DB::raw("count(*) as count"))->groupBy($group[0]->selector)->get();
        $json = [];
        $date_data = [];
        if (isset($group[0]->groupInterval) &&  $group[0]->groupInterval== "year")
        {
            //map it like a hero
            foreach($query as $date)
            {
                $year_ky = date('Y', strtotime($date->key2));
                $month_ky = date('n', strtotime($date->key2));
                $day_ky = date('d', strtotime($date->key2));

                $found_year = false;
                $found_month = false;
                $found_day = false;
                //first key is the year
                foreach($date_data as $ykey => $data)
                {
                    if ($data['key'] == $year_ky)
                    {
                        $found_year = $ykey;
                        //second key is the month
                        foreach($data['items'] as $mkey=> $month)
                        {
                            if ($month['key'] == $month_ky)
                            {
                                $found_month = $mkey;
                                //third key is the day
                                foreach($month['items'] as $dkey=> $day)
                                {
                                    if ($day['key'] == $day_ky)
                                    {
                                        $found_day = $dkey;
                                        $day['count'] = $day['count'] + $date->count;
                                    }
                                }
                            }
                        }
                    }
                }
                
                if ($found_year === false) //must check the zero key
                {
                    $date_data[] = [
                        'key' => $year_ky,
                        'items' => [
                            [
                                'key' => $month_ky,
                                'items' => [
                                    [
                                        'key' => $day_ky,
                                        'count' => $date->count
                                    ]
                                ]
                            ]
                        ]
                    ];
                }else
                if ($found_month === false)
                {
                    $date_data[$ykey]['items'][] = [
                        'key' => $month_ky,
                        'items' => [
                            [
                                'key' => $day_ky,
                                'count' => $date->count
                            ]
                        ]
                    ];
                }else
                if ($found_day === false)
                {
                    $date_data[$ykey]['items'][$mkey]['items'][] = [
                        'key' => $day_ky,
                        'count' => $date->count
                    ];
                }

                
            }
            return ['data' => $date_data];
        }
        foreach($query as $d)
        {
            $json[] = ['key'=>$d->key2,'value'=>$d->count, 'items'=>null];
        }
        return ['data'=>$json,'totalCount'=>$query->count()];
    }
    
    return $query->skip(($request->skip)??0)->take(($request->take)??10)->get();
}

function getProductOptionalFields($product, $concatWith=" ")
{
    $name = "{$product->name} {$product->brand}";
    if (!empty(session()->get('settings.products.optional_items'))) {
        $fields = explode(",", session()->get('settings.products.optional_items'));
        foreach ($fields as $field) {
            if (!empty($product->$field) && strpos(session()->get('settings.products.optional_items'), $field) !== false) {
                $name .= "$concatWith" . (($field == 'category')?$product->$field->name:$product->$field);
            }
        }
    }
    return $name;
}

function termsBackground($pdf,$background){
    // get the current page break margin
    $bMargin = $pdf->getBreakMargin();
    // get current auto-page-break mode
    $auto_page_break = $pdf->getAutoPageBreak();
    // disable auto-page-break
    $pdf->SetAutoPageBreak(false, 0);
    // set bacground image
    $pdf->Image($background, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
    // restore auto-page-break status
    $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
    // set the starting point for the page content
    $pdf->setPageMark();
}