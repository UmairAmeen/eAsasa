<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Sync;
use Illuminate\Http\Request;
use DB;

class SyncController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$syncs = Sync::where('sync_id', null)->get();

		return view('syncs.index', compact('syncs'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('syncs.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$sync = new Sync();

		

		$sync->save();

		return redirect()->route('syncs.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$sync = Sync::findOrFail($id);

		return view('syncs.show', compact('sync'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$sync = Sync::findOrFail($id);

		return view('syncs.edit', compact('sync'));
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
		$sync = Sync::findOrFail($id);

		

		$sync->save();

		return redirect()->route('syncs.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$sync = Sync::findOrFail($id);
		$sync->delete();

		return redirect()->route('syncs.index')->with('message', 'Item deleted successfully.');
	}

	public function addOperation($sql, $data)
	{
		$operation = strtok($sql, ' ');
		$same_table = strpos($sql, '`syncs`') !== false;
		$migration_table = strpos($sql, '`migrations`') !== false;
		if (!$operation || $operation == "select" || $operation == "drop" || $operation == "truncate"  || $operation == "create" || $operation == "alter" || $same_table || $migration_table)
		{
			return false;
		}
		$salt = "";
		DB::beginTransaction();
			$sync = New Sync();
			$sync->action = $sql;
			$sync->data = json_encode($data);
			$sync->key = md5($sql . $sync->data . $salt);
			$sync->save();
		DB::commit();
	}

}
