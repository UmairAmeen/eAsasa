<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\PromotionSmsRequest;
use App\Http\Controllers\SMController;
use App\Customer;
use App\SM;
use Yajra\Datatables\Facades\Datatables;

class PromotionController extends Controller
{
    public function __construct()
	{
		\View::share('title',"Promotion");
		\View::share('load_head',true);	
	}
	 /**
     * Display a listing of the resource.
     *
     * @return Response
     */
	public function index()
	{
        $customers = Customer::all();
		$promotion_sms = SM::where('sms_type', 'promotion')->get();
		$last_message = SM::where('sms_type', 'promotion')->latest('id')->first();
		return view('promotion.index', compact('customers', 'last_message', 'promotion_sms'));
	}

    public function send_sms(PromotionSmsRequest $request)
	{
      //Branded SMS functionality started
		$response = [];
        $res = [];
		if(getSetting('sms_enable') == '1')
		{
			if(getSetting('sms_url') != "" && getSetting('sms_user_name') != "" && getSetting('sms_mask') != "" && getSetting('sms_password') != ""){
				$message = $request->message;
               if($request->customer_numbers)
                {
                     $customer_phone = explode(',',$request->customer_numbers[0]);
                }
                else
                {
                    $customer_ids = $request->customer_id;
                    $customer_phone = Customer::whereIn('id',$customer_ids)->pluck('phone')->toArray();
                }
                
                foreach($customer_phone as $phone)
                {
                    $result = (new SMController)->send_sms($phone,$message,true);
                    $res[] = ($result == "Sent Successfully")?'1' : '0';
                }
                if(!empty($res))
                {
                    $totalCount = count($res);
                    $valuesCount = array_count_values($res);
                    $successValues = isset($valuesCount[1])?$valuesCount[1]:'0';
                    $errorValues = $totalCount - $successValues;

                    $response['message'] = "Success : ".$successValues." Errors : ".$errorValues;
                    $response['type'] = 'success';
                }
			}
			else{
				$response['message'] = "SMS Not Sent due to invalid Credentials";
                $response['type'] = 'error';
			}
		}
		else{
			$response['message'] = "SMS Not Sent";
		}

		//Branded SMS fucntionality ended
		return response()->json(['sms_response' => $response,'action'=>'reset','do'=>""],200);
	}
	public function import_excel(Request $request)
	{
		$this->customer_numbers=[];
		@\Excel::load($request->file, function ($reader) {
            // Loop through all sheets
            $reader->each(function ($sheet) {
                if(isset($sheet->number))
                {
                    $this->customer_numbers[] = $sheet->number;
                }	
			});
        })->get();
		return response( $this->customer_numbers);
	}

	public function datatables($message)
    {
        return Datatables::of(SM::selectRaw("id,number,message,status")->where('sms_type','promotion')
		->when($message == 'success' , function ($query){
			return $query->where('status' , 'Sent Successfully');
		})->when($message == 'error' , function ($query){
			return $query->where('status' ,'!=', 'Sent Successfully');
		}))
          ->addColumn("options", function ($row) {
                return ' <ul class="list-inline">
    	 	<li><form action="'. url('promotion').'/'. $row->id .'" method="POST" style="display: inline;"><div id="log"></div><input type="hidden" name="_method" value="DELETE"><input type="hidden" name="_token" value="'. csrf_token() .'"> <button type="submit" class="btn btn-xs btn-danger delete_promotion_sms"><i class="glyphicon glyphicon-trash"></i> Delete</button></form></li>
			</ul>';
            })->make(true);
    }

	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $sms = SM::whereId($id)->first();
            $sms->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 401);
        }
        return response()->json(['message' => 'SMS is successfully deleted','action'=>'redirect','do'=>url('/promotion')], 200);
    }
}
