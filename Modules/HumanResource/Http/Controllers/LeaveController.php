<?php

namespace Modules\HumanResource\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Modules\HumanResource\Http\Requests\AddLeaveRequest;

use Modules\HumanResource\Entities\Employee;
use Modules\HumanResource\Entities\EmployeeLeaves;
use Modules\HumanResource\Entities\EmployeeAttendance;
use Modules\HumanResource\Entities\MonthBalance;
use Modules\HumanResource\Entities\PaymentAttendance;
use Modules\HumanResource\Entities\EmployeeBonus;
use Modules\humanresource\Entities\PaymentRelease;
use Datatables;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function __construct()
    {
        // \LogActivity::addToLog();
        \View::share('title',"Leaves Management");
        \View::share('load_head',true);
        // \View::share('product_menu',true);

    }


    public function datatable()
    {
        $query = EmployeeLeaves::select('employee_leaves.*', 'employee.name','employee.type as employee_type')
                        ->join('employee', 'employee.id', '=', 'employee_leaves.employee_id')
                        ->get();
        
        return DataTables::of($query)
        ->addColumn('action', function (EmployeeLeaves $users) {
                return '
                    <a href="#" data-id="'.$users->id.'" class="btn btn-outline-danger m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill remove-item" title="Delete"><i class="la la-trash"></i></a>
                    ';
               
            })->make(true);
    }

    public function index()
    {
        if (!is_allowed('access-hr')) {
            return redirect('/');
        }

        return view('humanresource::leave.index');        
    }

    public function create()
    {
        $employees = Employee::all();

        return view('humanresource::leave.create', compact('employees'));
    }

    public function multileave()
    {
        $employee=Employee::all();

        return view('humanresource::leave.mul_leaves',compact('employee'));
    }

    public function store(AddLeaveRequest $request)
    {
        $from = date('Y-m-d', strtotime($request->from));
        $to = date('Y-m-d', strtotime($request->to));
        $firstdate=date('Y-m-01',strtotime($request->from));
        $date=date('Y-m',strtotime($request->from));
        $start_month = new Carbon('first day of '.$date);
        $end_month = new Carbon('last day of '.$date);  
        
        DB::beginTransaction();

        try {
            while (strtotime($from) <= strtotime($to)) {
                $attendance = EmployeeAttendance::where('employee_id', $request->employee)->where('day', $from)
                                        ->first();
                                        // ->where('shift', $request->shift)
                                        
        if ($attendance) {
            DB::rollback();
    
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Attendance already marked against this date and shift!');
                if ($request->ajax())
            {
                 return response(["title"=>"Unable to mark leave",'content'=>"It seems like Employee is Present"], 500);
                 
            }
            
            return redirect('hr/leave');
        }

        $leave = EmployeeLeaves::where('employee_id', $request->employee)
                                ->where('day', $from)
                                ->first();
        if ($leave) {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Cannot mark attendance as employee is on leave!');
            if ($request->ajax())
            {
                return response(["title"=>"Unable to mark leave",'content'=>"It seems like Employee is Already on leave"], 500);
            }

            return redirect('hr/leave');
        }
                $leave = new EmployeeLeaves();
                $leave->employee_id = $request->employee;
                $leave->day = $from;
                $leave->type = $request->type;  
                $leave->leave_type=$request->leave_type;             
                $leave->save();
                $from = date('Y-m-d', strtotime($from . '+1 day'));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $e->getMessage());  
            if ($request->ajax())
            {
                return response(['error'=>$e->getMessage()], 500);
            }
            return redirect('/hr/');   
        }

        $request->session()->flash('message.level', 'success');
        $request->session()->flash('message.content', 'Leave(s) added successfully!');        
        if ($request->ajax())
            {
                return response(['success'], 200);
            }
        return redirect('/hr/leaves');   
    }

    public function storemultileave(Request $request)
    {
        // \Log::info(print_r($request->all(), true));
        // exit();

        try {
            $count = 0;
            foreach($request->employee as $key=>$value)
            {



                if (!$request->from[$key] || !$request->to[$key])
                {
                    continue;
                }
        $from = date('Y-m-d', strtotime($request->from[$key]));
        $to = date('Y-m-d', strtotime($request->to[$key]));
        // \Log::info(print_r($from, true));
        // exit();
        // \Log::info(print_r($to, true));
        // DB::beginTransaction();


            while (strtotime($from) <= strtotime($to)) {

                $attendance = EmployeeAttendance::where('employee_id', $request->employee[$key])->whereDate('day', $from)
                                        ->first();
             
                                        // ->where('shift', $request->shift)
                                        
        if ($attendance) {
            DB::rollback();
    
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Attendance already marked against this date and shift!');
                if ($request->ajax())
            {
                 return response(["title"=>"Unable to mark leave",'content'=>"It sesms like Employee is Present"], 500);
                 
            }
            
            return redirect('hr/leaves');
        }
        
        $leav = EmployeeLeaves::where('employee_id', $request->employee[$key])
                                ->whereDate('day', $from)
                                ->first();
        if ($leav) {
            DB::rollback();

            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Cannot mark attendance as employee is on leave!');
            if ($request->ajax())
            {
                return response(["title"=>"Unable to mark leave",'content'=>"It seems like Employee is Already on leave"], 500);
            }
            return redirect('hr/leaves');
        }



                $leave = new EmployeeLeaves();
                $leave->employee_id = $request->employee[$key];
                $leave->day = $from;
                $leave->type = $request->type[$key];  
                $leave->leave_type=$request->leave_type[$key];   
                $leave->save();
                
                $count++;
                $from = date('Y-m-d', strtotime($from . '+1 day'));
                 }
             }

            if ($count != 0) {
                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', 'Leave(s) added successfully!');
            
                return redirect('/hr/leaves');
            }
            else {
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'No Employee selected!');        
                
                return redirect('/hr/leave/mul');
            }
            
            // DB::commit();
        }//end try
         catch (\Exception $e) {
            // DB::rollback();
            
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', $e->getMessage());  

            return redirect('/hr/leaves');   
        }
    }
    
    
    public function show()
    {
        return view('humanresource::leave.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('humanresource::leave.edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try{
                EmployeeLeaves::whereId($id)->delete();
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
