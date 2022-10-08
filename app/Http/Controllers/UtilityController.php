<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use Exception;
use DB;
use Cache;


use App\Http\Requests;

class UtilityController extends Controller
{
    public function __construct()
    {
        \View::share('title', "Utility");
        View::share('load_head', true);
        // $this->supplier = new SupplierPurchaseController();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fake_data = [
            "customer" => "Add Fake Customers",
            "supplier" => "Add Fake Suppliers",
            "product" => "Add Fake Products",
        ];
        $title = "utility";
        return view('utilities.index', compact('title', 'fake_data'));
    }
    
    public function clear_views()
    {
        \Artisan::call('view:clear');
        return redirect()->back()->with('message', 'Views Cleared!');
    }

    public function clear_cache()
    {
        \Artisan::call('cache:clear');
        return redirect()->back()->with('message', 'Cache Cleared!');
    }

    public function add_fake_data(Request $request)
    {
        $selected_command = $URL_to_redirect =  null;
        $fake_data_commands = [
            "customer" => "fake:customer",
            "supplier" => "fake:supplier",
            "product" =>"fake:products"];
        foreach ($fake_data_commands as $key => $command) {
            if ($request->name == $key) {
                $selected_command = $command;
                $URL_to_redirect = $key;
            }
        }
        @\Artisan::call($selected_command, ["count" => (int)$request->count]);
        // return response()->json(['message' => 'Fake '.$URL_to_redirect.'(s) are successfully added','action'=>'redirect','do'=>url('/'.$URL_to_redirect.'s')], 200);
        return response()->json(['location' => $URL_to_redirect], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
