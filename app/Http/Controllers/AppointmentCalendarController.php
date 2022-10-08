<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\AppointmentCalendar;
use Illuminate\Http\Request;
use App\Http\Requests\ValidateAppointmentRequest;

use App\ChequeManager;
use App\Customer;
use App\Transaction;

use Carbon\Carbon;
use Yajra\Datatables\Facades\Datatables;
use View;
use DB;

class AppointmentCalendarController extends Controller {
	public function __construct()
	{
		View::share('title',"Calendar");
		 View::share('load_head', true);
		 View::share('appointment_menu',true);
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$events = [];

		//add all cheque details
		$cheque_manager = ChequeManager::all();
		foreach ($cheque_manager as $key => $value) {
			# code...
			$customer_name = ($value->customer)?$value->customer->name:"";
			$events[] = \Calendar::event(
			    "Cheque Release: ".$value->id." ".$value->bank." ".$value->transaction_id." | (".number_format($value->amount).") | ".$customer_name, //event title
			    false, //full day event?
			    $value->release_date, //start time (you can also use Carbon instead of DateTime)
			    $value->release_date, //end time (you can also use Carbon instead of DateTime)
				0, //optionally, you can specify an event ID
				["color"=>"purple"]
			);
		}


		//add all cheque details
		$transactions = Transaction::where('payment_type','cheque')->whereNotNull('release_date')->get();
		foreach ($transactions as $key => $value) {
			# code...
			$customer_name = ($value->customer)?$value->customer->name:"";
			$events[] = \Calendar::event(
			    "Transaction Cheque Release: ".$value->id." ".$value->bank." ".$value->transaction_id." | (".number_format($value->amount).") | ".$customer_name, //event title
			    false, //full day event?
			    $value->release_date, //start time (you can also use Carbon instead of DateTime)
			    $value->release_date, //end time (you can also use Carbon instead of DateTime)
				0, //optionally, you can specify an event ID
				["color"=>"orange"]
			);
		}
		//calling
		$customer_call = Customer::where('payment_notify',true)->get();
		foreach ($customer_call as $key => $value) {
			# code...
			$dating = new Carbon($value->last_contact_on);
			$balance = getCustomerBalance($value->id);
			if (!$balance)
			{
				continue;
			}
			$days = $dating->addDays($value->after_last_payment)->format("Y-m-d H:i:s");
			$events[] = \Calendar::event(
			    "Payment Call: ".$value->name." | Last Contact: ".date('d-M-Y',strtotime($value->last_contact_on))." | Balance: ".$balance, //event title
			    false, //full day event?
			    $days, //start time (you can also use Carbon instead of DateTime)
			    $days, //end time (you can also use Carbon instead of DateTime)
				0, //optionally, you can specify an event ID
				["color"=>"green"]
			);
			$repeatation = 7;
			for ($i=1; $i < 6 ; $i++) { 
				# code...
				$datingX = new Carbon($value->last_contact_on);
				$dayz = $value->after_last_payment + ($repeatation * $i);
				$alert_days = $datingX->addDays($dayz)->format('Y-m-d H:i:s');
				$events[] = \Calendar::event(
			    	"Overdue Payment Call: ".$value->name." | Last Contact: ".date('d-M-Y',strtotime($value->last_contact_on))." | Balance: ".$balance, //event title
			    	false, //full day event?
			    	$alert_days,
			    	// date('Y-m-d H:i:s', strtotime($value->last_contact_on . ' +5 days')), //start time (you can also use Carbon instead of DateTime)
			    	$alert_days, //end time (you can also use Carbon instead of DateTime)
					0, //optionally, you can specify an event ID
					["color"=>"red"]
				);				
			}
		}
		$appointment_calendars = AppointmentCalendar::orderBy('id', 'desc')->get();
		$calendar = \Calendar::addEvents($events)->addEvents($appointment_calendars, [ //set custom color fo this event
		        
		    ])->setOptions([ //set fullcalendar options
				'firstDay' => 1,
				'header' => [
            'left' => 'prev,next today',
            'center' => 'title',
            'right' => 'month,basicWeek,listDay,listWeek',
        ],

      // customize the button names,
      // otherwise they'd all just say "list"
      "views"  => [
       "listDay" => ["buttonText"=> 'list day' ],
        "listWeek"=>[ "buttonText"=> 'list week' ]
      ],
        'navLinks'=> true,
        "defaultView"=>"listDay",
        'eventLimit'=> true,
			])->setCallbacks([ //set fullcalendar callback options (will not be JSON encoded)
		        // 'viewRender' => ''
		    ]);

		return view('appointment_calendars.index', compact('appointment_calendars','calendar'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('appointment_calendars.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(ValidateAppointmentRequest $request)
	{
		try{
		$appointment_calendar = new AppointmentCalendar();
		$appointment_calendar->title = $request->title;
		$appointment_calendar->start = date('Y-m-d',strtotime($request->start))." ".date('H:i:s');
		$appointment_calendar->end = date('Y-m-d',strtotime($request->start))." ".date('H:i:s');
		$appointment_calendar->all_day = false;
		$appointment_calendar->background_color = $request->background_color;
		$appointment_calendar->save();

			} catch(\Exception $e)
			{
				
				return response()->json(['message' => $e->getMessage()], 403);
			}
		return response()->json(['message' => 'Appointment added successfully.','action'=>'redirect','do'=>url('/appointment_calendars')], 200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$appointment_calendar = AppointmentCalendar::findOrFail($id);

		return view('appointment_calendars.show', compact('appointment_calendar'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$appointment_calendar = AppointmentCalendar::findOrFail($id);

		return view('appointment_calendars.edit', compact('appointment_calendar'));
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
		$appointment_calendar = AppointmentCalendar::findOrFail($id);

		

		$appointment_calendar->save();

		return redirect()->route('appointment_calendars.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$appointment_calendar = AppointmentCalendar::findOrFail($id);
		$appointment_calendar->delete();

		return response()->json(['message' => 'Appointment added successfully.','action'=>'redirect','do'=>url('/appointment_calendars')], 200);
	}

	public function appointment_datatable()
	{
		# code...
		return Datatables::of(AppointmentCalendar::query())

    		->edit_column('start',function($row){
    		return (strtotime($row->start))?date('d-M-Y H:i:s',strtotime($row->start)):"-";
    	})
    		->edit_column('end',function($row){
    		return (strtotime($row->end))?date('d-M-Y H:i:s',strtotime($row->end)):"-";
    	})
    		->edit_column('title',function($row){
    			return '<span class="fc-event-dot" style="background-color:'.$row->background_color.'"></span>'.$row->title;
    		})
    		->add_column('options',function($row){
    			return '
                                    <a class="btn btn-xs btn-warning" href="'.route('appointment_calendars.edit', $row->id) .'"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                    <form action="'. route('appointment_calendars.destroy', $row->id).'" method="POST" style="display: inline;" onsubmit="if(confirm(\'Delete? Are you sure?\')) { return true } else {return false };">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="'.csrf_token() .'">
                                        <button type="submit" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                                    </form>';
    		})
    	->make(true);
	}

}
