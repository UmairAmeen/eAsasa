<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;


use App\Http\Controllers\Controller;

use App\Purchase;
use App\Products;
use App\Supplier;
use App\Invoice;
use App\Transaction;
use App\Order;
use App\StockManage;
use App\Customer;
use App\User;
use App\Warehouse;

use View;
use DB;
use Yajra\Datatables\Facades\Datatables;


class PurchaseController extends Controller {

	public function __construct() {
		\View::share('title',"Purchase");
		View::share('load_head',true);
		View::share('purchase_menu',true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if (!is_allowed('purchase-list')) {
			return redirect('/');
		}
		return view('purchases.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		if (!is_allowed('purchase-create')) {
			return redirect('/');
		}
		$products = Products::lists('name','id');
		$suppliers = Supplier::all();
		return view('purchases.create',compact('products','suppliers','request'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(PurchaseRequest $request)
	{
		if (!is_allowed('purchase-create')) {
			return response(['message'=>'Unauthorised'],500);
		}
		$invoice_id = 0;
		DB::beginTransaction();
		try {
			$product_ids = $request->product;
			$warehouse_ids = $request->warehouse;
			$quantity = $request->quantity;
			$saleprice = $request->sale_price;
			$stype = $request->stype;			
			$invoice = new Invoice();
			$invoice->description = $request->description;
			$invoice->shipping = $request->shipping;
			$invoice->supplier_id = $request->customer;
			$invoice->discount = $request->discount;
			$invoice->total = $request->total;
			$invoice->type="purchase";
			$invoice->bill_number = $request->bill_number;
			$invoice->date = date('Y-m-d',strtotime($request->date));
			$invoice->added_by = \Auth::id();
			$invoice->save();
			$invoice_id = $invoice->id;
			$worth = 0;
			foreach ($product_ids as $key => $value) {
				$order = new Order();
				$order->invoice_id = $invoice_id;
				$order->product_id = $product_ids[$key];
				if(session()->get('settings.products.enable_advance_fields')){
					Products::whereId($product_ids[$key])->update(['salePrice' => $request->s_price[$key],'min_sale_price' => $request->minSalePrice[$key]]);
				}
				$order->salePrice = $saleprice[$key];
				$order->quantity = $quantity[$key];
				$order->save();
				$sp_type = "purchase";
				if($order->quantity < 0) {
					$sp_type = "out";
				}
				$worth += $order->salePrice*$order->quantity;
				//update rate for user
				$supplier_price_id = supplier_price_record($invoice->date, $saleprice[$key], $request->customer, $product_ids[$key]);
				// dd($supplier_price_id);
				//end update rate for user
				if (strcasecmp($stype[$key] ,"damage") != 0) {
					$sale = new StockManage();
					$sale->date = date('Y-m-d',strtotime($request->date));
					$sale->type = $sp_type;
					$sale->batch_id = $supplier_price_id;
					$sale->supplier_id = $request->customer;
					$sale->purchase_id = $order->id;
					$sale->product_id = $product_ids[$key];
					$sale->warehouse_id = $warehouse_ids[$key];
					$sale->quantity = abs($quantity[$key]);
					$sale->added_by = \Auth::id();
					//Stock Change Disabled by Ali Shan on 04 Sep 2019
					//Stock Change Enabled by Ali Shan on 23 Dec 2019
					$sale->save();
				}
			}//endforeach here
			if(!$invoice->shipping)	{ $invoice->shipping = 0;}
			if(!$invoice->tax)		{ $invoice->tax = 0;}
			if(!$invoice->discount)	{ $invoice->discount = 0;}
			$worth += $invoice->shipping + $invoice->tax - $invoice->discount;
			$invoice->total = $worth;
			$invoice->save();
			// \Log::info("T".$worth);
			$transaction = new Transaction;
			$transaction->date = date('Y-m-d',strtotime($request->date));
			$transaction->supplier_id = $request->customer;
			$transaction->amount = abs($worth);
			$transaction->type = ($worth<0)?"out":"in";//credit
			$transaction->invoice_id = $invoice_id;
			$transaction->added_by = \Auth::id();
			$transaction->save();
			if ($request->payment) {
				$transactions = new Transaction;
				$transactions->date = $invoice->date;
				$transactions->type = "out";
				$transactions->invoice_id = $invoice->id;
				$transactions->amount = $request->payment;
				$transactions->payment_type = $request->payment_type?:"cash";
				$transactions->supplier_id = $request->customer;
				$transactions->added_by = \Auth::id();
				$transactions->save();
			} elseif ($request->mark_paid) {
				$transactions = new Transaction;
				$transactions->date = $invoice->date;
				$transactions->type = ($worth<0)?"in":"out";//debit
				$transactions->invoice_id = $invoice->id;
				$transactions->amount = abs($worth);
				$transactions->payment_type = "cash";
				$transactions->supplier_id = $request->customer;
				$transactions->added_by = \Auth::id();
				$transactions->save();
			}			
		} catch(\Illuminate\Database\QueryException $e) {
			DB::rollBack();			
			return response()->json(['message' => $e->getMessage()], 403);
		}
		DB::commit();
		if ($request->print == "sm") {
			return response()->json(['message'=>"Purchase Added Successfully",'action'=>'redirect','do'=>url('/smallInvoice/'.$invoice->id)],200);
		}
		if ($request->print == "lg") {
			return response()->json(['message'=>"Purchase Added Successfully",'action'=>'redirect','do'=>url('/invoices/'.$invoice->id)],200);
		}
		return response()->json(['message'=>"Purchase Added Successfully",'action'=>'redirect','do'=>url('/purchases/')],200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		if (!is_allowed('purchase-list')) {
			return redirect('/');
		}
		$purchase = Purchase::findOrFail($id);
		return view('purchases.show', compact('purchase'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if (!is_allowed('purchase-edit')) {
			return redirect('/');
		}
		$invoice = Invoice::whereId($id)->first();
		$suppliers = Supplier::all();
		$warehouses = Warehouse::orderBy('name','desc')->get();
		return view('purchases.edit', compact('invoice','suppliers','warehouses'));
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
		if (!is_allowed('purchase-edit')) {
			return response(['message'=>'Unauthorised'],500);
		}
		$invoice_id = 0;
		DB::beginTransaction();
		try {
			// dd($request->all());
			$product_ids = $request->product;
			$warehouse_ids = $request->warehouse;
			$quantity = $request->quantity;
			$saleprice = $request->sale_price;
			$stype = $request->stype;
			$invoice = Invoice::whereId($id)->first();
			$invoice->description = $request->description;
			$invoice->shipping = $request->shipping;
			$invoice->supplier_id = $request->customer;
			$invoice->discount = $request->discount;
			$invoice->total = $request->total;
			// $invoice->type="sale";
			$invoice->edited_by = \Auth::id();
			$invoice->bill_number = $request->bill_number;
			$invoice->date = date('Y-m-d',strtotime($request->date));
			$invoice->save();
			$invoice_id = $invoice->id;
			$worth = 0;
			$all_order_ids = Order::where('invoice_id', $invoice->id)->pluck('id');
			Order::where('invoice_id', $invoice->id)->delete();
			StockManage::whereIn('purchase_id', $all_order_ids)->delete();
			foreach ($product_ids as $key => $value) {				
				$order = new Order();
				$order->invoice_id = $invoice_id;
				$order->product_id = $product_ids[$key];
				if(session()->get('settings.products.enable_advance_fields')){
					Products::whereId($product_ids[$key])->update(['salePrice' => $request->s_price[$key],'min_sale_price' => $request->minSalePrice[$key]]);
				}
				$order->salePrice = $saleprice[$key];
				$order->quantity = $quantity[$key];
				$order->save();
				$sp_type = "purchase";
				if($order->quantity < 0) {
					$sp_type = "out";
				}
				$worth += $order->salePrice*$order->quantity;
				//update rate for user
				supplier_price_record(date('Y-m-d',strtotime($request->date)), $saleprice[$key], $invoice->supplier_id, $product_ids[$key]);
				//end update rate for user
				if ($warehouse_ids[$key] && strcasecmp($stype[$key] ,"damage") != 0) {
					$sale = new StockManage();
					$sale->date = date('Y-m-d',strtotime($request->date));
					$sale->type = $sp_type;
					$sale->supplier_id = $request->customer;
					$sale->purchase_id = $order->id;
					$sale->product_id = $product_ids[$key];
					$sale->warehouse_id = $warehouse_ids[$key];
					$sale->quantity = abs($quantity[$key]);
					$sale->added_by = \Auth::id();
					$sale->save();
				}
			}//endforeach here
			if(!$invoice->shipping)	{ $invoice->shipping = 0;}
			if(!$invoice->tax)		{ $invoice->tax = 0;}
			if(!$invoice->discount)	{ $invoice->discount = 0;}
			$worth += $invoice->shipping + $invoice->tax - $invoice->discount;
			// Update Customer Details plus add edited by on All Transactions related to this invoice
			$transaction = Transaction::where('invoice_id', $invoice_id)->update([
				'supplier_id'=>$request->customer, 
				'edited_by'=>\Auth::id(),
				'date'=>date('Y-m-d',strtotime($request->date)),
				'amount'=>abs($worth)
			]);
			//update amount, date for this purchase invoice [in]
			$invoice->total = $worth;
			$invoice->save();
		} catch(\Illuminate\Database\QueryException $e) {
			DB::rollBack();
			return response()->json(['message' => $e->getMessage()], 403);
		}
		DB::commit();
		if ($request->print == "sm") {
			return response()->json(['message'=>"Purchase Updated Successfully",'action'=>'redirect','do'=>url('/smallInvoice/'.$invoice->id)],200);
		}
		if ($request->print == "lg") {
			return response()->json(['message'=>"Purchase Updated Successfully",'action'=>'redirect','do'=>url('/invoices/'.$invoice->id)],200);
		}
		return response()->json(['message'=>"Purchase Updated Successfully",'action'=>'redirect','do'=>url('/purchases/')],200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if (!is_allowed('purchase-delete')) {
			return response(['message'=>'Unauthorised'],500);
		}
		$purchase = Purchase::findOrFail($id);
		$purchase->delete();
		return redirect()->route('purchases.index')->with('message', 'Item deleted successfully.');
	}

	public function datatables() {
		$can_view_purchase_price = is_allowed('product-show-purchase-price');
		$users = User::pluck('name', 'id')->toArray();
    	return Datatables::of(Invoice::where('type','purchase')->with("supplier"))
		->editColumn('total',function($row) use($can_view_purchase_price) {
			return (!$can_view_purchase_price) ? 0 :  $row->total + 0;
		})->edit_column('date', function($row) {
			return date_format_app($row->date);
		})->add_column('options', function($row) {
			return '<a class="btn btn-xs btn-default" href="'.route('purchases.edit', $row->id).'"><i class="glyphicon glyphicon-edit"></i> Edit</a>
					<a class="btn btn-xs btn-primary" target="_blank" href="'.route('invoices.show', $row->id).'"><i class="glyphicon glyphicon-eye-open"></i> View</a>
					<a class="btn btn-xs btn-success" target="_blank" href="'.url('smallInvoice/'. $row->id) .'"><i class="glyphicon glyphicon-eye-open"></i> Minor Invoice</a>
					<form action="'.route('invoices.destroy', $row->id).'" method="POST" style="display: inline;"><div id="log"></div><input type="hidden" name="_method" value="DELETE"><input type="hidden" name="_token" value="'. csrf_token().'"> <button onclick="return (confirm(\'Delete? Are you sure?\'))" type="submit" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Delete</button></form>';
		})->add_column('added_by', function($row) use($users) {
			return (!empty($users[$row->added_by]))?$users[$row->added_by] : "-";
		})->add_column('updated_by', function($row) use($users) {
			return (!empty($users[$row->edited_by])) ? $users[$row->edited_by] : "-";
		})->make(true);
    }

	public function pos(Request $request) {
    	$customers = Customer::all();
    	$warehouses = Warehouse::orderBy("name","desc")->get();
    	return view("pos.pos_purchase",compact('customers','warehouses'));
    }
}