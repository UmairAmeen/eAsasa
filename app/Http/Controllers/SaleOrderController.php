<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\User;
use App\Order;
use App\Rates;
use App\Invoice;
use App\Customer;
use App\Products;
use App\Warehouse;
use App\SaleOrder;
use App\Transaction;
use App\DeliveryChallan;
use App\SalesPerson;
use App\InvoiceExtendedView;
use App\Http\Controllers\SMController;
use Carbon\Carbon;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Exception;

class SaleOrderController extends Controller {

	public function __construct()
	{
		View::share('title',"Sale Order");
		View::share('load_head',true);
		View::share('sales_order_menu',true);
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if (!is_allowed('sale-list')) {
			return redirect('/');
		}
		$sale_orders = SaleOrder::orderBy('id', 'desc')->paginate(10);
		return view('sale_orders.index', compact('sale_orders'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if (!is_allowed('sale-create')) {
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$salesPersons = SalesPerson::all();
		return view('sale_orders.create',compact('salesPersons'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$manual_total = $request->manual_total;
		$manual_check = ($request->manualMode == 'Enable Manual Mode')? 0 : 1;
		$tax_check = (!isset($request->taxMode) || $request->taxMode == 'Enable Tax Mode')? 0 : 1;
		if (!is_allowed('sale-create')) {
			return response(['message' => 'Unauthorised'], 500);
		}
        if ($request->custom_field) {
            $array_c = array_combine($request->custom_labels, $request->custom_field);
            $encoded = json_encode($array_c);
        }
		DB::beginTransaction();
		$invoice_id = 0;		
		try {
			$product_ids = $request->product;
			$quantity = $request->quantity;
			$notes = $request->note;
			$saleprice = $request->sale_price;
			$notes = $request->note;
			/////////////////////////
			//for manual sale order
			$quantity_sum = 0;
			foreach($quantity as $q) {
				$quantity_sum += $q;
			}
			$single_manual_price = $manual_total / $quantity_sum;
			/////////////////////////
			$invoice = new Invoice();
			$invoice->description = $request->description;
			$invoice->shipping = $request->shipping;
			$invoice->sales_person = $request->sales_person;
			$invoice->custom_inputs = ($encoded)?$encoded:null;
			$invoice->discount = $request->discount;
			$invoice->related_to = $request->related_to;
			$invoice->is_manual = $manual_check;
			$invoice->is_tax = ($tax_check == 1)?1:0;
			$invoice->tax_percentage = $request->taxPercent/100;
			//manual invoice mode check + tax check + manual tax check
			if ($manual_check == 1) {
				$invoice->tax = ($tax_check == 1) ? ($invoice->tax_percentage * $manual_total) : 0;
			} else {
				$invoice->tax = ($tax_check == 1) ? $invoice->tax_percentage * ($request->total1 - $request->taxAmount) : 0;
			}
			$invoice->customer_id = $request->customer;
			$invoice->bill_number = $request->bill_number;
			$invoice->date = date('Y-m-d',strtotime($request->date));
			$invoice->type="sale_order";
			$invoice->added_by = \Auth::id();
			if (!empty($request->id)) {
				$invoice->id = $request->id;
			}
			$invoice->save();
			$invoice_id = $invoice->id;
			$sale_order = new SaleOrder();	
			$sale_order->invoice_id = $invoice_id;
			$sale_order->sales_people_id = $request->salesPerson;
			$sale_order->customer_id = $request->customer;
			$sale_order->source = $request->source;
			$sale_order->posted = false;
			$sale_order->status = $request->status;
			$sale_order->date = date('Y-m-d',strtotime($request->date));
			if($request->delivery_date) {
				$sale_order->delivery_date = date('Y-m-d', strtotime($request->delivery_date));
			} else {
				$sale_order->delivery_date = Null;
			}
			$sale_order->save();
			$worth = 0;
			foreach ($product_ids as $key => $value) {
				$order = new Order();
				$order->invoice_id = $invoice_id;
				$order->product_id = $product_ids[$key];
				$order->note = $notes[$key];
				$order->delivery_status = DeliveryChallan::PENDING;
				if($manual_check == 1) {
					$order->salePrice = $single_manual_price;
				} else {
					$order->salePrice = $saleprice[$key];
				}
				// $order->salePrice = $saleprice[$key];
				$order->note = $notes[$key];
				$order->quantity = $quantity[$key];
				$order->save();
				if($manual_check == 0) {
					$worth += $order->salePrice*$order->quantity;
				}
				//update rate for user
				$rate = Rates::FirstOrNew(['customer_id' => $request->customer, 'product_id' => $value]);
				$rate->salePrice = $saleprice[$key];
				$rate->save();
				//end update rate for user
			}
			if(!$invoice->shipping) {
				$invoice->shipping = 0;
			}
			if(!$invoice->tax) {
				$invoice->tax = ((session()->get('settings.sales.tax_percentage')/100) * $worth);
			}
			if(!$invoice->discount) {
				$invoice->discount = 0;
			}
			if($manual_check == 1) {
				$worth = $manual_total + $invoice->tax - $invoice->discount;
			} else {
				$worth += $invoice->shipping + $invoice->tax - $invoice->discount;
			}
			$invoice->total = $worth;
			$invoice->save();

			$invoice_id = $invoice->id;

			if ($request->mark_paid || $request->post_order) {
				$sale_order->posted = true;
				$sale_order->save();
				$transactions = new Transaction;
				$transactions->date = $sale_order->date;
				$transactions->type = "out";
				$transactions->invoice_id = $sale_order->invoice_id;
				$transactions->amount = $worth;
				$transactions->payment_type = $request->payment_type;
				$transactions->customer_id = $sale_order->customer_id;
				$transactions->added_by = \Auth::id();
				$transactions->save();
				if ($request->payment) {
						$transactions = new Transaction;
						$transactions->date = $sale_order->date;
						$transactions->type = "in";
						$transactions->invoice_id = $sale_order->invoice_id;
						$transactions->amount = $request->payment;
						$transactions->bank = $request->bank?$request->bank:Null;
						$transactions->transaction_id = $request->transaction?$request->transaction:Null;
						$transactions->payment_type = $request->payment_type;
						$transactions->customer_id = $sale_order->customer_id;
						$transactions->added_by = \Auth::id();
						$transactions->save();
						Invoice::where('id',$invoice_id)->update(['advance' => $request->payment]);
						if (!empty($request->payment_creditcard)) {
							$transactions_c = new Transaction;
							$transactions_c->date = $invoice->date;
							$transactions_c->type = "in";
							$transactions_c->invoice_id = $invoice->id;
							$transactions_c->amount = $request->payment_creditcard;
							$transactions_c->payment_type = $request->payment_type_2;
							$transactions_c->customer_id = $invoice->customer_id;
							$transactions_c->added_by = \Auth::id();
							$transactions_c->save();
							Invoice::where('id',$invoice_id)->update(['advance' => $request->payment_creditcard]);
						}
				} elseif ($request->mark_paid) {
					$transactions = new Transaction;
					$transactions->date = $sale_order->date;
					$transactions->type = "in";
					$transactions->invoice_id = $sale_order->invoice_id;
					$transactions->amount = $worth;
					$transactions->payment_type = "cash";
					$transactions->customer_id = $sale_order->customer_id;
					$transactions->added_by = \Auth::id();
					$transactions->save();
					Invoice::where('id',$invoice_id)->update(['advance' => $worth]);
				}
			}

			DB::commit();
		} catch(Exception $e) {
			DB::rollBack();
			return response()->json(['message' => $e->getMessage()], 403);
		}

		//Branded SMS functionality started
		$response = null;
		if(getSetting('sms_enable') == '1')
		{
			if(getSetting('sms_url') != "" && getSetting('sms_user_name') != "" && getSetting('sms_mask') != "" && getSetting('sms_password') != ""){
				$message = "Dear customer your order # ".$invoice->id." of worth RS.".($invoice->total + 0)." has been successfully created and you have payed RS.".($transactions->amount + 0).". Balance is RS.".(($invoice->total - ($transactions->amount ? : 0)) + 0).". Thank you.";
				$result = (new SMController)->send_sms($invoice->customer->phone,$message);
				$response = ($result == "Sent Successfully")?"SMS Sent" : $result;
			}
			else{
				$response = "SMS Not Sent due to invalid Credentials";
			}
		}

		//Branded SMS fucntionality ended

		if ($request->print == "sm") {
			return response()->json(['message'=>"Sale Order Added Successfully",'sms_message' => $response,'action'=>'redirect','do'=>url('/smallInvoice/'.$sale_order->invoice_id)],200);
		}
		if ($request->print == "lg") {
			return response()->json(['message'=>"Sale Order Added Successfully",'sms_message' => $response,'action'=>'redirect','do'=>url('/invoices/'.$sale_order->invoice_id)],200);
		}
		return response()->json(['message'=>"Sale Order Added Successfully",'sms_message' => $response,'action'=>'redirect','do'=>url('/sale_orders/'.$sale_order->id)],200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		if (!is_allowed('sale-list')) {
			return redirect('/');
		}
		$sale_order = SaleOrder::findOrFail($id);
		$worth = 0;
		foreach ($sale_order->invoice->orders as $key => $value) {
			# code...
			$worth += $value->salePrice*$value->quantity;
		}
		$worth += $sale_order->invoice->shipping + $sale_order->invoice->tax;
		$worth -= $sale_order->invoice->discount;

		return view('sale_orders.show', compact('sale_order','worth'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if (!is_allowed('sale-edit'))
		{
			return redirect('/');
		}
		$sale_order = SaleOrder::findOrFail($id);
    $transaction = Transaction::where(['invoice_id' => $sale_order->invoice_id, 'type' => 'in'])
		->orderBy('id', 'asc')->first();
		$salesPersons = SalesPerson::all();

		return view('sale_orders.edit', compact('sale_order', 'transaction','salesPersons'));
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
		$manual_total = $request->manual_total;
		$manual_check = ($request->manualMode == 'Enable Manual Mode') ? 0 : 1;
		$tax_check = ($request->taxMode == 'Enable Tax Mode') ? 0 : 1;
		if (!is_allowed('sale-edit')) {
			return response(['message'=>'Unauthorised'],500);
		}
		$sale_order = SaleOrder::findOrFail($id);
        if ($request->custom_field) {
            $array_c = array_combine($request->custom_labels, $request->custom_field);
            $encoded = json_encode($array_c);
        }
		DB::beginTransaction();
		$invoice_id = $sale_order->invoice_id;
		try {
			$product_ids = $request->product;
			$quantity = $request->quantity;
			$notes = $request->note;
			$saleprice = $request->sale_price;
			$total_quantity = 0;
			foreach ($quantity as $q) {
				$total_quantity+=$q;
			}
			$single_manual_price = $manual_total / $total_quantity;
			$invoice = Invoice::whereId($invoice_id)->first();
			$invoice->description = $request->description;
			$invoice->shipping = $request->shipping;
			$invoice->custom_inputs = ($encoded)?$encoded:null;
			$invoice->bill_number = $request->bill_number;
			$invoice->related_to = $request->related_to;
			$invoice->sales_person = $request->sales_person;
			$invoice->is_manual = $manual_check;
			$invoice->is_tax = ($tax_check == 1) ? 1 : 0;
			$invoice->tax_percentage = $request->taxPercent/100;
			if ($manual_check == 1) {
				$invoice->tax = ($tax_check == 1)?($invoice->tax_percentage * $manual_total):0;			
			} else {
				$invoice->tax = ($tax_check == 1) ? $invoice->tax_percentage * ($request->total - $request->taxAmount) : 0;
			}
			$invoice->total = ($manual_check == 1) ? $manual_total:($request->total - $request->taxAmount);
			$invoice->discount = $request->discount;
			$invoice->date = date('Y-m-d',strtotime($request->date));
			$invoice->edited_by = \Auth::id();
			$invoice->customer_id = $request->customer;
			$invoice->save();
			$sale_order->status = $request->status;
			$sale_order->customer_id = $request->customer;
			$sale_order->source = $request->source;
			$sale_order->sales_people_id = $request->salesPerson;
			// $sale_order->posted = true;
			$sale_order->date = date('Y-m-d',strtotime($request->date));
			if($request->delivery_date) {
				$sale_order->delivery_date = date('Y-m-d', strtotime($request->delivery_date));
			} else {
				$sale_order->delivery_date = Null;
			}
            $sale_order->save();
			Order::where('invoice_id',$invoice_id)->delete();
			$worth = $request->shipping - $request->discount + $request->taxAmount;
			foreach ($product_ids as $key => $value) {				
				$order = new Order();
				$order->invoice_id = $invoice_id;
				$order->product_id = $product_ids[$key];
				$order->note = $notes[$key];
				$order->delivery_status = DeliveryChallan::PENDING;
				if($invoice->is_manual == 1){
					$order->salePrice = $single_manual_price;
				} else {
					$order->salePrice = $saleprice[$key];
				}
				$order->note = $notes[$key];
				$order->quantity = $quantity[$key];
				$order->save();
				$worth += $order->salePrice*$order->quantity;
				//update rate for user
				$rate = Rates::FirstOrNew(['customer_id'=>$invoice->customer_id, 'product_id'=>$value]);
				$rate->salePrice = $saleprice[$key];
				$rate->save();
				//end update rate for user
			}
			$sale_order->save();

			//update invoice using worth
			if (!$invoice->is_manual) {
				$invoice->total = $worth;
				$invoice->save();
			}
			if ($invoice->is_manual == true) {
				Transaction::where('invoice_id', $invoice_id)->where('type', 'out')->update(['amount'=>$manual_total +$invoice->tax	, 'edited_by'=>\Auth::id(),'customer_id'=>$invoice->customer_id]);
			} else {
				Transaction::where('invoice_id', $invoice_id)->where('type', 'out')->update(['amount'=>$worth	, 'edited_by'=>\Auth::id(),'customer_id'=>$invoice->customer_id]);
			}
			$advance_amount = Transaction::where('invoice_id', $invoice_id)->where('type','in')->first();
			if ($advance_amount) {
				$advance_amount->amount = $request->amount_paid;
				$advance_amount->edited_by = \Auth::id();
				$advance_amount->customer_id = $invoice->customer_id;
				$advance_amount->payment_type = $request->payment_type;
				$advance_amount->bank = $request->bank;
				$advance_amount->transaction_id = $request->transaction_id;
				$advance_amount->save();
			}			
		} catch(Exception $e) {
			DB::rollBack();
			return response()->json(['message' => $e->getMessage()], 403);
		}
		DB::commit();
		if ($request->print == "sm") {
			return response()->json(['message'=>"Sale Order Updated Successfully",'action'=>'redirect','do'=>url('/smallInvoice/'.$invoice->id)],200);
		}
		if ($request->print == "lg") {
			return response()->json(['message'=>"Sale Order Updated Successfully",'action'=>'redirect','do'=>url('/invoices/'.$invoice->id)],200);
		}
		return response()->json(['message'=>"Sale Order Added Successfully",'action'=>'redirect','do'=>url('/sale_orders/'.$sale_order->id)],200);
	}

	public function showTransaction(Request $request)
	{
		View::share('load_head',false);
		$invoice = Invoice::whereId($request->invoice_id)->first();
		$customer = Customer::whereId($request->customer_id)->first();
		if (!$invoice || !$customer)
		{
			return "Invalid Attempt";
		}

		$transactions = Transaction::where('invoice_id',$request->invoice_id)->get();

		return view('invoices.transactions',compact('invoice', 'transactions', 'customer'));
	}


	public function showStock(Request $request)
	{
		if (!is_allowed('stocks-list'))
		{
			return redirect('/');
		}
		View::share('load_head',false);
		$sale_order = SaleOrder::whereId($request->sale_order_id)->first();
		$product_id = Order::where('invoice_id',$sale_order->invoice_id)->pluck('product_id');
		$products = Products::whereIn('id',$product_id)->get();
		$customer = Customer::whereId($request->customer_id)->first();
		if (!$sale_order || !$customer)
		{
			return "Invalid Attempt";
		}

		$warehouses = Warehouse::all();

		return view('invoices.stock',compact('sale_order', 'warehouses', 'customer','products'));
	}

	public function updateStatus(Request $request)
	{
		if (!is_allowed('sale-edit'))
		{
			return response(['message'=>'Unauthorised'],500);
		}

		$sale_order = SaleOrder::whereId($request->id)->first();

		$token_fbr= null;

		$is_fbr = session()->get('settings.fbr.is_fbr_enable')?:'';
		$fbr_pos_id = session()->get('settings.fbr.fbr_pos_id')?:'';
		$fbr_url = session()->get('settings.fbr.fbr_url')?:'';
		if($is_fbr == 1 && $request->status == 5 && ($fbr_url == '' || $fbr_pos_id == ''))
		{
			return response(['message'=>'Please Compleate FBR Fields OR Disable "FBR Invoice" From Settings'],500);
		}
		if($request->status == 5 && $sale_order->status != 5 && $is_fbr == 1)
			{
				$buyer = Customer::where('id', $sale_order->customer_id)->get()->first();
				$invoice_id = $sale_order->invoice_id;
				$invoice = Invoice::where('id', $invoice_id)->get()->first();
				$orders = Order::where('invoice_id', $invoice_id)->get();
				$total_quantity = Order::where('invoice_id', $invoice_id)->sum('quantity');
				$total_wo_discount_shipping = ($invoice->total - $invoice->tax  + $invoice->discount) -  $invoice->shipping;

				$fbr_data["InvoiceNumber"] = ''; //blank
				$fbr_data["POSID"] = $fbr_pos_id; //c
				$fbr_data["USIN"] = $invoice_id ?: 1111; //c
				$fbr_data["DateTime"] = Carbon::now()->format("Y-m-d H:i:s"); //c
				$fbr_data["BuyerNTN"] = str_replace('-','',$buyer->ntn);//$buyer->ntn ?: 000000; //o 
				$fbr_data["BuyerCNIC"] = str_replace('-','',$buyer->cnic);// ?: 1234567890123; //o 
				$fbr_data["BuyerName"] = $buyer->name; //o
				$fbr_data["BuyerPhoneNumber"] = $buyer->phone ?: 03000000000; //o
				$fbr_data["TotalQuantity"] = $total_quantity ? : 1; //c
				$fbr_data["Discount"] = $invoice->discount ?: 0; //o
				$fbr_data["TotalSaleValue"] = $total_wo_discount_shipping; //c
				$fbr_data["TotalTaxCharged"] = ((session()->get('settings.sales.tax_percentage')/100) * $total_wo_discount_shipping); //c
				$fbr_data["TotalBillAmount"] = ((session()->get('settings.sales.tax_percentage')/100) * $total_wo_discount_shipping) + $total_wo_discount_shipping; //c
				$fbr_data["FurtherTax"] = $invoice->futher_tax ?: 0; //o
				$fbr_data["PaymentMode"] = 1;  //c
				$fbr_data["RefUSIN"] = ''; //o
				$fbr_data["InvoiceType"] = 1; //c -- new field in form

				$fbr_data["Items"] = [];
				foreach ($orders as $key => $order) {
					$product = Products::where('id', $order->product_id)->get()->first();
					$fbr_data["Items"][] = [ //c
						'ItemCode' => ($product->code) ? $product->code : 0000, //c
						'ItemName' => $product->name, //c
						'Quantity' => $order->quantity, //c
						'PCTCode' => ($product->pct_code) ? $product->pct_code : 0000, //c  
						'TaxRate' => session()->get('settings.sales.tax_percentage') ? : 0, //c  
						'SaleValue' => $order->salePrice, //c
						'TaxCharged' => ((session()->get('settings.sales.tax_percentage')/100) * ($order->salePrice * $order->quantity)) ?: 0, //c
						'TotalAmount' => ((session()->get('settings.sales.tax_percentage')/100) * ($order->salePrice * $order->quantity)) + ($order->salePrice * $order->quantity), //c
						'Discount' => 0, //o
						'FurtherTax' => $request->p_f_tax ?: 0, //o
						'InvoiceType' => 1, //c -- add field in form drop down
						'RefUSIN' => '', //O
					];
				}
				// dd($fbr_data);

				$token_fbr = '1298b5eb-b252-3d97-8622-a4a69d5bf818';
			}
		try{
			if($request->status == 5 && $sale_order->status != 5 && $is_fbr == 1)
			{
				$client = new Client(); //GuzzleHttp\Client
				$result = $client->post($fbr_url, [
					'verify' => false,
					'headers' => [
						'Authorization' => 'Bearer ' . $token_fbr,
						'content-type' => 'application/json'],
					'body' =>  json_encode($fbr_data, true)
				]);
				$fbr_invoice = json_decode($result->getBody())->InvoiceNumber;

				$invoice->fbr_invoice = $fbr_invoice;
				$invoice->save();
			}

			DB::beginTransaction();
			$sale_order->status = $request->status;
			if($sale_order->status == 4  || $sale_order->status == 5){
				$sale_order->completion_date = date('Y-m-d');
				Order::where('invoice_id', $sale_order->invoice_id)->update(['delivery_status' => DeliveryChallan::DELIVERED]);
			}
			elseif($sale_order->status != 4 && $sale_order->status != 5){
					$sale_order->completion_date = null;
					Order::where('invoice_id', $sale_order->invoice_id)->update(['delivery_status' => DeliveryChallan::PENDING]);
			}
			$sale_order->save();
			
		}catch(Exception $e)
		{
			DB::rollBack();
			return response()->json(['message' => $e->getMessage()], 500);
		}
		DB::commit();
		return response()->json(['message'=>"Sale Order Updated",'action'=>'redirect','do'=>url('/sale_orders/'.$sale_order->id)],200);

	}

	public function confirmOrder(Request $request)
	{
		if (!is_allowed('sale-edit'))
		{
			return response(['message'=>'Unauthorised'],500);
		}

		try{
			DB::beginTransaction();
			$sale_order = SaleOrder::whereId($request->id)->first();
			$worth = 0;
			foreach ($sale_order->invoice->orders as $key => $value) {
				# code...
				$worth += $value->salePrice*$value->quantity;
			}
			$worth += $sale_order->invoice->shipping + $sale_order->invoice->tax - $sale_order->invoice->discount;
			$sale_order->status = 1;
			$sale_order->posted = 1;
			$sale_order->save();

			$transactions = new Transaction;
			$transactions->date = $sale_order->date;
			$transactions->type = "out";
			$transactions->invoice_id = $sale_order->invoice_id;
			$transactions->amount = $worth;
			$transactions->payment_type = "cash";
			$transactions->customer_id = $sale_order->customer_id;
			$transactions->added_by = \Auth::id();
			$transactions->save();
			
		}catch(Exception $e)
		{
			DB::rollBack();
			return response()->json(['message' => $e->getMessage()], 500);
		}
		DB::commit();
		if ($request->dt)
		{
			return response(['message'=>"Updated",'action'=>'same_state_datable_reload','do'=>'.sale_order_listing']);
		}
		return response()->json(['message'=>"Sale Order Updated",'action'=>'redirect','do'=>url('/sale_orders/'.$sale_order->id)],200);


	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if (!is_allowed('sale-delete'))
		{
			return response(['message'=>'Unauthorised'],500);
		}
		DB::beginTransaction();
		try{
			$sale_order = SaleOrder::findOrFail($id);
			$invoice_id = $sale_order->invoice_id;
			$sale_order->delete();
			Invoice::whereId($invoice_id)->delete();
			Order::where('invoice_id',$invoice_id)->delete();
			Transaction::where('invoice_id', $invoice_id)->delete();

		}catch(Exception $e)
		{
			DB::rollBack();
			return response()->json(['message'=>"Unable to delete Sale Order"],500); 

		}
		DB::commit();
		


		return response()->json(['message'=>"Sale Order Deleted",'action'=>'redirect','do'=>url('/sale_orders/')],200);
	}

	public function json(Request $request)
	{
		//ini set execution time
		ini_set('max_execution_time',5000);
		$isAdmin = is_admin();
		// $data = InvoiceExtendedView::where('type','sale_order')->selectRaw("id, bill_number, customer_name, customer_city, customer_address,sale_order_id, sale_order_status, balance, invoice_id, posted, date, total, added_by, edited_by");
		$data = SaleOrder::join('invoice','invoice.id','=','sale_orders.invoice_id')
						->leftjoin('customer','customer.id','=','sale_orders.customer_id')
						->leftjoin(DB::raw("(select calculate_balance(id) as balance, id as invo_id from invoice) as balance_view"),'balance_view.invo_id','=','sale_orders.invoice_id')
						->selectRaw('sale_orders.*,invoice.total,invoice.bill_number,invoice.sales_person as manual_sales_person,invoice.added_by as added_by,invoice.edited_by as edited_by,customer.name as name, customer.city as city, balance_view.balance as balance');

		$user = User::pluck('name', 'id')->toArray();

		//replace date in request to sale_order.date

		$json = processJsonFilters($data, $request, ['date'=>'sale_orders.date','PENDING'=>SaleOrder::PENDING, 'ACTIVE'=>SaleOrder::ACTIVE,'QUOTATION'=>SaleOrder::QUOTATION, 'COMPLETED'=>SaleOrder::COMPLETED, 'balance' => 'balance_view.balance']);

		if ($request->has('group'))
		{
			$group = json_decode($request->group);

			foreach($json['data'] as $k => $d)
			{
				if ($group[0]->selector == "status")
				{
					$json['data'][$k]['key'] = saleOrderStatus($d['key']);
				}
			}
			return $json;
		}
		
		// self processing JSON data
		foreach($json as $val)
		{
			if (isset($val->added_by))
			{
				$val->added_by = (!empty($user[$val->added_by])) ? $user[$val->added_by] : "-";
			}

			if (isset($val->edited_by))
			{
				$val->updated_by = (!empty($user[$val->edited_by])) ? $user[$val->edited_by] : "-";
			}
			$val->posted = ($val->posted) ? "<a class='btn btn-success'>Yes</a>" : "<a class='btn btn-warning' onclick='changeStatus(".$val->id.")'>No</a>";
			$val->status = saleOrderStatusHtml($val->status);
			$profit_button = "";
    			if ($val->posted && $isAdmin) {
    				$profit_button = '<a title="Profit" class="btn btn-primary px-3" href="'.url('reports/profit').'?sale_id='.$val->invoice_id.'" target="_blank"><i class="fas fa-chart-line"></i></a><br>';
    			}
    			$val->action = '
				<a title="Details" class="btn btn-primary px-3" href="'. route('sale_orders.show', $val->id) .'"><i class="fas fa-info"></i></a>
				<a title="View Invoice" class="btn btn-default px-3" target="_blank" href="'.route('invoices.show', $val->invoice_id) .'"><i class="fas fa-file-invoice"></i></a>
				<a title="View Receipt" class="btn btn-success px-3" target="_blank" href="'.url('smallInvoice/'. $val->invoice_id) .'"><i class="fas fa-receipt"></i></a>
				<form action="'. route('sale_orders.destroy', $val->id) .'" method="POST" style="display: inline;" >
						<input type="hidden" name="_method" value="DELETE">
							<input type="hidden" name="_token" value="'. csrf_token() .'">
							<button type="submit" class="btn btn-danger px-3" onclick="return confirm(\'Delete? Are you sure?\');"><i class="fas fa-trash"></i></button>
						</form>
				'.$profit_button;
		}

		
		$salesPersons = SalesPerson::pluck('name','id');
	
		foreach($json as $key => $val){
			$json[$key]['sales_person'] = ($val->manual_sales_person)?$val->manual_sales_person:(($val->sales_people_id)?$salesPersons[$val->sales_people_id]:'-');
		}

		return response()->json(['data'=>$json,'totalCount'=>$data->count()],200);
	}


	public function datatables()
    {
		$isAdmin = is_admin();
		$user = User::pluck('name', 'id')->toArray();
    	return Datatables::of(SaleOrder::with('customer')->with('invoice'))
    	->edit_column('customer.name', function($row) {
    		return ($row->customer) ? "<a href='/customers/{$row->customer->id}' target='_blank'>{$row->customer->name}<br><small>{$row->customer->city}</small></a>" : "N/A";
    	})->edit_column('status', function($row) {
    		return saleOrderStatusHtml($row->status);
    	})->add_column('total', function($row) {
				if($row->invoice->total > 0){
					return number_format($row->invoice->total, 2);
				}
				else{
					$worth = 0;
					foreach ($row->invoice->orders as $key => $value) {
						# code...
						$worth += $value->salePrice*$value->quantity;
					}
					$worth += $row->invoice->shipping + $row->invoice->tax - $row->invoice->discount;
					$worth = round($worth);
						return number_format($worth, 2);		
				}

    	})->edit_column('date', function($row) {
    		return (strtotime($row->date)) ? date_format_app($row->date) : "-";
    	})->edit_column('posted', function($row) {
    		return ($row->posted) ? "<a class='btn btn-success'>Yes</a>" : "<a class='btn btn-warning' onclick='changeStatus(".$row->id.")'>No</a>";
    	})->add_column('actions', function($row) use ($isAdmin) {
    			$profit_button = "";
    			if ($row->posted && $isAdmin) {
    				$profit_button = '<a class="btn btn-xs btn-primary" href="'.url('reports/profit').'?sale_id='.$row->invoice_id.'" target="_blank">View Profit Details</a>';
    			}
    			return '<a class="btn btn-xs btn-primary" href="'. route('sale_orders.show', $row->id) .'"><i class="glyphicon glyphicon-eye-open"></i> View Details</a>
				<a class="btn btn-xs btn-success" target="_blank" href="'.route('invoices.show', $row->invoice_id) .'"><i class="glyphicon glyphicon-eye-open"></i> View Invoice</a>
				<a class="btn btn-xs btn-success" target="_blank" href="'.url('purchaseInvoice/'. $row->invoice_id, 1) .'"><i class="glyphicon glyphicon-eye-open"></i> Print Purchase Order</a>
				<a class="btn btn-xs btn-success" href="'.url('smallInvoice/'. $row->invoice_id) .'"><i class="glyphicon glyphicon-eye-open"></i> Minor Invoice</a>
						<form action="'. route('sale_orders.destroy', $row->id) .'" method="POST" style="display: inline;" >
							<input type="hidden" name="_method" value="DELETE">
							<input type="hidden" name="_token" value="'. csrf_token() .'">
							<button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Delete? Are you sure?\');"><i class="glyphicon glyphicon-trash"></i> Delete</button>
						</form>'.$profit_button;
    		})->add_column('added_by', function($row) use ($user) {
				return (!empty($user[$row->invoice->added_by])) ? $user[$row->invoice->added_by] : "-";
			})->add_column('updated_by', function($row) use ($user) {
				return (!empty($user[$row->invoice->edited_by])) ? $user[$row->invoice->edited_by] : "-";
    		})
    	->make(true);
    }


		public function updateSaleOrderCompletionDate(Request $request){
			if (!is_allowed('sale-edit'))
			{
				return response(['message'=>'Unauthorised'],500);
			}
	
			$sale_order = SaleOrder::whereId($request->id)->first();
	
			try{
				DB::beginTransaction();

				$sale_order->completion_date = $request->completion_date;
				$sale_order->save();
				
			}catch(Exception $e)
			{
				DB::rollBack();
				return response()->json(['message' => $e->getMessage()], 500);
			}
			DB::commit();
			return response()->json(['message'=>"Sale Order Completion Date updated",'action'=>'redirect','do'=>url('/sale_orders/'.$sale_order->id)],200);
	
		}

}
