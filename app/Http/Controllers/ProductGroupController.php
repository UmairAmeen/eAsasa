<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\ProductGroup;
use Illuminate\Http\Request;

class ProductGroupController extends Controller {

	public function __construct()
	{
		// if (!Cache::has('products'))
		// {
		// 	CacheController::rebuildAllCache();	
		// }
		\View::share('title',"Product Groups");
		\View::share('load_head',true);
		\View::share('product_menu',true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if (!is_allowed('product-list'))
		{
			return redirect('/');
		}
		$product_groups = ProductGroup::orderBy('id', 'desc')->get();

		return view('product_groups.index', compact('product_groups'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if (!is_allowed('product-create'))
		{
			return redirect('/');
		}
		return view('product_groups.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		if (!is_allowed('product-create'))
		{
			return response(['message'=>'Unauthorised'],500);
		}
		$product_group = new ProductGroup();
		$product_group->name = $request->name;
		$product_group->price = $request->price;
		$product_group->products = serialize($request->products);
		$product_group->quantity = serialize($request->quantity);
		$product_group->save();

		return redirect()->route('product_groups.index')->with('message', 'Product Group Created successfully.');
		// return response()->json(['message'=>"Group Added Successfully",'action'=>'redirect','do'=>url('/product_groups/')],200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$product_group = ProductGroup::findOrFail($id);

		return view('product_groups.show', compact('product_group'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if (!is_allowed('product-edit'))
		{
			return response(['message'=>'Unauthorised'],500);
		}
		$product_group = ProductGroup::findOrFail($id);
		return view('product_groups.edit', compact('product_group'));
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
		if (!is_allowed('product-edit'))
		{
			return response(['message'=>'Unauthorised'],500);
		}
		$product_group = ProductGroup::findOrFail($id);
		$product_group->name = $request->name;
		$product_group->price = $request->price;
		$product_group->products = serialize($request->products);
		$product_group->quantity = serialize($request->quantity);
		$product_group->save();

		return response()->json(['message'=>"Group Updated Successfully",'action'=>'redirect','do'=>url('/product_groups/')],200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if (!is_allowed('product-delete'))
		{
			return response(['message'=>'Unauthorised'],500);
		}
		$product_group = ProductGroup::findOrFail($id);
		$product_group->delete();

		return response()->json(['message'=>"Group Deleted Successfully",'action'=>'redirect','do'=>url('/product_groups/')],200);
	}


	public function process_json(Request $req)
    {
    	\Debugbar::disable();
    	$prod = ProductGroup::all();
    	$processor = [];
    	foreach ($prod  as $value) {
    		# code...
    		$products = unserialize($value->products);
    		$quantity = unserialize($value->quantity);
    		$result = [];
    		foreach ($products as $key => $vaie) {
    			# code...
    			if ($req->purchase)
    			{
    				$my_pro_gf = get_product_purchase($vaie)->toArray();

    			}else{
    				$my_pro_gf = get_product($vaie)->toArray();
    			}

    			$result[] = ["product"=>$my_pro_gf, "quantity"=>$quantity[$key]];
    		}
    		

    		$processor[] = ["id"=>$value->id, "text"=>$value->name, "price"=>$value->price, "products"=>$result];
    	}
    	return "var product_group_json_d=".json_encode($processor);
    }

}
