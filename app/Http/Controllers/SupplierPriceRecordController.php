<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SupplierPriceRecord;
use Illuminate\Http\Request;
use App\Supplier;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Exception;
class SupplierPriceRecordController extends Controller {

	public function __construct()
	{
		View::share('title',"Supplier Price Record");
		View::share('load_head',true);
		View::share('price_record_menu',true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if (!is_allowed('report-product_record_supplier')) {
			return redirect('/');
		}
		$supplier_price_records = SupplierPriceRecord::select(DB::raw('supplier_id, max(date) as max_dt'))->groupBy('supplier_id')->get();
		return view('supplier_price_records.index', compact('supplier_price_records'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if (!is_allowed('report-product_record_supplier')) {
			return redirect('/');
		}
		return view('supplier_price_records.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		if (!is_allowed('report-product_record_supplier')) {
			return response()->json(['message' => "Unauthorized"], 403);
		}
		$supplier_price_record = new SupplierPriceRecord();
		$supplier_price_record->save();
		return redirect()->route('supplier_price_records.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		if (!is_allowed('report-product_record_supplier')) {
			return redirect('/');
		}
		// $supplier_price_record = SupplierPriceRecord::findOrFail($id);
		$supplier = Supplier::whereId($id)->first();
		if (!$supplier) {
			return redirect('/');
		}
		$prod_arr = SupplierPriceRecord::where('supplier_id', $id)->groupBy('product_id')->pluck('product_id')->toArray();
		$supplier_price_record = [];
		foreach($prod_arr as $key => $val){
			$sp_id = SupplierPriceRecord::where('product_id',(int)$val)->max('id');
			$supplier_price_record[$key] = SupplierPriceRecord::where('id',(int)$sp_id)->first();
		}
		// $supplier_price_record = SupplierPriceRecord::whereIn('id',$supplier_price_record_id)
		// // ->whereRaw()
		// ->groupBy('product_id',DB::raw("max('date')"))
		// ->get();
		return view('supplier_price_records.show', compact('supplier_price_record','supplier'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if (!is_allowed('report-product_record_supplier')) {
			return redirect('/');
		}
		$supplier_price_record = SupplierPriceRecord::findOrFail($id);
		return view('supplier_price_records.edit', compact('supplier_price_record'));
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
		if (!is_allowed('report-product_record_supplier')) {
			return response()->json(['message' => "Unauthorized"], 403);
		}
		$supplier_price_record = SupplierPriceRecord::findOrFail($id);
		$supplier_price_record->save();
		return redirect()->route('supplier_price_records.index')->with('message', 'Item updated successfully.');
	}

	public function store_product_pricing(Request $request)
	{
		if (!is_allowed('report-product_record_supplier')) {
			return response()->json(['message' => "Unauthorized"], 403);
		}
		DB::beginTransaction();
		try {
			foreach ($request->product as $key => $value) {
				if (($request->price[$key] != $request->old_price[$key]) || ($request->date[$key] != $request->old_date[$key])) {
					supplier_price_record($request->date[$key], $request->price[$key], $request->supplier_id, $value);
				}				
				if ($request->sale_price[$key] != $request->old_sale_price[$key]) {
					product_price_update($value, $request->sale_price[$key], $request->date[$key]);
				}
			}
			CacheController::rebuildAllCache();
			DB::commit();
			return response()->json(['message' => 'Supplier Pricing Updated','action'=>'reload','do'=>url('/products')], 200);
		} catch(Exception $e) {
			DB::rollBack();
			return response()->json(['message' => $e->getMessage()], 403);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$supplier_price_record = SupplierPriceRecord::findOrFail($id);
		$supplier_price_record->delete();
		return response()->json(['message' => 'Supplier Pricing Deleted'], 200);
	}

	public function deleteDef($id)
	{
		$supplier_price_record = SupplierPriceRecord::findOrFail($id);
		$supplier_price_record->delete();
		// return response()->json(['message' => 'Supplier Pricing Deleted'], 200);
		return back();
	}
}
