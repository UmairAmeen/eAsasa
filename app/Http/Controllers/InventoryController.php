<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$inventories = Inventory::orderBy('id', 'desc')->paginate(10);

		return view('inventories.index', compact('inventories'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('inventories.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$inventory = new Inventory();

		

		$inventory->save();

		return redirect()->route('inventories.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$inventory = Inventory::findOrFail($id);

		return view('inventories.show', compact('inventory'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$inventory = Inventory::findOrFail($id);

		return view('inventories.edit', compact('inventory'));
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
		$inventory = Inventory::findOrFail($id);

		

		$inventory->save();

		return redirect()->route('inventories.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$inventory = Inventory::findOrFail($id);
		$inventory->delete();

		return redirect()->route('inventories.index')->with('message', 'Item deleted successfully.');
	}

}
