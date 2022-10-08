<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use View;

class SettingController extends Controller {

	public function __construct()
	{
		\View::share('title',"Settings");
		View::share('load_head',true);
		View::share('settings_menu',true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if (!is_admin()) {
			return redirect('/');
		}
		$sms_keys = ['sms_url','sms_user_name','sms_mask','sms_password','sms_promotional'];
		$defaultSettings = Setting::GetDefaultValues();
		$settings = Setting::select('module', 'key', 'value')->get()->toArray();
		// dd(array_keys($defaultSettings),$defaultSettings, $settings);
		return view('settings.create', compact('sms_keys','defaultSettings','settings'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('settings.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$defaultSettings = Setting::GetDefaultValues();
		$settings = $request->all();
		// try {
			DB::beginTransaction();
			foreach ($settings as $module => $array) {
				foreach($array as $key => $value) {
					$setting = Setting::where(['key' => $key])->first();
					if ($setting) {
						$setting->update([
							'module' => $module,
							'name' => $defaultSettings[$module][$key]['title'],
							'type' => $defaultSettings[$module][$key]['type'],
							'value' => is_array($value) ? implode(",", $value) : $value,
							'defaultValue' => $defaultSettings[$module][$key]['default'],
							'updated_by' => auth()->user()->id
						]);
					} else {
						Setting::create([
							'module' => $module,
							'key' => $key,
							'name' => $defaultSettings[$module][$key]['title'],
							'type' => $defaultSettings[$module][$key]['type'],
							'value' => is_array($value) ? implode(",", $value) : $value,
							'defaultValue' => $defaultSettings[$module][$key]['default'],
							'updated_by' => auth()->user()->id
						]);
					}
				}
			}
			foreach ($defaultSettings as $module => $element) {
				foreach($element as $key => $data) {
					if ((
							$data['type'] == Setting::CHECKBOX ||
							$data['type'] == Setting::LABEL ||
							$data['type'] == Setting::SELECT2
						) && isset($settings[$module][$key]) == false
					) {
						$setting = Setting::where(['key' => $key])->first();
						if ($setting) {
							$setting->update([
								'module' => $module,
								'name' => $data['title'],
								'type' => $data['type'],
								'value' => $data['type'] == Setting::CHECKBOX ? 0 : $data['default'],
								'defaultValue' => $data['default'],
								'updated_by' => auth()->user()->id
							]);
						} else {
							Setting::create([
								'module' => $module,
								'name' => $data['title'],
								'key' => $key,
								'type' => $data['type'],
								'value' => $data['default'],
								'defaultValue' => $data['default'],
								'updated_by' => auth()->user()->id
							]);
						}
					}
				}
			}
			DB::commit();
			session()->put('settings', Setting::GetCurrentSettings());
			return response()->json(['message' => 'Settings Updated','action'=>'redirect','do'=>url('/settings')], 200);
		// } catch (\Exception $e) {
		// 	DB::rollBack();
		// 	return response()->json(['message' => 'Settings Failed to Update Because '.$e->getMessage(), 'action'=>'redirect','do'=> url('/settings')], 500);
		// }		
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$setting = Setting::findOrFail($id);

		return view('settings.show', compact('setting'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$setting = Setting::findOrFail($id);

		return view('settings.edit', compact('setting'));
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
		$setting = Setting::findOrFail($id);

		

		$setting->save();

		return redirect()->route('settings.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$setting = Setting::findOrFail($id);
		$setting->delete();

		return redirect()->route('settings.index')->with('message', 'Item deleted successfully.');
	}

}
