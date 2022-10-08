<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\CreateSaleRequest;

use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use App\Order;
use App\Invoice;
use App\StockManage;
use App\Transaction;
use App\Rates;
use App\Refund;
use App\Http\Controllers\SMController;
use App\Customer;
use App\Products;
use App\Warehouse;
use Carbon\Carbon;
// use App\Sale;

use Illuminate\Http\Request;
use View;
use DB;
use Exception;

class SaleController extends Controller
{

	public function __construct()
	{
		View::share('title', "Sales");
		View::share('load_head', true);
		View::share('sales_menu', true);
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
			// return response(['message'=>'Unauthorised'],500);
		}

		$sales = Invoice::where('type', 'sale')->get();
		return view('sales.index', compact(['sales']));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		if (!is_allowed('sale-create')) {
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		// $orders = Order::lists('product_id','id');
		// return view('sales.create',compact('request'));
		$invoice = Invoice::orderBy('id', 'DESC')->first();
		$presentInvoiceNo = 1 + ($invoice->id);
		return view('sales.create', compact('request', 'presentInvoiceNo'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(CreateSaleRequest $request)
	{
		// dd($request->all());
		if (!is_allowed('sale-create')) {
			// return redirect('/');
			return response(['message' => 'Unauthorised'], 500);
		}
		if (!$request->product || !is_array($request->product) || count($request->product) == 0) {
			return response(['message' => 'Please add some products'], 422);
		}
		if ($request->custom_field) {
			$array_c = array_combine($request->custom_labels, $request->custom_field);
			$encoded = json_encode($array_c);
		}

		$is_fbr = session()->get('settings.fbr.is_fbr_enable')?:'';
		$fbr_pos_id = session()->get('settings.fbr.fbr_pos_id')?:'';
		$fbr_url = session()->get('settings.fbr.fbr_url')?:'';

		if($is_fbr == 1 && ($fbr_url == '' || $fbr_pos_id == ''))
		{
			return response(['message'=>'Please Compleate FBR Fields OR Disable "FBR Invoice" From Settings'],500);
		}
		
		$invoice_id = 0;
		DB::beginTransaction();
		try {

			$product_ids = $request->product;
			$warehouse_ids = $request->warehouse;
			$quantity = $request->quantity;
			$saleprice = $request->sale_price;
			$discounted_price = $request->discounted_price;
			$stype = $request->stype;
			$total_wo_discount_shipping = ($request->total + $request->discount) - $request->shipping;

			$invoice = new Invoice();
			$invoice->description = $request->description;
			$invoice->shipping = $request->shipping;
			$invoice->custom_inputs = ($encoded) ? $encoded : null;
			$invoice->customer_id = $request->customer;
			$invoice->bank_id = (session()->get('settings.sales.is_bank_enable_in_direct_sale_invoice') == '1') ? $request->bank : null;
			$invoice->discount = $request->discount;
			$invoice->total = $request->total;
			$invoice->type = "sale";
			$invoice->bill_number = $request->bill_number;
			$invoice->date = date('Y-m-d', strtotime($request->date));
			$invoice->added_by = \Auth::id();
			$invoice->save();
			$invoice_id = $invoice->id;

			if($is_fbr == 1)
			{
				$buyer = Customer::where('id', $request->customer)->get()->first();

				$fbr_data["InvoiceNumber"] = ''; //blank
				$fbr_data["POSID"] = $fbr_pos_id; //c
				$fbr_data["USIN"] = $invoice_id ?: 1111; //c
				$fbr_data["DateTime"] = Carbon::now()->format("Y-m-d H:i:s"); //c
				$fbr_data["BuyerNTN"] = $buyer->ntn ?: 000000; //o 
				$fbr_data["BuyerCNIC"] = $buyer->cnic ?: 1234567890123; //o 
				$fbr_data["BuyerName"] = $buyer->name; //o
				$fbr_data["BuyerPhoneNumber"] = $buyer->phone ?: 03000000000; //o
				$fbr_data["Discount"] = $request->discount ?: 0; //o
				$fbr_data["TotalQuantity"] = array_sum($request->quantity); //c
				$fbr_data["TotalSaleValue"] = $total_wo_discount_shipping; //c
				$fbr_data["TotalTaxCharged"] = ((session()->get('settings.sales.tax_percentage')/100) * $total_wo_discount_shipping); //c
				$fbr_data["TotalBillAmount"] = ((session()->get('settings.sales.tax_percentage')/100) * $total_wo_discount_shipping) + $total_wo_discount_shipping; //c
				$fbr_data["FurtherTax"] = $request->futher_tax ?: 0; //o
				$fbr_data["PaymentMode"] = 1;  //c
				$fbr_data["RefUSIN"] = ''; //o
				$fbr_data["InvoiceType"] = 1; //c -- new field in form

				$fbr_data["Items"] = [];
				foreach ($request->product as $key => $pro_id) {
					$product = Products::where('id', $pro_id)->get()->first();
					$fbr_data["Items"][] = [ //c
						'ItemCode' => ($product->code) ? $product->code : 0000, //c
						'ItemName' => $product->name, //c
						'Quantity' => $request->quantity[$key], //c
						'PCTCode' => ($product->pct_code) ? $product->pct_code : 0000, //c  
						'TaxRate' => session()->get('settings.sales.tax_percentage') ? : 0, //c  
						'SaleValue' => $request->sale_price[$key], //c
						'TaxCharged' => ((session()->get('settings.sales.tax_percentage')/100) * ($request->sale_price[$key] * $request->quantity[$key])) ? : 0, //c
						'TotalAmount' => ((session()->get('settings.sales.tax_percentage')/100) * ($request->sale_price[$key] * $request->quantity[$key])) + ($request->sale_price[$key] * $request->quantity[$key]), //c
						'Discount' => 0, //o
						'FurtherTax' => $request->p_f_tax ?: 0, //o
						'InvoiceType' => 1, //c -- add field in form drop down
						'RefUSIN' => '', //O
					];
				}
				// dd($fbr_data);

				$token_fbr = '1298b5eb-b252-3d97-8622-a4a69d5bf818';
				try {
					$client = new Client(); //GuzzleHttp\Client
					// $result = $client->post('http://localhost:8524/api/IMSFiscal/GetInvoiceNumberByModel', [
					$result = $client->post($fbr_url, [
						'verify' => false,
						'headers' => [
							'Authorization' => 'Bearer ' . $token_fbr,
							'content-type' => 'application/json'],
						'body' =>  json_encode($fbr_data, true)
					]);
					// dd(strval($result->getBody()));
					$fbr_invoice = json_decode($result->getBody())->InvoiceNumber;
					// dd($fbr_invoice);
				} catch (Exception $e) {
					dd($e->getMessage());
				}
				$invoice->fbr_invoice = $fbr_invoice;
				$invoice->tax = ((session()->get('settings.sales.tax_percentage')/100) * $total_wo_discount_shipping);
				$invoice->save();

				$invoice_id = $invoice->id;
			}
			
			$worth = 0;

			foreach ($product_ids as $key => $value) {

				$order = new Order();
				$order->invoice_id = $invoice_id;
				$order->product_id = $product_ids[$key];
				$order->original_price = $saleprice[$key];
				$order->salePrice = ($discounted_price[$key])??$saleprice[$key];
				$order->purchasePrice = getSupplierRateOnBatchId($product_ids[$key], $request->batch[$key]);
				$order->quantity = $quantity[$key];
				$order->save();

				$sp_type = "sale";

				if ($order->quantity < 0) {
					$sp_type = "in";
				}

				
				$worth += $order->salePrice*$order->quantity;
				//worth calculated acurately


				//update rate for user
				$rate = Rates::FirstOrNew(['customer_id' => $request->customer, 'product_id' => $value]);
				$rate->salePrice = $saleprice[$key];
				$rate->save();
				//end update rate for user
				if (strcasecmp($stype[$key], "damage") != 0) {
					$sale = new StockManage();
					$sale->date = date('Y-m-d', strtotime($request->date));
					$sale->type = $sp_type;
					$sale->customer_id = $request->customer;
					$sale->sale_id = $order->id;
					$sale->batch_id = $request->batch[$key];
					$sale->product_id = $product_ids[$key];
					$sale->warehouse_id = $warehouse_ids[$key];
					$sale->quantity = abs($quantity[$key]);
					$sale->added_by = \Auth::id();
					//Stock Change Disabled by Ali Shan on 04 Sep 2019
					//Stock Change Enabled by Ali Shan on 23 Dec 2019
					$sale->save();
				}
			} //endforeach here

			if (!$invoice->shipping) {
				$invoice->shipping = 0;
			}
			if (!$invoice->tax) {
				$invoice->tax = 0;
			}
			if (!$invoice->discount) {
				$invoice->discount = 0;
			}

			$worth += $invoice->shipping + $invoice->tax - $invoice->discount;
			
// \Log::info("T".$worth);
			$transaction = new Transaction;
			$transaction->date = date('Y-m-d', strtotime($request->date));
			$transaction->customer_id = $request->customer;
			$transaction->amount = abs($worth);
			$transaction->type = ($worth > 0) ? "out" : "in"; //credit
			$transaction->invoice_id = $invoice_id;
			$transaction->added_by = \Auth::id();
			$transaction->save();
			Invoice::where('id',$invoice_id)->update(['advance' => $worth]);

			if ($request->payment){
					$transactions = new Transaction;
					$transactions->date = $invoice->date;
					$transactions->type = "in";
					$transactions->invoice_id = $invoice->id;
					$transactions->amount = $request->payment;
					$transactions->payment_type = $request->payment_type?:"cash";
					$transactions->bank = $request->bank?$request->bank:Null;
					$transactions->customer_id = $invoice->customer_id;
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
			} else if ($request->mark_paid) {

				$transactions = new Transaction;
				$transactions->date = $invoice->date;
				$transactions->type = ($worth > 0) ? "in" : "out"; //debit
				$transactions->invoice_id = $invoice->id;
				$transactions->amount = abs($worth);
				$transactions->payment_type = "cash";
				$transactions->bank = $request->bank?$request->bank:Null;
				$transactions->customer_id = $request->customer;
				$transactions->added_by = \Auth::id();
				$transactions->save();
				Invoice::where('id',$invoice_id)->update(['advance' => $worth]);
			}
		} catch (\Illuminate\Database\QueryException $e) {
			DB::rollBack();

			return response()->json(['message' => $e->getMessage()], 403);
		}
		DB::commit();

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

		if ($request->print == "sm")
		{
			return response()->json(['message'=>"Sale Added Successfully",'sms_message' => $response,'action'=>'reset','do'=>url('/smallInvoice/'.$invoice->id)],200);
		}
		if ($request->print == "lg")
		{
			return response()->json(['message'=>"Sale Added Successfully",'sms_message' => $response,'action'=>'reset','do'=>url('/invoices/'.$invoice->id)],200);
		}
		return response()->json(['message'=>"Sale Added Successfully",'sms_message' => $response,'action'=>'redirect','do'=>url("sales/create")],200);
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
			// return response(['message'=>'Unauthorised'],500);
		}
		$sale = Sale::findOrFail($id);

		return view('sales.show', compact('sale'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if (!is_allowed('sale-edit')) {
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$sale_order = Invoice::whereId($id)->where('type', 'sale')->first();
		$warehouses = Warehouse::all();
		$customers = Customer::all();
		$transaction = Transaction::where(['invoice_id' => $sale_order->id, 'type' => 'in'])
		->orderBy('id', 'asc')->first();

		return view('sales.edit', compact('sale_order','warehouses','customers','transaction'));
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
		if (!is_allowed('sale-edit')) {
			// return redirect('/');
			return response(['message' => 'Unauthorised'], 500);
		}
		if ($request->custom_field) {
			$array_c = array_combine($request->custom_labels, $request->custom_field);
			$encoded = json_encode($array_c);
		}
		$invoice_id = 0;
		DB::beginTransaction();
		try {
			$product_ids = $request->product;
			$warehouse_ids = $request->warehouse;
			$quantity = $request->quantity;
			$discounted_price = $request->discounted_price;
			$saleprice = $request->sale_price;
			$stype = $request->stype;

			$invoice = Invoice::whereId($id)->first();
			$invoice->description = $request->description;
			$invoice->shipping = $request->shipping;
			$invoice->bank_id = (session()->get('settings.sales.is_bank_enable_in_direct_sale_invoice') == '1') ? $request->bank : null;
			$invoice->custom_inputs = ($encoded)?$encoded:null;
			$invoice->customer_id = $request->customer;
			$invoice->discount = $request->discount;
			$invoice->total = $request->total;
			$invoice->edited_by = \Auth::id();
			// $invoice->type="sale";
			$invoice->bill_number = $request->bill_number;
			$invoice->date = date('Y-m-d', strtotime($request->date));
			$invoice->save();

			$invoice_id = $invoice->id;




			$worth = 0;
			$all_order_ids = Order::where('invoice_id', $invoice->id)->pluck('id');
			Order::where('invoice_id', $invoice->id)->delete();
			StockManage::whereIn('sale_id', $all_order_ids)->delete();
			foreach ($product_ids as $key => $value) {

				$order = new Order();
				$order->invoice_id = $invoice_id;
				$order->product_id = $product_ids[$key];
				$order->salePrice = $saleprice[$key];
				$order->original_price = ($discounted_price[$key])??$saleprice[$key];
				$order->quantity = $quantity[$key];
				$order->save();

				$sp_type = "sale";

				if ($order->quantity < 0) {
					$sp_type = "in";
				}

				$worth += $order->salePrice*$order->quantity;



				//update rate for user
				// $rate = Rates::FirstOrNew(['customer_id'=>$request->customer, 'product_id'=>$value]);
				// $rate->salePrice = $saleprice[$key];
				// $rate->save();
				//end update rate for user
				if ($warehouse_ids[$key] && strcasecmp($stype[$key], "damage") != 0) {
					$sale = new StockManage();
					$sale->date = date('Y-m-d', strtotime($request->date));
					$sale->type = $sp_type;
					$sale->customer_id = $request->customer;
					$sale->sale_id = $order->id;
					$sale->product_id = $product_ids[$key];
					$sale->warehouse_id = $warehouse_ids[$key];
					$sale->quantity = abs($quantity[$key]);
					$sale->added_by = \Auth::id();
					$sale->save();
				}
			} //endforeach here

			if (!$invoice->shipping) {
				$invoice->shipping = 0;
			}
			if (!$invoice->tax) {
				$invoice->tax = 0;
			}
			if (!$invoice->discount) {
				$invoice->discount = 0;
			}

			$worth += $invoice->shipping + $invoice->tax - $invoice->discount;

			//update invoice transaction details FOR CREDIT AND DEBIT BOTH
			$transaction = Transaction::where('invoice_id', $invoice_id)->update(['date' => date('Y-m-d', strtotime($request->date)), 'customer_id' => $request->customer]);

			//update sale amount in transaction

			$transaction = Transaction::where('invoice_id', $invoice_id)->where('type', 'out')->update(['amount' => abs($worth)]);

			if (isset($request->payment)) {
				$transaction = Transaction::firstOrNew(['invoice_id' => $invoice_id, 'type' => 'in']);
				$transaction->type = "in";
				$transaction->amount = $request->payment;
				$transaction->date = date('Y-m-d',strtotime($request->date));
				$transaction->bank = $request->bank?$request->bank:Null;
				$transaction->invoice_id = $invoice_id;
				$transaction->customer_id = $request->customer;
				if ($transaction->id || $request->payment) {
					$transaction->save();
				}
				//update payment amount in transaction
			}


			$invoice->total = $worth;
			$invoice->save();
		} catch (\Illuminate\Database\QueryException $e) {
			DB::rollBack();

			return response()->json(['message' => $e->getMessage()], 403);
		}
		DB::commit();
		if ($request->print == "sm") {
			return response()->json(['message' => "Sale Updated Successfully", 'action' => 'redirect', 'do' => url('/smallInvoice/' . $invoice->id)], 200);
		}
		if ($request->print == "lg") {
			return response()->json(['message' => "Sale Updated Successfully", 'action' => 'redirect', 'do' => url('/invoices/' . $invoice->id)], 200);
		}
		return response()->json(['message' => "Sale Updated Successfully", 'action' => 'redirect', 'do' => url('/sales/')], 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if (!is_allowed('sale-delete')) {
			// return redirect('/');
			return response(['message' => 'Unauthorised'], 500);
		}
		try {
			DB::beginTransaction();
			$sale = Invoice::whereId($id)->delete();
			DB::commit();
			return response("success", 200);
		} catch (Exception $e) {
			DB::rollBack();
			return response($e->getMessage(), 200);
		}


		return redirect()->route('sales.index')->with('message', 'Item deleted successfully.');
	}

	public function sale_return($id)
	{
		if (!is_allowed('sale-edit')) {
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$sale_order = Invoice::whereId($id)->where('type', 'sale')->first();
		$warehouses = Warehouse::all();
		$customers = Customer::all();
		$transaction = Transaction::where(['invoice_id' => $sale_order->id, 'type' => 'in'])
		->orderBy('id', 'asc')->first();

		return view('sales.return', compact('sale_order','warehouses','customers','transaction'));
	}


	public function datatables()
	{
		$p = Invoice::where('type', 'sale')->with('customer');
		return Datatables::of($p)
			->edit_column('customer.name', function ($row) {
				if ($row->customer) {

					return "<a href='/customers/" . $row->customer->id . "' target='_blank'>" . $row->customer->name . "<br><small>" . $row->customer->city . "</small></a>";
				}
				return "N/A";
			})
			->add_column('options', function ($row) {

				return '<a class="btn btn-xs btn-primary" target="_blank" href="' . route('invoices.show', $row->id) . '"><i class="glyphicon glyphicon-eye-open"></i> View Invoice</a>
    			<a class="btn btn-xs btn-success" target="_blank" href="' . url('smallInvoice/' . $row->id) . '"><i class="glyphicon glyphicon-eye-open"></i> Minor Invoice</a>
                                    <a class="btn btn-xs btn-warning" href="' . route('sales.edit', $row->id) . '"><i class="glyphicon glyphicon-edit "></i> Edit</a>
									<a class="btn btn-xs btn-warning" href="' . route('sales.return', $row->id) . '"><i class="glyphicon glyphicon-retweet "></i> Sale Return</a>
                                    <form action="' . route('invoices.destroy', $row->id) . '" method="POST" style="display: inline;">
                                        <div id="log"></div>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="' .  csrf_token() . '">
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Do you really want to delete the invoice?\');"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                                    </form>';
			})
			->edit_column('total', function ($row) {
				$output = formating_price($row->total);
				if ($row->total > 0) {
					$p_made = payment_for_invoice($row->id);
				} else {
					$p_made = payment_for_invoice($row->id, 'out');
				}
				if ($p_made) {
					$output .= "<br><small>PAID " . check_negative($p_made) . "</small>";
				}
				return $output;
			})
			->add_column('added_by', function ($row) {

				return ($row->added_user) ? $row->added_user->name : "-";
			})
			->add_column('updated_by', function ($row) {
				return ($row->edited_user) ? $row->edited_user->name : "-";
			})
			->make(true);
	}


	public function pos(Request $request)
	{
		if (!is_allowed('sale-create')) {
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$customers = Customer::all();
		return view("pos.pos", compact('customers'));
	}


	public function pos_direct(Request $request)
	{
		if (!is_allowed('sale-create')) {
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$customers = Customer::all();
		$warehouses = Warehouse::orderBy("name", "desc")->get();
		return view("pos.pos_direct", compact('customers', 'warehouses'));
	}



	public function direct_barcode(Request $request)
	{
		if (!is_allowed('sale-create')) {
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$customers = Customer::all();
		$warehouses = Warehouse::orderBy("name", "desc")->get();
		return view("pos.pos_direct_barcode", compact('customers', 'warehouses'));
	}
}
