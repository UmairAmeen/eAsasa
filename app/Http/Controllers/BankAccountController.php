<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\BankAccount;
use Illuminate\Http\Request;
use View;

class BankAccountController extends Controller {

	public function __construct()
	{
		View::share('load_head',true);
		View::share('bank_menu',true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$bank_accounts = BankAccount::orderBy('id', 'desc')->paginate(10);

		return view('bank_accounts.index', compact('bank_accounts'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('bank_accounts.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$bank_account = new BankAccount();
		$bank_account->name = $request->name;
		$bank_account->account_number = $request->account_number;
		$bank_account->branch = $request->branch;
		$bank_account->comment = $request->comment;
		$bank_account->save();
		return response()->json(['message' => 'bank account is successfully added','action'=>'redirect','do'=>url('/bank_accounts')], 200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$bank_account = BankAccount::findOrFail($id);

		return view('bank_accounts.show', compact('bank_account'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$bank_account = BankAccount::findOrFail($id);

		return view('bank_accounts.edit', compact('bank_account'));
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
		$bank_account = BankAccount::findOrFail($id);
		$bank_account->name = $request->name;
		$bank_account->account_number = $request->account_number;
		$bank_account->branch = $request->branch;
		$bank_account->comment = $request->comment;
		$bank_account->save();
		return response()->json(['message' => 'bank account is successfully updated','action'=>'redirect','do'=>url('/bank_accounts')], 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$bank_account = BankAccount::findOrFail($id);
		$bank_account->delete();

		// return redirect()->route('bank_accounts.index')->with('message', 'Item deleted successfully.');
		return response()->json(['message' => 'bank account is successfully deleted','action'=>'redirect','do'=>url('/bank_accounts')], 200);

	}

}
