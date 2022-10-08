<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\OrderRequest;

use App\Http\Controllers\Controller;

use App\Order;
use App\Product;
use App\Customer;
use Illuminate\Http\Request;

class OrderController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$orders = Order::orderBy('id', 'desc')->paginate(10);

		return view('orders.index', compact('orders'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$products = Product::lists('name','id');
		$customers = Customer::lists('name','id');

		return view('orders.create',compact(array('products','customers')));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(OrderRequest $request)
	{
		$latestOrder = Order::orderBy('id', 'DESC')->first();
		if (!$latestOrder)
		{
			$oid = 1;
		}else{
			$oid = $latestOrder->id + 1;
		}
		
		$order = new Order();
		$order->id = $oid;
		$order->customer_id = ($request->customer_id)?$request->customer_id:0;
		$order->product_id = $request->product;
		$order->amount = $request->amount;
		$order->saleprice = $request->saleprice;
		$order->discount = ($request->discount)?$request->discount:0;
		$order->warehouse_id = $request->warehouse;
		$order->discountispercentage = ($request->discountispercentage)?true:false;
		$order->save();

		return response()->json(['id'=>$oid, 'message'=>'Successfully Added Order'],200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$order = Order::findOrFail($id);

		return view('orders.show', compact('order'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$order = Order::findOrFail($id);

		return view('orders.edit', compact('order'));
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
		$order = Order::findOrFail($id);

		

		$order->save();

		return redirect()->route('orders.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$order = Order::findOrFail($id);
		$order->delete();

		return redirect()->route('orders.index')->with('message', 'Item deleted successfully.');
	}

}
