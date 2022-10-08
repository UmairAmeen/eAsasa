<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Unit;
use Illuminate\Http\Request;
use View;

class UnitController extends Controller {


	public function __construct()
	{
		\View::share('title',"Units");
		View::share('load_head',true);
		View::share('units_menu',true);
		// $this->supplier = new SupplierPurchaseController();
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
			// return 0;
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$units = Unit::orderBy('id', 'desc')->paginate(10);

		return view('units.index', compact('units'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('units.create');
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
			// return 0;
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$unit = Unit::firstOrNew(['name'=>$request->name]);
		$unit->name = $request->name;
		$unit->deleteable = true;
		$unit->save();

		return redirect()->route('units.index')->with('message', 'Unit created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$unit = Unit::findOrFail($id);

		return view('units.show', compact('unit'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$unit = Unit::findOrFail($id);

		return view('units.edit', compact('unit'));
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
			// return 0;
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$unit = Unit::findOrFail($id);
		$unit->name = $request->name;

		

		$unit->save();

		return redirect()->route('units.index')->with('message', 'Unit updated successfully.');
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
			// return 0;
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$unit = Unit::findOrFail($id);
		$unit->delete();

		return redirect()->route('units.index')->with('message', 'Unit deleted successfully.');
	}

}
