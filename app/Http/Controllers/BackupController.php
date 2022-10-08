<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Artisan;

class BackupController extends Controller
{
	public function __construct()
	{
		\View::share('title',"Backup");
		\View::share('load_head',true);
		\View::share('backup_menu',true);
			
	}
	public function index()
	{
		if (!is_allowed('backup'))
			{
				return redirect('/');
			}
		$path = storage_path('app')."/http---localhost";
		try {
			$files = scandir($path);
			
		} catch (Exception $e) {
			$files = [];
		}

		return view('backup.index', compact(['files','path']));
	}
    public function backup()
    {
    	// php artisan backup:run --only-db
    	$data = Artisan::call("backup:run",array("--only-db"=>true));
    	Artisan::call('backup:clean');
    	return response()->json(['message' => 'Backup Created','action'=>'redirect','do'=>url('/backup')], 200);

    }
    public function download($path)
    {
    	$file = base64_decode($path);
    	return response()->download($file);
    }
}
