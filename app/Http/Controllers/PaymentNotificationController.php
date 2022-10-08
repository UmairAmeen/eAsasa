<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class PaymentNotificationController extends Controller
{
    public function __construct()
	{
		\View::share('title',"Payment Notification");
		View::share('load_head',true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$notifications = Notification::orderBy('id', 'desc')->get();

		return view('notifications.index', compact('notifications'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('notifications.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$notification = new Notification();

		

		$notification->save();

		return redirect()->route('notifications.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$notification = Notification::findOrFail($id);

		return view('notifications.show', compact('notification'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$notification = Notification::findOrFail($id);

		return view('notifications.edit', compact('notification'));
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
		$notification = Notification::findOrFail($id);

		

		$notification->save();

		return redirect()->route('notifications.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$notification = Notification::findOrFail($id);
		$notification->delete();

		return redirect()->route('notifications.index')->with('message', 'Item deleted successfully.');
	}
}
