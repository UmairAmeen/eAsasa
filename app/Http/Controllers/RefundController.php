<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddClaimRequest;


use View;
use App\Refund;
use DB;


class RefundController extends Controller
{
    	public function __construct()
	{
		\View::share('title',"Refund");
		View::share('load_head',true);
		View::share('refund_menu',true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$refunds = Refund::orderBy('id', 'desc')->paginate(10);

		return view('refunds.index', compact('refunds'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('refunds.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(AddClaimRequest $request)
	{

		try{
			DB::beginTransaction();

			$latestClaim = Refund::orderBy('id', 'DESC')->first();

			if (!$latestClaim)
			{
				$rid = 1;
			}else{
				$rid = $latestClaim->id +1;
			}

			$refund = new Refund();
			$refund->product_id = $request->product;
			$refund->quantity = $request->stock;
			$refund->price = $request->price;
			$refund->rid = $rid;
			$refund->warehouse_id = $request->warehouse;
			$refund->date = date('Y-m-d',strtotime($request->date));
			$refund->customer_id = $request->supplier;
			if ($request->customer)
				$refund->customer_id = $request->customer;
			$refund->save();


			DB::commit();
		}catch(\Exception $e)
		{
			DB::rollBack();
			return response()->json(['message' => $e->getMessage()], 403);
		}

		return response()->json(['message' => 'Claim added','action'=>'redirect','do'=>url('/refunds')], 200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$Refund = Refund::findOrFail($id);

		return view('refunds.show', compact('Refund'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$Refund = Refund::findOrFail($id);

		return view('refunds.edit', compact('Refund'));
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
		$Refund = Refund::findOrFail($id);

		

		$Refund->save();

		return redirect()->route('refunds.index')->with('message', 'Item updated successfully.');
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
			Transaction::where('invoice_id', $id)->delete();
			$orders = Order::where('invoice_id',$id)->pluck('id')->toArray();
			StockManage::whereIn('sale_id', $orders)->delete();
			Order::where('invoice_id', $id)->delete();
			Invoice::whereId($id)->delete();
			// $Refund->delete();
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json(['message' => $e->getMessage()], 403);
		}
		

		return response()->json(['message' => 'Claim Successfully Removed','action'=>'redirect','do'=>url('/refunds')], 200);
	}
}
