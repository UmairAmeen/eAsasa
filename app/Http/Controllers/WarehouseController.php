<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\WarehouseRequest;
use App\Http\Controllers\Controller;

use App\Warehouse;
use App\Products;
use App\StockManage;

use Illuminate\Http\Request;
use View;
use Exception;
use DB;
use Cache;

class WarehouseController extends Controller {

	private $supplier;

	public function __construct()
	{
		\View::share('title',"Warehouse");
		View::share('load_head',true);
		View::share('warehouse_menu',true);
		// $this->supplier = new SupplierPurchaseController();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$warehouses = Warehouse::orderBy('id', 'desc')->paginate(10);

		return view('warehouses.index', compact('warehouses'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('warehouses.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(WarehouseRequest $request)
	{
		$warehouse = new Warehouse();
		$warehouse->name = $request->name;
		$warehouse->address = $request->address;
		// $warehouse->is_active = $request->is_active;
		$warehouse->save();

		return response()->json(['message' => 'Warehouse is successfully added','action'=>'redirect','do'=>url('/warehouses')], 200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$warehouse = Warehouse::findOrFail($id);

		$pro = $warehouse->stocks->groupBy('product_id');

		return view('warehouses.show', compact('warehouse','pro'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$warehouse = Warehouse::findOrFail($id);

		return view('warehouses.edit', compact('warehouse'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @param Request $request
	 * @return Response
	 */
	public function update(WarehouseRequest $request, $id)
	{
		$warehouse = Warehouse::findOrFail($id);
		$warehouse->name = $request->name;
		$warehouse->address = $request->address;
		// $warehouse->is_active = $request->is_active;
		$warehouse->save();

		return response()->json(['message' => 'Warehouse is successfully updated','action'=>'redirect','do'=>url('/warehouses')], 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try{
			DB::beginTransaction();
		$warehouse = Warehouse::findOrFail($id);
		StockManage::where('warehouse_id',$id)->delete();
		$warehouse->delete();
		Cache::forget('products');
		DB::commit();
		return response()->json(['message' => 'Warehouse Deleted','action'=>'redirect','do'=>url('/warehouses')], 200);
			
		}catch(Exception $e)
		{
			DB::rollback();
			return response()->json(['message' => 'You cannot delete Warehouse','action'=>'redirect2','do'=>url('/warehouses')], 500);
		}

	}

	public function getAvailableQuantity($warehouse_id=0, $product_id=0)
	{

		$warehouse = Warehouse::whereId($warehouse_id)->first();
		if (!$warehouse)
			return 0;

		return getProductInventory($warehouse->inventory, $product_id)->quantity;
	}

	public function returnJson(Request $req)
    {
    	$product_id = $req->product;
    	// $id = $this->supplier->getWarehouseFromProduct($product_id);

    	if (isset($req->q)){
    		$warehouse = Warehouse::Where('name', 'like', '%' . $req->q.'%')->orderBy('id','desc')->get();
    	}else {
    	 	# code...
    	 	$warehouse = Warehouse::orderBy('id','desc')->get();
    	 } 
    	 $returnArray = [];
    	 foreach ($warehouse as $key => $value) {
    	 	if ($value->address)
    	 		$returnArray[] = ['id'=>$value->id, 'text'=>$value->name ." - (" .$value->address.")"];
    	 	else
    	 		$returnArray[] = ['id'=>$value->id, 'text'=>$value->name];
    	 }
    	 return $returnArray;
    }
    public function allWarehouseJson(Request $req)
    {
    	if (isset($req->q)){
    		$warehouse = Warehouse::Where('name', 'like', '%' . $req->q.'%')->orderBy('id','desc')->get();
    	}else {
    	 	# code...
    	 	$warehouse = Warehouse::orderBy('id','desc')->get();
    	 } 
    	 $returnArray = [];
    	 foreach ($warehouse as $key => $value) {
    	 	if ($value->address)
    	 		$returnArray[] = ['id'=>$value->id, 'text'=>$value->name ." - (" .$value->address.")"];
    	 	else
    	 		$returnArray[] = ['id'=>$value->id, 'text'=>$value->name];
    	 }
    	 return $returnArray;
    }
    public function warehouse_json(Request $req)
    {
    	\Debugbar::disable();
    	$data = $this::allWarehouseJson($req);

    	return "var warehouse_d = ".json_encode($data).";";

    }
    public function returnProductJson(Request $req)
    {
    	$product = Products::whereId($req->product_id)->first();
    	return warehouse_stock($product, intval($req->warehouse_id));
    }
    public function returnProductInWarehouse(Request $request)
    {
    	$warehouse = Warehouse::whereId($request->warehouse)->first();
    	if (!$warehouse)
    		return [];
    	// echo $warehouse->inventory;
    	$returnArray = [];
    	 foreach ($warehouse->inventory as $key => $value) {
    	 		$returnArray[] = ['id'=>$value->product->id, 'text'=>$value->product->name];
    	 }
    	 return $returnArray;
    }

}
