<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SM;
use App\Setting;

use Illuminate\Http\Request;
use \GuzzleHttp\Client;
use \GuzzleHttp\Psr7;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Exception\ClientException;
use Propaganistas\LaravelPhone\PhoneNumber;

use Exception;

class SMController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$s_m_s = SM::orderBy('id', 'desc')->paginate(10);

		return view('s_m_s.index', compact('s_m_s'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('s_m_s.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$s_m_ = new SM();

		

		$s_m_->save();

		return redirect()->route('s_m_s.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$s_m_ = SM::findOrFail($id);

		return view('s_m_s.show', compact('s_m_'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$s_m_ = SM::findOrFail($id);

		return view('s_m_s.edit', compact('s_m_'));
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
		$s_m_ = SM::findOrFail($id);

		

		$s_m_->save();

		return redirect()->route('s_m_s.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$s_m_ = SM::findOrFail($id);
		$s_m_->delete();

		return redirect()->route('s_m_s.index')->with('message', 'Item deleted successfully.');
	}

	public function line_webhook(Request $request)
	{
			$message_safety = new SM;
			$message_safety->message = json_encode($request->all());
			$message_safety->save();
	}

	public function line_notification($message = "No Notification")
	{
		$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('RVqJG2ZGxfBPe6ODQ/Xr1vZNlYrUu9D6fPQ4JnKPL89aBJMe7l02LUF07YQoVQpAw69Uh3/kz5LuLPGFCjROR3q44KMe0cmGEotEJ34kqIUxJg/R/g9eNa6dvnsbPGJ47a2VS+k2zpvBWvnVQgC8MAdB04t89/1O/w1cDnyilFU=');
		$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '6ff17c7f85bd124817f333a416c35263']);
		$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);

		//this sent to room, in which shan is added. and bot send message daily on this.
		//shan userId: Ue8e9e41ce2739637a0a4c4b3774480c5
		//roomid shan: R9bb282a23fee4bb265a504d2e1f608b1

		//My ID: Uddf4919f357fc44420965cd0c86af12e
		try{
			$response = $bot->pushMessage(getSetting('notification_line','Uddf4919f357fc44420965cd0c86af12e'), $textMessageBuilder);
			echo $response->getHTTPStatus() . ' ' . $response->getRawBody();

		}catch(\Exception $e)
		{

		}
		


		// $message_safety = new SM;
		// $message_safety->message = $message;
		// $message_safety->save();
	}


	public function send_sms($receiver, $message, $promotion_sms = false)
	{
		$receiver = explode(',',$receiver);
		$receiver = (str_replace('-', '', trim($receiver[0])));
		$receiver = preg_replace('/^0/', '92', $receiver);
		// dd($num, $message);
		// dd(getSetting('sms_url'));
		// $message = $request->message;// "Hello, How are you\nWelcome to eAsasa"
		// $setting= Setting::where('key','notification_phone')->first();

		// if (!$setting || !$setting->value)
		// {
		// 	$message_safety = new SM;
		// 	$message_safety->message = $message;
		// 	$message_safety->save();
		// }
		// $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
		// $phone_numberObject = $phoneNumberUtil->parse($setting->value, 'PK');
		// $phone_number = PhoneNumber::make($setting->value, 'PK')->formatE164();
		// $phone_number = $phoneNumberUtil->format($phone_numberObject, \libphonenumber\PhoneNumberFormat::E164);

		//outreach sms data param
		// $params = [
		// 	'to'=>$receiver,
		// 	'id'=>env('SMS_USERNAME'),
		// 	'pass'=>env('SMS_PASSWORD'),
		// 	'mask'=>env('SMS_SENDER'),
		// 	'msg'=>$message,
		// 	'type'=>'json',
		// 	'lang' =>'English'
		// ];

		//SMS POINT sms data param

		//ENV params
		// $params =  "userName=".env('SMS_USER_NAME')."&password=".
		// env('SMS_PASSWORD')."&ClientID=".env('SMS_CLIENT_ID')."&mask=".
		// env('SMS_MASK')."&msg=".$message."&to=".$receiver."&language=".'English';
		// $url = env('SMS_URL');

		//Settings params
		$params =  "userName=".session('settings.profile.sms_user_name')."&password=".
		session('settings.profile.sms_password')."&ClientID=".session('settings.profile.sms_user_name')."&mask=".
		session('settings.profile.sms_mask')."&msg=".$message."&to=".$receiver."&language=".'English';
		$url = session('settings.profile.sms_url');
		
		try{
			//Outreach sms request
			// $cli = new Client;
			// $response = $cli->request('POST', env('SMS_URL'),['form_params' => $params]);
			// $response = $cli->request('POST', env('SMS_URL'),['body' => $params]);
			// dd($response);
			// $result = json_decode($response->getBody());


			// SMS POINT sms request
			$curl_request = curl_init($url);
			curl_setopt($curl_request, CURLOPT_POST, true); 
			curl_setopt($curl_request, CURLOPT_POSTFIELDS, $params); 
			curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true); 
			$result = curl_exec($curl_request); //get return String
			curl_close($curl_request);

			if($result)
			{
				$message_safety = new SM;
				$message_safety->message = $message;
				$message_safety->number = $receiver;
				$message_safety->status = $result;
				if($promotion_sms)
				{
					$message_safety->sms_type = 'promotion';
				}
				$message_safety->save();
			}

		}catch(Exception $e)
		{
			$message_safety = new SM;
			$message_safety->message = $e->getMessage()." ** ".print_r($params,true);
			$message_safety->save();
		}

		/*
{
    "Response": {
        "message_id": "158385770",
        "message_count": 1,
        "price": 0.0085
    },
    "ErrorMessage": "",
    "Status": 0
}
		*/

			

			return $result;

	}

}
