<?php

namespace Modules\HumanResource\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Modules\HumanResource\Http\Requests\AddAttendanceRequest;
use Modules\HumanResource\Http\Requests\AllAttendanceRequest;
use Modules\HumanResource\Http\Requests\EditAttendanceRequest;

use Modules\HumanResource\Entities\Employee;
use Modules\HumanResource\Entities\EmployeeAttendance;
use Modules\HumanResource\Entities\EmployeeLeaves;
use Modules\HumanResource\Entities\PaymentAttendance;
use Modules\HumanResource\Entities\PaymentRelease;
use Modules\HumanResource\Entities\EmployeeBonus;
use Modules\HumanResource\Entities\MonthBalance;
use Yajra\Datatables\Facades\Datatables;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        // \LogActivity::addToLog();
        \View::share('title',"Leaves Management");
        \View::share('load_head',true);
        // \View::share('product_menu',true);

    }


    private function overtime($date)
    {
        $day = date('w', strtotime($date));

        if ($day == 5) {
            return true;
        }

        return false;
    }

    public function datatable()
    {
        $query = EmployeeAttendance::select('employee_attendances.*', 'employee.name','employee.type as employee_type')
                        ->join('employee', 'employee.id', '=', 'employee_attendances.employee_id')
                        ->orderBy('employee_attendances.day', 'desc')
                        ->get();
        
        return DataTables::of($query)
        ->addColumn('action', function (EmployeeAttendance $users) {
                return '
                    <a href="'.url('/hr/attendance/edit/'.$users->id).'" data-id="'.$users->id.'" class="btn btn-outline-success m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill" title="Edit"><i class="glyphicon glyphicon-edit"></i></a>
                    <a href="#" data-id="'.$users->id.'" class="btn btn-outline-danger m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill remove-item" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
                    ';
            })
            ->addColumn('overtime', function ($query) {
                return $query->overtime == 1 ? : 0;
            })->make(true);

    }

    public function check_in ($id, Request $request)
    {
        if ($id) {
            $attendance = EmployeeAttendance::where('employee_id', $id)
                                            ->where('day', date('Y-m-d'))
                                            ->first();

            if ($attendance) {
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Employee already checked in!');
            }
            else {
                $attendance = new EmployeeAttendance();
                $attendance->employee_id = $id;
                $attendance->day = date('Y-m-d');
                $attendance->time_in = date('H:i:s');
                $attendance->save();

                if ($attendance->id) {
                    $request->session()->flash('message.level', 'success');
                    $request->session()->flash('message.content', 'Employee checked in successfully!');        
                }
                else {
                    $request->session()->flash('message.level', 'danger');
                    $request->session()->flash('message.content', 'Unable to check in!');           
                }
            }
        }
        else {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'No employee selected for check in!');
        }

        return redirect('/hr/');    
    }

    public function check_out($id, Request $request)
    {
        if ($id) {
            $attendance = EmployeeAttendance::where('employee_id', $id)
                                            ->whereDate('day', date('Y-m-d'))
                                            ->first();

            if (!$attendance) {
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Employee not checked in!');
            } else {
                $attendance->time_out = date('H:i:s');
                $total_hours = date('H:i:s', strtotime($attendance->time_in . '+12 hours'));

                if (strtotime($attendance->time_out) > strtotime($total_hours)) {
                    $diff = strtotime($attendance->time_out) - strtotime($total_hours);
                    $attendance->overtime = date('H:i:s', $diff);
                }
 
                
                $attendance->save();

                if ($attendance->id) {
                    $request->session()->flash('message.level', 'success');
                    $request->session()->flash('message.content', 'Employee checked out successfully!');        
                }
                else {
                    $request->session()->flash('message.level', 'danger');
                    $request->session()->flash('message.content', 'Unable to check out!');           
                }
            }
        } else {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'No employee selected for check out!');
        }

        return redirect('/hr/');    
    }

    public function index()
    {
        if (!is_allowed('access-hr')) {
            return redirecat('/');
        }

        return view('humanresource::attendance.index');        
    }

    public function attendance()
    {
        $employees = Employee::all();
        
        return view('humanresource::attendance.attendance', compact('employees'));        
    }

    public function create()
    {
        $employees = Employee::all();
        
        return view('humanresource::attendance.create', compact('employees'));           
    }

    public function machine_attendance(Request $request)
    { 
        // file_put_contents('php_text.txt',json_encode($users));
        // return "ok";
        // $json_data = json_decode(file_get_contents('http://ims.umair.pk/attendance_users_json.txt', true));
        $data = $request->all();
        $json_data = json_decode($data[0]);
        $records = $json_data->records;
        $users = $json_data->users;

        $employee_ids = [];
        foreach($records as $key => $record)
        {
            $dateTime = Carbon::parse($record->timestamp);

            $previous_attendance = EmployeeAttendance::latest('id')->first();
            if($previous_attendance)
            {
                $time = ($previous_attendance->time_out != '00:00:00') ? $previous_attendance->time_out : $previous_attendance->time_in;
                $previous_dateTime = date('Y-m-d H:i:s', strtotime("$previous_attendance->day $time"));
            }
            else{
                $previous_dateTime = '';
            }
            if($record->punch == '0')
            {
                $record_dateTime = $dateTime->format('Y-m-d H:i:s');
            }
            elseif($record->punch == '1')
            {
                $record_dateTime = $dateTime->format('Y-m-d H:i:s');
            }

            if($record_dateTime > $previous_dateTime )
            {
                if($record->status == '1' || $record->status == '3'  || $record->status == '4')
                {
                    $status = 'Present';
                }
                if($record->punch == '0')
                {
                    $check_in = $dateTime->format('Y-m-d H:i:s');
                    $check_out = '';
                }
                elseif($record->punch == '1')
                {
                    $check_in = '';
                    $check_out = $dateTime->format('Y-m-d H:i:s');
                }
                $attendance_request[$key]['uid']        = $record->uid;
                $attendance_request[$key]['employee']   = $record->user_id;
                $attendance_request[$key]['day']        = $dateTime->format('Y-m-d');
                $attendance_request[$key]['status']     = $status;
                $attendance_request[$key]['punch']      = $record->punch;
                $attendance_request[$key]['check_in']   = $check_in;
                $attendance_request[$key]['check_out']  = $check_out;
                $attendance_request[$key]['shift']      = 'day';

                if (!in_array($record->user_id, $employee_ids))
                {
                    $employee_ids[] = $record->user_id;
                } 
            }
        }
        $existing_employee = Employee::pluck('id')->toArray();
        $employee_not_exists = array_diff($employee_ids, $existing_employee);

        DB::beginTransaction();
        try {
            if(!empty($employee_not_exists))
            {
                foreach($users as $user)
                {
                    if (in_array($user->user_id, $employee_not_exists))
                    {
                        $new_employee = new Employee();
                        $new_employee->id =  $user->user_id;
                        $new_employee->name =  $user->user_name; 
                        $new_employee->save();
                    }
                    DB::commit();
                }
            }
            if($attendance_request)
            {
                foreach($attendance_request as $single_request)
                {
                    $day = date('Y-m-d', strtotime($single_request['day']));
                    $month_year=date('Y-m', strtotime($single_request['day']));

                    // $start_month = new Carbon('first day of '.$month_year);
                    // $end_month = new Carbon('last day of '.$month_year);
                    // $start_month = $start_month->format("Y-m-d");
                    // $end_month = $end_month->format("Y-m-d");

                    $employe=Employee::where('id', '=', $single_request['employee'])->first();
                    if($employe)
                    {
                        $weekday=$employe->working_days;
                        $salary=$employe->salary;
                        $activedays=getActiveDays(unserialize($weekday), $month_year);
                        $holidays=getholidays(unserialize($weekday),$month_year,$single_request['employee']);
                        $strt_time=strtotime($employe->start_time);
                        $endd_time=strtotime($employe->end_time);
                        $workhour=($endd_time-$strt_time)/3600;
                        $totaltimetowork=$activedays*$workhour;
                        $salaryperday=$salary/$activedays;
                        $salaryperhour=$salaryperday/$workhour;

                        $attendance = EmployeeAttendance::where('employee_id', $single_request['employee'])->where('day', $day)->first();

                        if ($attendance) 
                        {
                            if($single_request['punch'] == '0')
                            {
                                if(empty($attendance->time_in) || $attendance->time_in == '00:00:00')
                                {
                                    $attendance->time_in = $single_request['check_in'];
                                    $attendance->update();
                                }
                            }
                            elseif($single_request['punch'] == '1')
                            {
                                if(empty($attendance->time_out) || $attendance->time_out == '00:00:00')
                                {
                                    $attendance->time_out = $single_request['check_out'];
                                    $attendance->update();
                                }
                            }
                        }
                        else{
                            $leave = EmployeeLeaves::where('employee_id', $single_request['employee'])->where('day', $day)->first();
                            if(!$leave)
                            {
                                $attendance = new EmployeeAttendance();
                                $payattend=new PaymentAttendance();
                                $attendance->employee_id = $single_request['employee'];
                                $attendance->day = $day;
                                // $attendance->status = $single_request['status'];
                                $attendance->shift = (isset($single_request['shift']) && $single_request['shift']) ? $single_request['shift'] : '';
                                $attendance->time_in = $single_request['check_in'] ? date('Y-m-d H:i:s', strtotime($single_request['check_in'])) : date('Y-m-d H:i:s', strtotime('0000-00-00 00:00:00'));
                                $attendance->time_out = $single_request['check_out'] ? date('Y-m-d H:i:s', strtotime($single_request['check_out'])) : date('Y-m-d H:i:s', strtotime('0000-00-00 00:00:00'));
                                $totalworksaday = strtotime($attendance['time_out']) - strtotime($attendance['time_in']);
                                $totalworksaday=$totalworksaday/3600;
                                $salarythisday=$totalworksaday*$salaryperhour;

                                $payattend->employee_id=$single_request['employee'];
                                $payattend->date=$day;
                                $payattend->payment_actual=$salarythisday;

                                $total_hours = date('Y-m-d H:i:s', strtotime($attendance['time_in'] . '+8 hours'));                                                              
                                if (strtotime($attendance->time_out) > strtotime($total_hours)) 
                                {
                                    $diff = strtotime($attendance->time_out) - strtotime($total_hours);
                                    $attendance->overtime = date('H:i:s', $diff);
                                } 
                                $attendance->save();
                                $payattend->save();
                            }
                        }
                        DB::commit();
                    }
                }
                return response()->json(['message' => 'Attendance saved successfully!']);
            }
            else{
                return response()->json(['message' => 'Attendance already marked against these dates!']);
            }
            }
            catch (\Exception $e)
            {
                DB::rollback();
                return response(['error'=>$e->getMessage()], 500);
            }
    }

    public function single_attendance(AddAttendanceRequest $request)
    {   
        $day = date('Y-m-d');
        $month_year=date('Y-m');
        if (isset($request->day))
        {
            $day = date('Y-m-d', strtotime($request->day));
            $month_year=date('Y-m', strtotime($request->day));
           

        } 

        // $start_month = new Carbon('first day of '.$month_year);
        // $end_month = new Carbon('last day of '.$month_year);
        // $start_month = $start_month->format("Y-m-d");
        // $end_month = $end_month->format("Y-m-d");

        $employe=Employee::where('id', '=', $request->employee)->first();
        $weekday=$employe->working_days;
        $salary=$employe->salary;
        $activedays=getActiveDays(unserialize($weekday), $month_year);
        $holidays=getholidays(unserialize($weekday),$month_year,$request->employee);
        $strt_time=strtotime($employe->start_time);
        $endd_time=strtotime($employe->end_time);
        $workhour=($endd_time-$strt_time)/3600;
        $totaltimetowork=$activedays*$workhour;
        $salaryperday=$salary/$activedays;
        $salaryperhour=$salaryperday/$workhour;


        DB::beginTransaction();

        try {

                $attendance = EmployeeAttendance::where('employee_id', $request->employee)->where('day', $day)->first();
                if ($attendance) 
                {
                    DB::rollback();
            
                    $request->session()->flash('message.level', 'danger');
                    $request->session()->flash('message.content', 'Attendance already marked against this date!');
                    if ($request->ajax())
                    {
                        return response(['error'], 500);
                    }    
                    return redirect('hr/attendance');
                }
                $leave = EmployeeLeaves::where('employee_id', $request->employee)->where('day', $day)->first();
                if ($leave)
                {
                    $request->session()->flash('message.level', 'danger');
                    $request->session()->flash('message.content', 'Cannot mark attendance as employee is on leave!');
                    if ($request->ajax())
                    {
                        return response(['error'=>$e->getMessage()], 500);
                    }
                    return redirect('hr/attendance');
                }
                $attendance = new EmployeeAttendance();
                $payattend=new PaymentAttendance();
                $attendance->employee_id = $request->employee;
                $attendance->day = $day;
                $attendance->shift = $request->shift;
                $attendance->time_in = date('Y-m-d H:i:s', strtotime($request->time_in));
                $attendance->time_out = date('Y-m-d H:i:s', strtotime($request->time_out));
                $totalworksaday = strtotime($attendance->time_out) - strtotime($attendance->time_in);
                $totalworksaday=$totalworksaday/3600;
                $salarythisday=$totalworksaday*$salaryperhour;

                $payattend->employee_id=$request->employee;
                $payattend->date=$day;
                $payattend->payment_actual=$salarythisday;


                $total_hours = date('Y-m-d H:i:s', strtotime($attendance->time_in . '+8 hours'));                                                              
                if (strtotime($attendance->time_out) > strtotime($total_hours)) 
                {
                    $diff = strtotime($attendance->time_out) - strtotime($total_hours);
                    $attendance->overtime = date('H:i:s', $diff);
                } 
                $attendance->save();
                $payattend->save();

                DB::commit();

                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', 'Attendance saved successfully!');
            }
            catch (\Exception $e)
            {
            DB::rollback();
            
                if ($request->ajax())
                {
                return response(['error'=>$e->getMessage()], 500);
                }
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Something went wrong!');
            }

        return redirect('hr/attendances');
    }

    public function all_attendance(AllAttendanceRequest $request)
    {
        $day = date('Y-m-d');
        $month_year=date('Y-m');
        if (isset($request->day)) {
            $day = date('Y-m-d', strtotime($request->day));
            $month_year=date('Y-m', strtotime($request->day));

        }

        DB::beginTransaction();

        try {
            foreach ($request->employee as $key => $value) {

        $employe=Employee::where('id', '=', $request->employee[$key])->first();
        $weekday=$employe->working_days;
        $salary=$employe->salary;
        $activedays=getActiveDays(unserialize($weekday), $month_year);
        $holidays=getholidays(unserialize($weekday),$month_year,$request->employee[$key]);
        $strt_time=strtotime($employe->start_time);
        $endd_time=strtotime($employe->end_time);
        $workhour=($endd_time-$strt_time)/3600;
        $totaltimetowork=$activedays*$workhour;
        $salaryperday=$salary/$activedays;
        $salaryperhour=$salaryperday/$workhour;

                if(!$request->time_in[$key])
                {
                    continue;
                }
                $attendance = EmployeeAttendance::where('employee_id', $request->employee[$key])
                                            ->where('day', $day)
                                            ->first();
                if ($attendance) {
                    DB::rollback();
            
                    $request->session()->flash('message.level', 'danger');
                    $request->session()->flash('message.content', 'Attendance already marked against this date!');
                    if ($request->ajax())
            {
                
                return response(['error'], 500);
            }
                    
                    return redirect('hr/attendance/add');
                }

                $leave = EmployeeLeaves::where('employee_id', $request->employee[$key])
                                        ->where('day', $day)
                                        ->first();
                if ($leave) {
                    $request->session()->flash('message.level', 'danger');
                    $request->session()->flash('message.content', 'Cannot mark attendance as employee is on leave!');
                    if ($request->ajax())
            {
                return response(['error'=>$e->getMessage()], 500);
            }
                    return redirect('/hr/attendance/add');
                }

                 
                $attendance = new EmployeeAttendance();
                $payattend= new PaymentAttendance();

                $attendance->employee_id = $request->employee[$key];
                $attendance->day = $day;
                $attendance->shift = $request->shift[$key];
                $attendance->time_in = date('Y-m-d H:i:s', strtotime($request->time_in[$key]));
                $attendance->time_out = date('Y-m-d H:i:s', strtotime($request->time_out[$key]));  

                $totalworksaday = strtotime($attendance->time_out) - strtotime($attendance->time_in);
                $totalworksaday=$totalworksaday/3600;
                $salarythisday=$totalworksaday*$salaryperhour;

                $payattend->employee_id=$request->employee[$key];
                $payattend->date=$day;
                $payattend->payment_actual=$salarythisday;

                $total_hours = date('Y-m-d H:i:s', strtotime($attendance->time_in . '+12 hours'));
                
                if (strtotime($attendance->time_out) > strtotime($total_hours)) {
                    $diff = strtotime($attendance->time_out) - strtotime($total_hours);
                    $attendance->overtime = date('H:i:s', $diff);
                }       
                $payattend->save();
                $attendance->save();
            }

            DB::commit();

            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Attendance saved successfully!');
            return redirect('hr/attendances');
        } catch (\Exception $e) {
            DB::rollback();
            
                if ($request->ajax())
            {
                return response(['error'=>$e->getMessage()], 500);
                
            }
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Something went wrong!');

        }
    }

    public function store(AllAttendanceRequest $request)
    {
        $day = date('Y-m-d', strtotime($request->day));
        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to));
        $month_year=date('Y-m', strtotime($request->from));

        $date=date('Y-m',strtotime($request->day));
        $firstdate=date('Y-m-01',strtotime($request->day));

        $employe=Employee::where('id', '=', $request->employee)->first();
        $weekday=$employe->working_days;
        $salary=$employe->salary;
        $activedays=getActiveDays(unserialize($weekday), $month_year);
        $holidays=getholidays(unserialize($weekday),$month_year,$request->employee);
        $strt_time=strtotime($employe->start_time);
        $endd_time=strtotime($employe->end_time);
        $workhour=($endd_time-$strt_time)/3600;
        $totaltimetowork=$activedays*$workhour;
        $salaryperday=$salary/$activedays;
        $salaryperhour=$salaryperday/$workhour;

          while (strtotime($from) < strtotime($to)) {
        $attendance = EmployeeAttendance::where('employee_id', $request->employee)
                                        ->where('shift', $request->shift)
                                        ->whereDate('day', $from)
                                        ->first();
        $emplyee = Employee::where('id',$request->employee)->first();
        
         $strtime=decimalHours($emplyee->start_time);
         $endtime=decimalHours($emplyee->end_time);                           
        if ($attendance) {
            DB::rollback();
    
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Attendance already marked against this date and shift!');
                if ($request->ajax())
            {
                 return response(["title"=>"Unable to mark Attendace",'content'=>"It seams like Employe is already Present"], 500);
            }
            
            return redirect('hr/attendance');
        }

        $leave = EmployeeLeaves::where('employee_id', $request->employee)
                                ->whereDate('day', $from)
                                ->first();
        if ($leave) {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Cannot mark attendance as employee is on leave!');
            if ($request->ajax())
            {
                return response(["title"=>"Unable to mark Attendance",'content'=>"It seams like Employe is on leave"], 500);
            }
            return redirect('/hr/attendance');
        }
        $attendance = new EmployeeAttendance();
        $payattend = new PaymentAttendance();
        $attendance->employee_id = $request->employee;
        $attendance->day=$day;
        $payattend->employee_id=$request->employee;
        $payattend->date=$day;
        if ($request->ajax())
        {
        $attendance->day = $from;
        $payattend->date = $from; 
        $date=date('Y-m',strtotime($request->from));
        $firstdate=date('Y-m-01',strtotime($request->from));
        }
        $attendance->shift = $request->shift;
        $attendance->time_in = date('Y-m-d H:i:s', strtotime($request->time_in));
        $attendance->time_out = date('Y-m-d H:i:s', strtotime($request->time_out));
        
        $totalworksaday = strtotime($attendance->time_out) - strtotime($attendance->time_in);
        $totalworksaday=$totalworksaday/3600;
        $salarythisday=$totalworksaday*$salaryperhour;
        
        $payattend->payment_actual=$salarythisday;

        $total_hours = date('Y-m-d H:i:s', strtotime($attendance->time_in . '+12 hours'));
        
        if (strtotime($attendance->time_out) > strtotime($total_hours)) {
            $diff = strtotime($attendance->time_out) - strtotime($total_hours);
            $attendance->overtime = date('Y-m-d H:i:s', $diff);
        }
        $attendance->save();
        $payattend->save();
       
        
        


        $from = date('Y-m-d', strtotime($from . '+1 day'));

    }

        if (empty($attendance->id)) {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Something went wrong!');
         if ($request->ajax())
            {
                return response(['error'], 500);
            }
            return redirect('/hr/attendance');
        } 

        $request->session()->flash('message.level', 'success');
        $request->session()->flash('message.content', 'Attendance saved successfully!');
         if ($request->ajax())
            {
                return response(['message'=>'success','data'=>$attendance], 200);
            }

        return redirect('hr/attendances');
    }

    public function edit($id)
    {
        $attendance = EmployeeAttendance::where('id', '=', $id)->first();
        
        return view('humanresource::attendance.edit', compact('attendance'));        
    }

    public function update(EditAttendanceRequest $request)
    {
        $month_year=date('Y-m', strtotime($request->day));
        $attendance = EmployeeAttendance::where('id', '=', $request->id)->first();
        $payattend= PaymentAttendance::where('date', '=', $request->day)->first();
        $employe=Employee::where('id', '=', $attendance->employee_id)->first();
        $weekday=$employe->working_days;
        $salary=$employe->salary;
        $activedays=getActiveDays(unserialize($weekday), $month_year);
        $holidays=getholidays(unserialize($weekday),$month_year,$attendance->employee_id);
        $strt_time=strtotime($employe->start_time);
        $endd_time=strtotime($employe->end_time);
        $workhour=($endd_time-$strt_time)/3600;
        $totaltimetowork=$activedays*$workhour;
        $salaryperday=$salary/$activedays;
        $salaryperhour=$salaryperday/$workhour;

        if (!$attendance) {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'No attendance found!');
            if ($request->ajax())
            {
                return response(['message'=>'error is there'], 500);
            }
            return redirect('/hr/attendances');
        }

        $attendance->time_in = date('Y-m-d H:i:s', strtotime($request->time_in));
        $attendance->time_out = date('Y-m-d H:i:s', strtotime($request->time_out)); 

        $totalworksaday = strtotime($attendance->time_out) - strtotime($attendance->time_in);
        $totalworksaday=$totalworksaday/3600;
        $salarythisday=$totalworksaday*$salaryperhour; 

        $payattend->payment_actual=$salarythisday;
        $total_hours = date('Y-m-d H:i:s', strtotime($attendance->time_in . '+8 hours'));
        
        if (strtotime($attendance->time_out) > strtotime($total_hours)) {
            $diff = strtotime($attendance->time_out) - strtotime($total_hours);
            $attendance->overtime = date('H:i:s', $diff);
        }             
        
        $attendance->save();
        $payattend->save();

        $request->session()->flash('message.level', 'success');
        $request->session()->flash('message.content', 'Attendance updated successfully!');
        if ($request->ajax())
            {
                return response(['message'=>'sucess'], 200);
            }
        
        return redirect('/hr/attendances');       
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try
        {
        $atendance=EmployeeAttendance::whereId($id)->first();
        PaymentAttendance::where('date', $atendance->day)->where('employee_id',$atendance->employee_id)->delete();
        EmployeeAttendance::whereId($id)->delete();
        
        
        DB::commit();
        return response()->json(['done']);
        }
        catch (\Exception $e)
            {
            DB::rollback();
            return response($e->getMessage()."Line:".$e->getLine(),500);
            }
    }
}
