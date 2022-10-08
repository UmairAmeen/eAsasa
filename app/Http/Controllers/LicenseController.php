<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\License;
use App\User;
use Illuminate\Http\Request;
use Session;

class LicenseController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$licenses = License::orderBy('id', 'desc')->paginate(10);

		return view('licenses.index', compact('licenses'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		// PACKAGE - EXPIRY - MOBILE - VALIDATION
		// A1 - 311216 - 3214024399 - ADD ALL LAST 5 CHAR
		// C1 - 010199 - 4236310831 - 70A8B
		// XX - XXXXX - XX XX XX XX - XXXXX (20 digits)
		// A1 - 4BFB0 - BF 92 1E FC - 6DF20
		// A1	58718100 BF 92 1E CF 3A070
		// 	A158684680BF921ECFA65F0 <valid license>
		//	A15685C180BF921ECF7E0F0 <invalid license>
		// 01	23456789 10 11 12 13 14 15 16 17  18
		// $license_generated = strtoupper("A1".dechex(strtotime("01/01/2017")).dechex(3214024399) .substr(dechex(161+strtotime("01/01/2017")+3214024399),-5));
		// $license_for_users = substr(chunk_split($license_generated,5,"-"), 0, -1);


		// //validate license
		// $license = $license_generated;
		// $license_data['package'] = substr($license, 0,2);
		// $license_data['expiry_date'] = date('m/d/y',(hexdec(substr($license, 2,8))));
		// $license_data['mobile_number'] = hexdec(substr($license, 10,8));
		// $license_data['validate'] = (substr($license, 18)== strtoupper(substr(dechex(hexdec($license_data['package'])+strtotime($license_data['expiry_date'])+$license_data['mobile_number']),-5)))?true:false;
		// $license_data['today'] = date("m/d/y");
		// $license_data['days_left_in_expiry'] = (strtotime($license_data['expiry_date']) - strtotime($license_data['today']))/(60*60*24);
		// $license_data['expired'] = ($license_data['days_left_in_expiry'] < 0)?true:false;
		// //end
		// return view('licenses.create', compact(['license_generated','license_data','license_for_users']));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		if ($this::keyvalidation($request->license_key))
		{
			$license = License::firstOrNew(['id'=> 1]);
			$license->license = $request->license_key;
			$license->save();
			return response()->json(['message' => 'Key is successfully installed','action'=>'redirect','do'=>url('/')], 200);
		}else{
			return response()->json(['message' => 'Key is invalid or expired'], 403);
		}
		

		


	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$license = License::findOrFail($id);

		return view('licenses.show', compact('license'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$license = License::findOrFail($id);

		return view('licenses.edit', compact('license'));
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
		$license = License::findOrFail($id);

		

		$license->save();

		return redirect()->route('licenses.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$license = License::findOrFail($id);
		$license->delete();

		return redirect()->route('licenses.index')->with('message', 'Item deleted successfully.');
	}

	private function keyvalidation($data)
	{
		$key = str_replace("-", "", $data);
		$license_data['package'] = substr($key, 0,2);
		$license_data['expiry_date'] = date('m/d/y',(hexdec(substr($key, 2,8))));
		$license_data['mobile_number'] = hexdec(substr($key, 10,8));
		$license_data['validate'] = (substr($key, 18)== strtoupper(substr(dechex(hexdec($license_data['package'])+hexdec(substr($key, 2,8))+$license_data['mobile_number']),-5)))?true:false;
		$license_data['today'] = date("m/d/y");
		$license_data['days_left_in_expiry'] = (strtotime($license_data['expiry_date']) - strtotime($license_data['today']))/(60*60*24);
		$license_data['expired'] = ($license_data['days_left_in_expiry'] < 0)?true:false;
		$user_mobile = str_replace("-", "", User::first()->email);
		Session::put('license_info',$license_data);

		return ($license_data['validate'] && !$license_data['expired'] && ($license_data['mobile_number'] == $user_mobile));

	}

	public function isValidLicense()
	{
		$license = License::whereId(1)->first();
		if (!$license)
			return false;
		Session::put('license',$license->license);
		return $this::keyvalidation($license->license);
	}
	public function invalidLicense()
	{
		if ($this::isValidLicense())
		{
			return redirect('/');
		}
		$screen = "";
		return view('licenses.verification', compact('screen'));
	}

}
