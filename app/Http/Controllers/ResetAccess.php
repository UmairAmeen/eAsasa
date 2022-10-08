<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Auth;

use App\User;

use Artisan;

class ResetAccess extends Controller
{
    public function verify_key(Request $request)
    {
    	$key = str_replace("-", "", $request->reset_pass_master);
    	$mac_address = $this::getmac();
    	$admin_user = User::first();
    	$mobile_number = str_replace("-", "", $admin_user->email);

    	if ($this::reset_pass($mac_address,$mobile_number) == $key)
    	{
    		Auth::loginUsingId($admin_user->id);

			return response()->json(['message' => 'Valid Reset Key. Please Wait...','action'=>'redirect','do'=>url('/')], 200);

    	}else{
    		return response()->json(['message' => 'Invalid Request'], 403);
    	}


    }
    private function reset_pass($mac_address, $mobile_number)
	{
		$mac = substr(base64_encode($mac_address), 0,5);
		$number =substr(base64_encode($mobile_number), 0,5);
		$secret_key = date('dmy'); // 6
		$reset_pass = md5(base64_encode($mac.$number.$secret_key));
		return strtoupper($reset_pass);
	}
    private function installation_key()
    {
        $mobile_number = "03454777487";
        $number =substr(base64_encode($mobile_number), 0,5);
        $salt = "softwareupgrdation";
        $secret_key = date('dmy');
        return md5($number. $salt. $secret_key);
    }
	private function getmac()
    {
        ob_start(); // Turn on output buffering
        system('ipconfig /all'); //Execute external program to display output
        $mycom=ob_get_contents(); // Capture the output into a variable
        ob_clean(); // Clean (erase) the output buffer
        $findme = "Physical";
        $pmac = strpos($mycom, $findme); // Find the position of Physical text
        $mac=substr($mycom,($pmac+36),17);
        return str_replace("-", "", $mac);
    }

    public function upgrade($key)
    {
         if($key == $this::installation_key()){
            try {
              echo '<br>init migrate:install... <br/>';
                echo Artisan::call('migrate', array('--force' => true));
                  echo 'done migrate:install <br/>';
                  echo '<br>init with tables seeder...';
                  Artisan::call('db:seed');
                  echo '<br>done with Sentry tables seader';
                } catch (Exception $e) {
                  Response::make($e->getMessage(), 500);
                }
          }else{
            abort(404);
          }
    }
}
