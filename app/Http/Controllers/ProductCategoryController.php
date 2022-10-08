<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller {

	public function __construct()
	{
		\View::share('title',"Product Category");
		\View::share('load_head',true);
		\View::share('product_category_menu',true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$product_categories = ProductCategory::orderBy('id', 'desc')->paginate(10);

		return view('product_categories.index', compact('product_categories'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('product_categories.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$product_category = new ProductCategory();
		$product_category->name = $request->name;
		$product_category->description = $request->description;

		$product_category->save();

		return response()->json(['message' => 'Category is successfully added','action'=>'redirect','do'=>url('/product_categories')], 200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$product_category = ProductCategory::findOrFail($id);

		return view('product_categories.show', compact('product_category'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$product_category = ProductCategory::findOrFail($id);
		

		return view('product_categories.edit', compact('product_category'));
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
		$product_category = ProductCategory::findOrFail($id);

		$product_category->name = $request->name;
		$product_category->description = $request->description;

		$product_category->save();

		return response()->json(['message' => 'Category is successfully updated','action'=>'redirect','do'=>url('/product_categories')], 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$product_category = ProductCategory::findOrFail($id);
		$product_category->delete();

		return response()->json(['message' => 'Category is successfully deleted','action'=>'redirect','do'=>url('/product_categories')], 200);
	}

}
