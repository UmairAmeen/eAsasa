<?php

namespace Modules\HumanResource\Http\Controllers;

use Illuminate\Http\Request;
use Modules\HumanResource\Http\Requests\AddEmployeeRequest;
use Modules\HumanResource\Http\Requests\EditEmployeeRequest;
use Modules\HumanResource\Http\Requests\AddBonusRequest;
use Modules\HumanResource\Http\Requests\DownloadAttendanceListRequest;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Modules\HumanResource\Entities\EmployeeSalaryLog;
use Modules\HumanResource\Entities\Employee;
use Modules\HumanResource\Entities\EmployeeAttendance;
use Modules\HumanResource\Entities\EmployeeLeaves;
use Modules\HumanResource\Entities\EmployeeBonus;
use Modules\HumanResource\Entities\Holiday;
use Modules\HumanResource\Entities\PaymentRelease;
use Modules\HumanResource\Entities\MonthBalance;
use Modules\HumanResource\Entities\PaymentAttendance;
// use Modules\Setting\Entities\Setting;
use App\Setting;

use Excel;
use Datatables;
use Exception;
use Input;
use Carbon\Carbon;

class HumanResourceController extends Controller
{
    public $employees;

    public function __construct()
    {
        // \LogActivity::addToLog();
        \View::share('title',"Human Resources");
        \View::share('load_head',true);
        // \View::share('product_menu',true);

    }

    public function download_attendance_list() 
    {
        return view('humanresource::download_attendance');
    }

    public function attendance_list(DownloadAttendanceListRequest $request)
    {
        $start_month = date('Y-m-d', strtotime($request->from));
        $end_month = date('Y-m-d', strtotime($request->to));

        $this->employees = DB::table('employee')
            ->select('employee.id', 'employee.name', 'employee.position', 'employee.type', 'employee_attendances.day', 
                'employee_attendances.time_in', 'employee_attendances.time_out', 'employee_attendances.overtime'
                )
            ->join('employee_attendances', 'employee.id', '=', 'employee_attendances.employee_id')
            ->whereBetween('employee_attendances.day', array($start_month, $end_month))
            ->get();

        Excel::create('Attendance List', function($excel) {
            $excel->sheet('New sheet', function($sheet) {
                $sheet->loadView('humanresource::sheets.attendances', ['employees'=>$this->employees]);
            });
        })->export('xlsx');   
    }

    public function download_attendance_summary() 
    {
        return view('humanresource::download_summary');
    }

    public function salary(Request $request)
    {


        $start_month = new Carbon('first day of this month');
        $end_month = new Carbon('last day of this month');

        $month = date("Y-m");



         if ($request->month_year)
            {
                $start_month = new Carbon('first day of '.$request->month_year);
                $end_month = new Carbon('last day of '.$request->month_year);
                $month = $request->month_year;
            }
            $sm = $start_month;
            $em = $end_month;
        
        $this->employees = Employee::all();
        foreach ($this->employees as $key => $employee) {
            $this->employees[$key]['bonus'] = 0;
            $this->employees[$key]['deduction'] = 0;
            $this->employees[$key]['total_overtime'] = 0;
            $this->employees[$key]['total_leaves'] = 0;
            $this->employees[$key]['full_leave'] = 0;
            $this->employees[$key]['half_leave'] = 0;
            $this->employees[$key]['total_attendance'] = 0;
            $this->employees[$key]['active_days'] = 0;
            $this->employees[$key]['public_holiday'] = 0;


                // echo $month;
            $daysinmonth=date('t',strtotime($month));
            $company=Setting::first();
            $year_x = $sm->format('Y');
            $month_x = $sm->format('m');
            $start_month = $sm->format("Y-m-d");
            $end_month = $em->format("Y-m-d");

            $id = $employee->id;
            
            

            $total_days_in_week = unserialize($employee->working_days);

            $total_working_days = getActiveDays($total_days_in_week, $month);

            $leaves = getLeaves($total_days_in_week, $month, $id);
            $holidays = getholidays($total_days_in_week, $month, $id);

            // $total_working_days -= $leaves;
            $total_working_days -= $holidays;

            $rate_per_day = $employee->salary/$total_working_days;
            $total_days_worked = getPresentDays($total_days_in_week, $month, $id) + $leaves;
            $working_hours = calculateworkinghours($employee->start_time, $employee->end_time);
            $rate_per_hour = $rate_per_day/$working_hours;
            $total_working_hours = $working_hours*($total_working_days-$leaves);

            $days_absent = getAbsent($total_days_in_week, $month, $id);
            $attendance_hours = getWorkingHours($total_days_in_week, $month, $id);

            //Basic Salary
            $salary  = $employee->salary;
            //OverTime Calculation
            $overtime = $overtimeamount = 0;
            $time_lag = $time_lag_amount = 0;
            //Bonus / Reward
            $total_bonus = EmployeeBonus::where('date','>=',$start_month)->where('date','<=',$end_month)->where('employee_id',$id)->where('bonus','>',0)->sum('bonus');

            $absent = $days_absent;
            $absent_amount = $days_absent * $rate_per_day;
            //Deduction
            $total_deduction = abs(EmployeeBonus::where('date','>=',$start_month)->where('date','<=',$end_month)->where('employee_id',$id)->where('bonus','<',0)->sum('bonus'));

            if (!$employee->billing_type)
            {
                $employee->billing_type = $company->hr_billing_type;
            }
            
            if ($employee->billing_type == "per_hour")
            {
                //Time Lag Amount
                //Basicall on all working days, less amount of task done
                //like working on days for 8 hours
                //total work for 4 days = 32 hours
                //total worked hours = 31hours
                //time lagged = 32 - 31 => 1 hour
                $time_lag = ($total_days_worked * $working_hours) - $attendance_hours;
                if ($time_lag > 0)
                {
                    $time_lag = round($time_lag, 2);
                    $time_lag_amount = $time_lag * $rate_per_hour;
                }else{
                    $overtime = abs($time_lag);
                    $overtimeamount = $overtime * $rate_per_hour;
                    $time_lag = 0;
                }
            }else{
                //otherwise it's per day
                if ($total_days_worked > $total_working_days)
                {
                    $overtime = $total_days_worked - $total_working_days;;
                    $overtimeamount = $overtime * $rate_per_day;   
                }
                // $salary - $absent days
            }

            if ($employee->billing_type == "no_deduction")
            {
                $absent = 0;
                $absent_amount = 0;
            
            }
            
            //Overtime is calculated from time lag
            

            //Absent

            


            

            $total_days_worked -= $leaves; //remove leave count for salary slip display


            $total_computed_salary = round($salary + $overtimeamount + $total_bonus - ($absent_amount + $time_lag_amount + $total_deduction),0);
          


           $this->employees[$key]['bonus'] = $total_bonus;
            $this->employees[$key]['deduction'] = $absent_amount + $time_lag_amount + $total_deduction;
            $this->employees[$key]['total_overtime'] = $overtime;
            $this->employees[$key]['total_overtime_amount'] = $overtimeamount;
            $this->employees[$key]['total_leaves'] = $leaves;
            $this->employees[$key]['absent'] = ($absent)?:0;
            $this->employees[$key]['total_attendance'] = $total_days_worked;
            $this->employees[$key]['active_days'] = $total_working_days;
            $this->employees[$key]['public_holiday'] = $holidays;
            $this->employees[$key]['total'] = $total_computed_salary;
           
           
        }//endforeach





        return view('humanresource::salary',['employees'=>$this->employees],compact('start_month','end_month'));
    }

    public function edit_released(Request $request,$id)
    {
        
        $mth=date('Y-m-01', strtotime($request->date));
        $mthend=date('Y-m-t', strtotime($request->date));
        $pre_mth = Date("Y-m-01", strtotime($mth . " last month"));

        $pay = PaymentRelease::where('id', '=', $id)->first();
        
        $employeid=$pay->employee_id;

       $pay->user_id = $request->userid;
       // $pay->employee_id = $id;
       $pay->date = $request->date;
       $pay->payment_release = $request->amount;
       $pay->status = $request->status;
       $remains=$pay->payment-$pay->payment_release;
       if($remains>0)
       {
         $pay->payment_remaining=$remains;
         $pay->payment_advance=0;
       }
       else
       {
        $pay->payment_remaining=0;
         $pay->payment_advance=abs($remains);
       }
       $actualsalary=$pay->payment;
       $pay->save();

       return redirect('hr/salary/pay/all')->with('success', 'Imported successfully');
    }

    public function release_pay(Request $request,$id)
    {


        $remainingsalary=0;
        $advancesalary=0;
        $actualsalary=str_replace(',', '', $request->amount);
        $released_salary=$request->amountrelease;
        // echo $request->date.'<br>';
        $mth=date('Y-m-01', strtotime($request->date));
        $cdate=date('Y-m-d',strtotime($request->date));
        $mthend=date('Y-m-t', strtotime($request->date));
        $pre_mth = Date("Y-m-01", strtotime($mth . " last month"));


        

        // $paidrelease = PaymentRelease::all();
        $monthamount=MonthBalance::where('date',$pre_mth)->where('employee_id', '=',$id)->first();
        $daysalary=PaymentAttendance::where('date',$cdate)->where('employee_id', '=',$id)->first();

        if($daysalary)
        {
        $payactual=$daysalary->payment_actual;

        }
        else
        {
            
            $payactual=0;
        }

        if($monthamount)
        {
        // $remainingsalary=$monthamount->payment_remaining;
        // $advancesalary=$monthamount->payment_advance;
        }
        $diffthem=$payactual-$released_salary;
        if($diffthem>0)
        {
            $remainingsalary=$diffthem;
            $advancesalary=0;
        }
        else
        {
            $remainingsalary=0;
            $advancesalary=$diffthem;
        }



        $paidreleasewithid=PaymentRelease::where('employee_id', '=',$id)->where('date', '<',$mthend)->where('date', '>=',$mth)->orderBy('date')->get();
        $currentmonth=MonthBalance::where('date',$mth)->where('employee_id', '=',$id)->first();
        if(!$currentmonth)
        {
            $currentmonth=new MonthBalance();
        }

    $bool=true;
        $totalrelease=0;
        $totalsalary=0;
foreach ($paidreleasewithid as $key => $value) {
            # code...

           // echo $value->date;
            if($value->date==$cdate)
            {
                $bool=false;

            }
        }

        if($bool)
        {
            $answer=0;
            $pay= new PaymentRelease();
            // $totalrelease=$released_salary+$totalrelease;
            // $answer=$actualsalary-$totalrelease+$remainingsalary-$advancesalary;
        // echo 'anser id' .$answer.'<br>';
                $pay->user_id = $request->userid;
                $pay->employee_id = $id;
                $pay->date = $cdate;
                $pay->payment_remaining=$remainingsalary;
                $pay->payment_advance=abs($advancesalary);
                $pay->payment = $payactual;
                $pay->payment_release=$released_salary;
                $pay->status = $request->status;
                $currentmonth->employee_id=$id;
                $currentmonth->date=$mth;
                $pay->save();
                $newentries=PaymentRelease::where('employee_id', '=',$id)->where('date', '<',$mthend)->where('date', '>=',$mth)->orderBy('date')->get();


                foreach ($newentries as $key => $value) {
                    # code...
                    $totalrelease=$value->payment_release+$totalrelease;
                    // $totalsalary=$value->payment+$totalsalary;
                }


                $currentmonth->Total_Salry=$actualsalary;
                $currentmonth->Total_release=$totalrelease;
                $remain=$actualsalary-$totalrelease;
                if($remain>0)
                {
                    $paymentmonthremian=$remain;
                    $paymentmonthadvance=0;
                }
                else
                {
                    $paymentmonthremian=0;
                    $paymentmonthadvance=$remain;   
                }

                $currentmonth->payment_remaining=$paymentmonthremian;
                $currentmonth->payment_advance=abs($paymentmonthadvance);
                $currentmonth->save();

                // $balance->save();
            return redirect('hr/salary/pay/all')->with('success', 'Imported successfully');
            
        }
        else
        {
            echo 'already present'.'<br>';
        }
        

        
       
    }

    public function allpaid(Request $request)
    {
        $start_month = date('Y-m-01');
        $end_month = date('Y-m-d', strtotime('last day of this month'));

        $month = date("Y-m");



         if ($request->month_year)
            {
                $start_month = new Carbon('first day of '.$request->month_year);
                $end_month = new Carbon('last day of '.$request->month_year);
                $month = $request->month_year;
            }
        
        $this->employees = Employee::all();
        foreach ($this->employees as $key => $employee) {
            $this->employees[$key]['bonus'] = 0;
            $this->employees[$key]['deduction'] = 0;
            $this->employees[$key]['total_overtime'] = 0;
            $this->employees[$key]['total_leaves'] = 0;
            $this->employees[$key]['full_leave'] = 0;
            $this->employees[$key]['half_leave'] = 0;
            $this->employees[$key]['total_attendance'] = 0;
            $this->employees[$key]['active_days'] = 0;
            $this->employees[$key]['public_holiday'] = 0;

           
           $weekday=$employee->working_days;
           $salary=$employee->salary;
           
           $strt_time=strtotime($employee->start_time);
           $endd_time=strtotime($employee->end_time);
           $workhour=($endd_time-$strt_time)/3600;

           $salaryperday=$salary/getActiveDays(unserialize($weekday), $month);

           $holidays=getholidays(unserialize($weekday),$month,$employee->id);
           
           $salaryperhour=$salaryperday/$workhour;

            $this->employees[$key]['active_days'] = getActiveDays(unserialize($weekday), $month);

            $attend = EmployeeAttendance::where('employee_id', $employee->id)->whereBetween('day', [$start_month, $end_month])->count('id');

            $attendcountpermonth = EmployeeAttendance::where('employee_id', $employee->id)->whereBetween('day', [$start_month, $end_month])->get();
            $monthbalance=MonthBalance::firstOrNew(['date'=>$start_month, 'employee_id'=>$employee->id]);
            $this->employees[$key]['total_attendance']=$attend;

            // $total_leaves = EmployeeLeaves::where('employee_id', $employee->id)->whereBetween('day', [$start_month, $end_month])->count('id');
            // $this->employees[$key]['total_leaves'] = $total_leaves;  
            $total_leaves=getLeaves(unserialize($weekday), $month,$employee->id);
            $this->employees[$key]['total_leaves']=$total_leaves;
            $this->employees[$key]['public_holiday']=$holidays;                                        
            
            $bonus = EmployeeBonus::where('employee_id', $employee->id)->where('bonus','>',0)->whereBetween('date', [$start_month, $end_month])->sum('bonus');

            $this->employees[$key]['bonus'] = $bonus;

            $deduction = EmployeeBonus::where('employee_id', $employee->id)->where('bonus','<',0)->whereBetween('date', [$start_month, $end_month])->sum('bonus');


            $totalworkhours=0;
            $normalworkhours=0;
            $totalovertime=0;

            foreach ($attendcountpermonth as $ke => $val) {

                # code...
            $hourworked=decimalHours($val->time_out) - decimalHours($val->time_in);
                if($workhour>$hourworked)
                {
                $normalworkhours=number_format($normalworkhours+$hourworked,2);
                    
                }
                else
                {
                    $overtimeis=$hourworked-$workhour;
                    $totalovertime=number_format($totalovertime+$overtimeis,2);
                    $normalworkhours=number_format($normalworkhours+$workhour,2);

                }
            }
            $totalholidays=$total_leaves+$holidays;
            $holidays_leaves_salary=round(getsalaryotherthanpresent($totalholidays,$salaryperday),2);
            $this->employees[$key]['deduction'] = $deduction;

            $totalpaiddays=$attend+$total_leaves+$holidays;

            $totalworkhours=$normalworkhours+$totalovertime;

            $this->employees[$key]['total_overtime']=$totalovertime;

            $this->employees[$key]['total'] = round($totalworkhours*$salaryperhour + $bonus + $deduction+$holidays_leaves_salary+$totalovertime*$salaryperhour,2);

            


                $monthbalance->Total_release=PaymentRelease::where('employee_id',$employee->id)->where('date', '<=',$end_month)->where('date', '>=',$start_month)->sum('payment_release');

             if(!$monthbalance->id)
             {
                $monthbalance->employee_id=$employee->id;
                


             }
            $monthbalance->date=$start_month;
            $monthbalance->Total_Salry=$this->employees[$key]['total'];
            $remain=$this->employees[$key]['total']-$monthbalance->Total_release;
            if($remain>0)
            {
                $monthbalance->payment_remaining=$remain;
                $monthbalance->payment_advance=0;
            }
            else
            {
             $monthbalance->payment_remaining=0;
                $monthbalance->payment_advance=$remain;   
            }
            $monthbalance->save();
             DB::commit();

             

        }
    

        $paidrelease = PaymentRelease::where('date', '
            <=',$end_month)->where('date', '>=',$start_month)->orderBy('date')->get();
        $paidreleasemonthly=MonthBalance::where('date', '=',$start_month)->get();
        return view('humanresource::released_salary.paidsalary',compact('paidrelease','allmonth','paidreleasemonthly','start_month','end_month'));
    }

    public function view_salary(Request $request,$id)
    {

        // $p_monthpresent=MonthBalance::where('employee_id','=',$id)->get();
        $start_month = new Carbon('first day of this month');
        $end_month = new Carbon('last day of this month');
        $end_of_month = new Carbon('last day of this month');
        $month=date('Y-m');
        if ($request->month_year)
            {
                $start_month = new Carbon('first day of '.$request->month_year);
                $end_month = new Carbon('last day of '.$request->month_year);
                $month = $request->month_year;
            }
            // echo $month;
        $daysinmonth=date('t',strtotime($request->month_year));
        $company=Setting::first();
        $year_x = $start_month->format('Y');
        $month_x = $start_month->format('m');
        $start_month = $start_month->format("Y-m-d");
        $end_month = $end_month->format("Y-m-d");


        $employee = Employee::whereId($id)->first();

        $total_days_in_week = unserialize($employee->working_days);

        $total_working_days = getActiveDays($total_days_in_week, $month);

        $leaves = getLeaves($total_days_in_week, $month, $id);
        $holidays = getholidays($total_days_in_week, $month, $id);

        // $total_working_days -= $leaves;
        $total_working_days -= $holidays;

        $rate_per_day = $employee->salary/$total_working_days;
        $total_days_worked = getPresentDays($total_days_in_week, $month, $id) + $leaves;
        $working_hours = calculateworkinghours($employee->start_time, $employee->end_time);
        $rate_per_hour = $rate_per_day/$working_hours;
        $total_working_hours = $working_hours*($total_working_days-$leaves);

        $days_absent = getAbsent($total_days_in_week, $month, $id);
        $attendance_hours = getWorkingHours($total_days_in_week, $month, $id);

        //Basic Salary
        $salary  = $employee->salary;
        //OverTime Calculation
        $overtime = $overtimeamount = 0;
        $time_lag = $time_lag_amount = 0;
        //Bonus / Reward
        $total_bonus = EmployeeBonus::where('date','>=',$start_month)->where('date','<=',$end_month)->where('employee_id',$id)->where('bonus','>',0)->sum('bonus');

        $absent = $days_absent;
        $absent_amount = $days_absent * $rate_per_day;
        //Deduction
        $total_deduction = abs(EmployeeBonus::where('date','>=',$start_month)->where('date','<=',$end_month)->where('employee_id',$id)->where('bonus','<',0)->sum('bonus'));

        if (!$employee->billing_type)
        {
            $employee->billing_type = $company->hr_billing_type;
        }
        
        if ($employee->billing_type == "per_hour")
        {
            //Time Lag Amount
            //Basicall on all working days, less amount of task done
            //like working on days for 8 hours
            //total work for 4 days = 32 hours
            //total worked hours = 31hours
            //time lagged = 32 - 31 => 1 hour
            $time_lag = ($total_days_worked * $working_hours) - $attendance_hours;
            if ($time_lag > 0)
            {
                $time_lag = round($time_lag, 2);
                $time_lag_amount = $time_lag * $rate_per_hour;
            }else{
                $overtime = abs($time_lag);
                $overtimeamount = $overtime * $rate_per_hour;
                $time_lag = 0;
            }
        }else{
            //otherwise it's per day
            if ($total_days_worked > $total_working_days)
            {
                $overtime = $total_days_worked - $total_working_days;;
                $overtimeamount = $overtime * $rate_per_day;   
            }
            // $salary - $absent days
        }

        if ($employee->billing_type == "no_deduction")
        {
            $absent = 0;
            $absent_amount = 0;
        
        }
        
        //Overtime is calculated from time lag
        

        //Absent

        


        

        $total_days_worked -= $leaves; //remove leave count for salary slip display


        $total_computed_salary = round($salary + $overtimeamount + $total_bonus - ($absent_amount + $time_lag_amount + $total_deduction),0);
       $f = new \NumberFormatter( locale_get_default(), \NumberFormatter::SPELLOUT );
       $salary_in_words = $f->format($total_computed_salary); 

        return view('humanresource::singlesalary',compact('employee','start_month','end_month','salary','company','overtime','overtimeamount','total_bonus','absent','absent_amount','time_lag','time_lag_amount','total_deduction','total_computed_salary','salary_in_words','total_days_worked','leaves','holidays','attendance_hours','id'));
    }

    // Calculate attendance by number of holidays and overtime
    public function attendance_summary(DownloadAttendanceListRequest $request)
    {
        $start_month = date('Y-m-d', strtotime($request->from));
        $end_month = date('Y-m-d', strtotime($request->to));
        $this->employees = Employee::all();
        foreach ($this->employees as $key => $employee) 
        {
            $this->employees[$key]['total_attendance'] = 0;
            $this->employees[$key]['total_overtime'] = 0;
            $this->employees[$key]['total_leaves'] = 0;
            $this->employees[$key]['full_leave'] = 0;
            $this->employees[$key]['half_leave'] = 0;
            $attendance = DB::table('employee')
                        ->select(DB::raw('count(*) as total_attendances'),
                            DB::raw('SUM(employee_attendances.overtime) as total_overtime'))
                        ->join('employee_attendances', 'employee.id', '=', 'employee_attendances.employee_id')
                        ->where('employee_id', $employee->id)
                        ->whereBetween('employee_attendances.day', array($start_month, $end_month))
                        ->get();
            if ($attendance) 
            {
                if ($attendance[0]->total_attendances) 
                {
                    $this->employees[$key]['total_attendance'] = $attendance[0]->total_attendances;
                }
                if (isset($attendance[0]->total_overtime) && $attendance[0]->total_overtime > 0) 
                {
                    $this->employees[$key]['total_overtime'] = date('H:i:s', $attendance[0]->total_overtime);
                }
            }

            $leave = EmployeeLeaves::select(DB::raw('count(*) as full_leave'))
                                ->where('employee_id', '=', $employee->id)
                                ->where('type', 'Full')
                                ->first();
            $full_leave = 0;            
            if ($leave) 
            {
                $full_leave = $leave->full_leave;
                $this->employees[$key]['full_leave'] = $leave->full_leave;
            }

            $leave = EmployeeLeaves::select(DB::raw('count(*) as half_leave'))
                                ->where('employee_id', '=', $employee->id)
                                ->where('type', 'Half')
                                ->first();
            $half_leave = 0;
            if ($leave) 
            {
                $half_leave = $leave->half_leave;
                $this->employees[$key]['half_leave'] = $leave->half_leave;
            }

            $total_leaves = $full_leave + (($half_leave)/2);
            if ($total_leaves > 0) 
            {
                $this->employees[$key]['total_leaves'] = $total_leaves;                                            
            }
        }

        Excel::create('Attendance Summary', function($excel) {
            $excel->sheet('New sheet', function($sheet) {
                $sheet->loadView('humanresource::sheets.attendance_summary', ['employees'=>$this->employees]);
            });

        })->export('xlsx');
    }
    public function bonus_list()
    {
        $date = date('Y-m-01');
        
        $this->employees = DB::table('employee')
            ->select('employee.id', 'employee.name', 'employee.position', 'employee.type', 
                DB::raw('SUM(employee_salary_logs.bonus) as bonus_total'), 
                DB::raw('count(employee_salary_logs.bonus) as bonus_count')
                )
            ->join('employee_salary_logs', 'employee.id', '=', 'employee_salary_logs.employee_id')
            ->whereDate('employee_salary_logs.created_at', ' >= ', $date)
            ->groupBy('employee.id')
            ->get();
        
        Excel::create('Bonus List', function($excel) {
            $excel->sheet('New sheet', function($sheet) {
                $sheet->loadView('humanresource::sheets.bonuses', ['employees'=>$this->employees]);
            });

        })->export('xlsx');   
    }
    public function salary_list(Request $request)
    {
        $start_month = date('Y-m-01');
        $end_month = date('Y-m-d', strtotime('last day of this month'));
        $month = date("Y-m");
        $totalamount=0;
        if ($request->month_year)
            {
                $start_month = new Carbon('first day of '.$request->month_year);
                $end_month = new Carbon('last day of '.$request->month_year);
                $month = $request->month_year;
            }

        
        $this->employees = Employee::all();
        foreach ($this->employees as $key => $employee) {
            $this->employees[$key]['bonus'] = 0;
            $this->employees[$key]['deduction'] = 0;
            $this->employees[$key]['total_overtime'] = 0;
            $this->employees[$key]['total_leaves'] = 0;
            $this->employees[$key]['full_leave'] = 0;
            $this->employees[$key]['half_leave'] = 0;
            $this->employees[$key]['total_attendance'] = 0;
            $this->employees[$key]['active_days'] = 0;
            $this->employees[$key]['public_holiday'] = 0;
            $this->employees[$key]['total']=0;
           
           $weekday=$employee->working_days;
           $salary=$employee->salary;
           
           $strt_time=strtotime($employee->start_time);
           $endd_time=strtotime($employee->end_time);
           $workhour=($endd_time-$strt_time)/3600;

           $salaryperday=$salary/getActiveDays(unserialize($weekday), $month);




           $holidays=getholidays(unserialize($weekday),$month,$employee->id);
           
            

           $salaryperhour=$salaryperday/$workhour;

            $this->employees[$key]['active_days'] = getActiveDays(unserialize($weekday), $month);

            $attend = EmployeeAttendance::where('employee_id', $employee->id)->whereBetween('day', [$start_month, $end_month])->count('id');

            $attendcountpermonth = EmployeeAttendance::where('employee_id', $employee->id)->whereBetween('day', [$start_month, $end_month])->get();

             

            $this->employees[$key]['total_attendance']=$attend;

            $total_leaves = EmployeeLeaves::where('employee_id', $employee->id)->whereBetween('day', [$start_month, $end_month])->count('id');
            $this->employees[$key]['total_leaves'] = $total_leaves;                                            
            
            $bonus = EmployeeBonus::where('employee_id', $employee->id)->where('bonus','>',0)->whereBetween('date', [$start_month, $end_month])->sum('bonus');

            $this->employees[$key]['bonus'] = $bonus;

            $deduction = EmployeeBonus::where('employee_id', $employee->id)->where('bonus','<',0)->whereBetween('date', [$start_month, $end_month])->sum('bonus');


            $totalworkhours=0;
            $normalworkhours=0;
            $totalovertime=0;

            foreach ($attendcountpermonth as $ke => $val) {

                # code...
            $hourworked=decimalHours($val->time_out) - decimalHours($val->time_in);
                if($workhour>$hourworked)
                {
                $normalworkhours=number_format($normalworkhours+$hourworked,2);
                    
                }
                else
                {
                    $overtimeis=$hourworked-$workhour;
                    $totalovertime=number_format($totalovertime+$overtimeis,2);
                    $normalworkhours=number_format($normalworkhours+$workhour,2);

                }
            }
            $this->employees[$key]['deduction'] = $deduction;

            $totalpaiddays=$attend+$total_leaves+$holidays;

            $totalworkhours=$normalworkhours+$totalovertime;

            $this->employees[$key]['total_overtime']=$totalovertime;

            $this->employees[$key]['total'] = round($totalworkhours*$salaryperhour + $bonus + $deduction,2);
            $totalamount=$totalamount+$this->employees[$key]['total'];
        }

        $newdate=date('F, Y', strtotime($month));
        $this->month = $newdate;
        $this->totalamount=$totalamount;
        

        Excel::create('Salary List '.$newdate, function($excel) {

            $excel->sheet('New sheet', function($sheet) {

                $sheet->loadView('humanresource::sheets.salaries', ['employees'=>$this->employees,'month'=>$this->month,'totalamount'=>$this->totalamount]);

            });

        })->export('xlsx');
    }
    public function deletebonus($id)
    {
        EmployeeBonus::whereId($id)->delete();
        return response()->json(['success']);
    }
    public function delete_released($id)
    {
         DB::beginTransaction();
        try
        {

        PaymentRelease::whereId($id)->delete();
        
        
        DB::commit();
        return response()->json(['done']);

        }
        catch (\Exception $e)
            {
            DB::rollback();
            return response($e->getMessage()."Line:".$e->getLine(),500);
            }
    }
    public function view_edit($id)
    {
        $payinfo=PaymentRelease::where('id', '=', $id)->first();
        return view('humanresource::released_salary.view_edit',compact('payinfo'));
    }

    public function view_bonus(Request $request, $id)
    {
        if ($id) {
            $employee = Employee::where('id', '=', $id)->first();
        }

        $start_month = new Carbon('first day of this month');
            $end_month = new Carbon('last day of this month');

            if ($request->month_year)
            {
                $start_month = new Carbon('first day of '.$request->month_year);
                $end_month = new Carbon('last day of '.$request->month_year);
            }

        if (!$employee) {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'No Employee selected for bonus!');

            return redirect('/hr/');
        } 

        $employeebonus = EmployeeBonus::where('employee_id', $employee->id)
                                ->where('date', '>=', $start_month->format('Y-m-d'))
                                ->where('date', '<=',$end_month->format('Y-m-d'))
                                ->get();
        
        return view('humanresource::bonus', compact('employee', 'employeebonus', 'start_month','end_month'));
    }
    public function view_bonus_list(Request $request)
    {
            $employs = Employee::all();
            $start_month = new Carbon('first day of this month');
            $end_month = new Carbon('last day of this month');

            if ($request->month_year)
            {
                $start_month = new Carbon('first day of '.$request->month_year);
                $end_month = new Carbon('last day of '.$request->month_year);
            }
            // echo $end_month;
            // exit;

            // SELECT employee_id, SUM(bonus) as sb FROM `employee_bonuses` GROUP By employee_id
            $employee_bonus=EmployeeBonus::where('date', '<=',$end_month->format('Y-m-d'))->where('date', '>=',$start_month->format('Y-m-d'))->groupBy('employee_id')->select(DB::raw('employee_id, sum(bonus) as sb'))->get();


            $employee = Employee::whereNotIn('id',$employee_bonus->pluck('employee_id')->toArray())->get();
            // $employee_bonus= EmployeeBonus::all();

        return view('humanresource::bonus_list', compact('employee', 'employee_bonus', 'start_month', 'end_month','employs'));
    }

    public function publicholiday()
    {
        $holiday=Holiday::all();
        return view('humanresource::public_holidays',compact('holiday'));
    }

    public function update_bonus(Request $request)
    {
        if (!is_allowed('access-hr')) {
            return redirect('/');
        }


        // \Log::info(print_r($request->all(), true));
        



        // $bonus = EmployeeBonus::where('employe_id', '=', $request->id)->first();

        foreach($request->bonus_id as $key => $emp)
        {
            if($request->bonus_id[$key]==0)
            {
                $employee = new EmployeeBonus();
                $employee->employee_id  = $request->id;
                $bonus = abs($request->bonus[$key]);
                if($request->type_of_bonus[$key]=='deduction')
                    {
                //agar bonus milla tu wo positive hoga
                //agar deduction to negative hai
                // mera bonus wala column hai wo positive/negative lay sakta hai
                // soo I concluded keh main issi technique ko use karon ga takkay table main dalna na paray
                $bonus *= -1;
                //ab mujhay aik aur issue samjh aaya, agar user ne negative value bhji aur type bonus kardi tu
                // iss liye main ABS ka function use kardon ga
                    }

                $employee->bonus = $bonus;
                $employee->date = $request->bonus_date[$key];
                $employee->description = $request->bonus_reason[$key];
                $employee->save();
            }
            else
            {

                $employee = EmployeeBonus::whereId($emp)->first(); 
                $employee->employee_id  = $request->id;
                $employee->bonus = $request->bonus[$key];
                $employee->date = $request->bonus_date[$key];
                $employee->description = $request->bonus_reason[$key];
                $employee->save();


            }
            



        }


        // if (! empty($employeeSalaryLog->id)) {
        //     $request->session()->flash('message.level', 'success');
        //     $request->session()->flash('message.content', 'Employee bonus added successfully!');
        // } else {
        //     $request->session()->flash('message.level', 'success');
        //     $request->session()->flash('message.content', 'Unable to add bonus.Please try later.');
        // }

        return redirect('/hr/bonuslist');
    }

    public function add_bonus(Request $request)
    {
        // if (empty(is_admin()) || !is_admin()) {
        //     return redirect('/');
        // }

        

       // / $employee = EmployeeBonus::where('employee_id', '=', $request->id)->first();

        $employee = new EmployeeBonus();

        $employee->employee_id  = $request->input('employee_name');
        $employee->bonus = $request->input('bonus');
        $employee->date = $request->input('month');
        $employee->description = $request->input('reason_bonus');
       
        
        $employee->save();

        if (! empty($Employee->id)) {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Employee bonus added successfully!');
        } else {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Unable to add bonus.Please try later.');
        }

        return redirect('/hr/bonuslist');
    }

    public function download_employee_list() 
    {
        $employees = Employee::groupBy('type')->get();
        return view('humanresource::download_employee', compact('employees'));
    }

    public function employee_list(Request $request)
    {
        if (!is_allowed('access-hr')) {
            return redirect('/');
        }

        if ($request->type == 'all') {
            $this->employees = Employee::all();
        }
        else {
            $this->employees = Employee::where('type', '=', $request->type)->get();
        }

        Excel::create('Employee List', function($excel) {

            $excel->sheet('New sheet', function($sheet) {

                $sheet->loadView('humanresource::employees', ['employees'=>$this->employees]);

            });

        })->export('xlsx');
    }

    public function dataTable()
    {
        
        $query = Employee::all();
        foreach ($query as $key => $employee) {
            $query[$key]['check_in'] = 'false';
            $query[$key]['check_out'] = 'false';

            $attendance = EmployeeAttendance::where('employee_id', $employee->id)
                            ->whereDate('day', date('Y-m-d'))
                            ->first();
            
            if ($attendance) {
                if (!empty($attendance->time_in)) {
                    $query[$key]['check_in'] = 'true';
                }
                if (!empty($attendance->time_out)) {
                    $query[$key]['check_out'] = 'true';
                }
            }
        }

        // <a href="'.url('/hr/check_in/' . $users->id).'" class="m-portlet__nav-link btn m-btn m-btn--hover-primary m-btn--icon m-btn--icon-only m-btn--pill" title="Check In"><i class="la la-sign-in"></i></a>
        // <a href="'.url('/hr/check_out/' . $users->id).'" class="m-portlet__nav-link btn m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill" title="Check Out"><i class="la la-sign-out"></i></a>
        
        return DataTables::of($query)
        ->addColumn('action', function (Employee $users) {
                $status = '';
                if (is_allowed('access-hr')) {
                    if ($users['check_in'] == 'true' && $users['check_out'] == 'false') {
                        $status = '<a href="'.url('/hr/check_out/' . $users->id).'" class="btn btn-outline-warning m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill" title="Check Out"><i class="la la-sign-out"></i></a>';
                        // <td> <a href="{{ url('/hr/check_out/' . $employee->id) }}">Check Out</a> </td>
                    }
                    elseif ($users['check_in'] == 'false' && $users['check_out'] == 'false') {
                        $status = '<a href="'.url('/hr/check_in/' . $users->id).'" class="btn btn-outline-info m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill" title="Check In"><i class="la la-sign-in"></i></a>';
                        // <td> <a href="{{ url('/hr/check_in/' . $employee->id) }}">Check In</a> </td>
                    }
                    elseif ($users['check_out'] == 'true') {
                        // <td> <a href="">Change Time</a> </td>
                    }
                }

                return '
                    <a href="'.url('/hr/bonus/'.$users->id).'" data-id="'.$users->id.'" class="btn btn-outline-accent m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill" title="Add Bonus"><i class="la la-bank"></i></a>
                    <a href="'.url('/hr/show/'.$users->id).'" data-id="'.$users->id.'" class="btn btn-outline-metal m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill" title="Show"><i class="la la-eye"></i></a>
                    <a href="'.url('/hr/edit/'.$users->id).'" data-id="'.$users->id.'" class="btn btn-outline-success m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill" title="Edit"><i class="la la-edit"></i></a>
                    <a href="#" data-id="'.$users->id.'" class="btn btn-outline-danger m-btn m-btn--icon btn-sm m-btn--icon-only m-btn--pill remove-item" title="Delete"><i class="la la-trash"></i></a>
                    '.$status;
               
            })->make(true);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!is_allowed('access-hr')) {
            return redirect('/');
        }

         $employee = Employee::all();

         // $s = md5(date('D-M-Y').'nouman'.'2'); generate reset token unique

        return view('humanresource::index',compact('employee'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        if (!is_allowed('access-hr')) {
            return redirect('/');
        }
        
        return view('humanresource::create_view');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $picture = $cnic_front = $cnic_back = null;
        try{
            if ($request->file('profile_pic'))
            {
                // $picture = $request->file('profile_pic')->store('public/profile');
                $picture = "storage/".str_replace('public/', "", $request->file('profile_pic')->store('public/profile'));
            }
            if ($request->file('cnic_front'))
            {
                $cnic_front = $request->file('cnic_front')->store('public/');
            }

            if ($request->file('cnic_back'))
            {
                $cnic_back = $request->file('cnic_back')->store('public/');
            }
            $employee = new Employee();
            $employee->name     = $request->input('name');
            $employee->father_name = $request->input('father_name');
            $employee->cnic = $request->input('cnic');
            $employee->address = $request->input('address');
            $employee->phone     = $request->input('phone');
            $employee->position = $request->input('position');
            $employee->date_of_joining=$request->input('date_of_joining');
            $employee->type      = $request->type;
            $employee->salary    = $request->salary;
            $employee->description    = $request->description."";
            $employee->working_days = serialize($request->w_days);
            $employee->billing_type = $request->billing_type;
            $employee->start_time = date("H:i", strtotime($request->start_time));
            $employee->end_time = date("H:i", strtotime($request->end_time));
            $employee->picture  = $picture;
            $employee->cnic_front  = $cnic_front;
            $employee->cnic_back  = $cnic_back;
            $employee->status   = 'active';
            $employee->save();

            // $employeeSalaryLog = new EmployeeSalaryLog();
            // $employeeSalaryLog->employee_id = $employee->id;
            // $employeeSalaryLog->salary = $employee->salary;
            // $employeeSalaryLog->bonus = 0;
            // $employeeSalaryLog->tax = 0;
            // $employeeSalaryLog->total = $employee->salary;
            // $employeeSalaryLog->save();

        }catch(Exception $e)
        {
            return response([$e->getMessage()],500);
        }
        // return redirect('/hr');
        // return response(['success'],200);
        return response()->json(['message' => 'bank account is successfully deleted','action'=>'redirect','do'=>url('/hr')], 200);

    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id, Request $request)
    {
        if ($id) {
            $employee = Employee::where('id', '=', $id)->first();

        }
        
        $attendance = EmployeeAttendance::where('employee_id',$id)->get();
        foreach($attendance as $atten){
            if($atten->time_out != '00:00:00'){
                $atten->check_out = date("g:i A",strtotime($atten->day.' '.$atten->time_out));
            }
        }  
        $atten=EmployeeAttendance::where('employee_id',$id)->select(DB::raw("count('employe_id') as present,DATE_FORMAT(day, '%b %y') as dy,month(day) as month_number"))->orderBy('dy','DESC')->groupBy('dy')->get();
        $leav=EmployeeLeaves::where('employee_id',$id)->select(DB::raw("count('employe_id') as present,DATE_FORMAT(day, '%b %y') as dy,month(day) as month_number"))->orderBy('dy','DESC')->groupBy('dy')->get();
        

       $employeget=Employee::where('id',$id)->first();
       $week=unserialize($employeget->working_days);

       if(!$week || !is_array($week))
       {
        $week = [];
       }
       //12N
        $Finalarrayattend=[];
       for($monty=1;$monty<=12;$monty++)
       {

        foreach ($atten as $key => $value) { //all attendance count
            # code...
            if($monty==$value->month_number)
            {
                $Finalarrayattend[$monty] = $value->present;
                // break;

            }
            else if (!array_key_exists($monty, $Finalarrayattend))
            {
                $Finalarrayattend[$monty] =  0;
                // break;
            }

        }


       }
       $Finalarrayleave=[];

       for($montu=1;$montu<=12;$montu++)
       {

        foreach ($leav as $key => $value) { //all attendance count
            # code...
            if($montu==$value->month_number)
            {
                $Finalarrayleave[$montu] = $value->present;
                // break;

            }
            else if (!array_key_exists($montu, $Finalarrayleave))
            {
                $Finalarrayleave[$montu] =  0;
                // break;
            }

        }


       }

         $weekday= $employeget->working_days;

         $holiday=Holiday::pluck('from','to');

         $month_array=array();
         $month_holiday=array();
         $holidayfinal=array();

         foreach ($holiday as $key => $value) {
             # code...
            
            $keyitm = strtotime($key);
            $valueitm=strtotime($value);

            $newkey = date('Y-m',$keyitm);
            $newvalue = date('Y-m',$valueitm);

            $newkeymonth=date('m',$keyitm);
            $newvaluemonth=date('m',$valueitm);

            if(!in_array($newkeymonth, $month_holiday))
            {
                array_push($month_holiday, $newkeymonth);
            }
            if(!in_array($newvaluemonth, $month_holiday))
            {
                array_push($month_holiday, $newvaluemonth);
            }



            if(!in_array($newkey, $month_array))
            {
                array_push($month_array,$newkey);

            }
            if(!in_array($newvalue, $month_array))
            {
                array_push($month_array, $newvalue);
            }

         }

         sort($month_array);

         for($mont=1;$mont<=12;$mont++)
         {
           $holidays=getholidays(unserialize($weekday),date('Y')."-".$mont,$id);

           array_push($holidayfinal, $holidays); 

         }
         sort($month_holiday);
           
        $leaves=EmployeeLeaves::where('employee_id',$id)->get();
        $holiday=Holiday::all();

        if (!$employee) {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'No Employee to show!');

            return redirect('/hr/');
        } 
        // print_r($employee);die;
        return view('humanresource::show', compact('employee','attendance','leaves','holiday','atten','finalvalu','month_name','leav','holidayfinal','month_attend','month_leave','month_holiday','Finalarrayattend','Finalarrayleave','week'));
    }



   
    public function edit($id)
    {
        if ($id) {
            $employee = Employee::where('id', '=', $id)->first();
        }
        else {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Invalid employee Id!');

            return redirect('/hr/');
        }

        return view('humanresource::edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(EditEmployeeRequest $request) //EditEmployeeRequest 
    {
        
        // if($request->ajax()){
        //     dd('ajax');
        // }
        // else{
        //     dd('normal');
        // }
        // return redirect('/');
        $employeeall=Employee::all();
        try{
        $employee = Employee::where('id', '=', $request->id)->first();

        if (!$employee) {
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Invalid employee!');
        }
        else {
            $picture = $cnic_front = $cnic_back = null;

            if ($request->file('profile_pic'))
            {
                // $picture = $request->file('profile_pic')->store('public/profile');
                $picture = "storage/".str_replace('public/', "", $request->file('profile_pic')->store('public/profile'));
                $employee->picture  = $picture;
            }
            $flag=1;
            foreach ($employeeall as $key => $value) {
                # code...

                 if($value->cnic==$request->cnic)
                 {
                    $flag=0;

                 }
            }
            
            
            // if ($request->file('cnic_front'))
            //     $cnic_front = $request->file('cnic_front')->store('employee');

            // if ($request->file('cnic_back'))
            //     $cnic_back = $request->file('cnic_back')->store('employee');

            
            $employee->name     = $request->input('name');
            $employee->father_name = $request->input('father_name');
            if($flag)
            {
            $employee->cnic = $request->input('cnic');    
            }
            $employee->address = $request->input('address');
            $employee->phone     = $request->input('phone');
            $employee->position = $request->input('position');
            $employee->type = $request->input('type');

            if ($employee->salary != $request->input('salary')) {
                $employee->salary    = $request->input('salary');
                
                $employeeSalaryLog = new EmployeeSalaryLog();
                $employeeSalaryLog->employee_id = $employee->id;
                $employeeSalaryLog->salary = $employee->salary;
                $employeeSalaryLog->bonus = 0;
                $employeeSalaryLog->tax = 0;
                $employeeSalaryLog->total = $employee->salary;
                $employeeSalaryLog->save();
            }
            
            $employee->description    = $request->input('description');
            $employee->date_of_joining    = $request->input('date_of_joining');
            $employee->cnic_front  = $cnic_front;
            $employee->cnic_back  = $cnic_back;
            $employee->working_days = serialize($request->w_days);
            $employee->start_time = date("H:i", strtotime($request->start_time));
            $employee->end_time = date("H:i", strtotime($request->end_time));
            $employee->status   = 'active';
            $employee->billing_type = $request->billing_type;
            $employee->save();

            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Employee updated successfully!');
        }

        }catch(Exception $e)
        {
            return response([$e->getMessage()],500);
        }
        // return response(['success']);
        return redirect()->route('hr.index')->with('message', 'Employee has been updated successfully');
        // return response()->json(['success'=>'Employee has been updated successfully.','do' =>'redirect', 'url'=>route('hr.index')]);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        Employee::whereId($id)->delete();
        return response()->json(['done']);
    }




    /**
     * Export Excel file .
     * @return Response
     */

    public function downloadExcel()
    {
        $data = Employee::get()->toArray();
        return Excel::create('HumanResource', function ($excel) use ($data){
            $excel->sheet('mysheet', function($sheet) use ($data){
                $sheet->fromArray($data);
            });

        })->download();
    }
    

    /**
     * Import Excel File.
     * @return Response
     */

    public function importExcel()
    {
        if (input::hasFile('import_file')) {
            
            $path = Input::file('import_file')->getRealPath();

            $data = Excel::load($path, function($reader){

            })->get();
            if (!empty($data) && $data->count()) {
                
                foreach ($data as $key => $value) {

                    $insert[]= ['name' => $value->name,
                                'father_name' => $value->father_name,
                                'picture' => $value->picture,
                                'cnic' => $value->cnic,
                                'cnic_front' => $value->cnic_back,
                                'address' => $value->address,
                                'phone' => $value->phone,
                                'position' => $value->position,
                                'status' => $value->status,
                                'type' => $value->type,
                                'salary' => $value->salary,
                                'date_of_joining' => $value->date_of_joining,
                                'date_of_leaving' => $value->date_of_leaving,
                               ];
                }

                if (!empty($insert)) {

                   $data =  Employee::insert($insert);
                   
                   return redirect('hr')->with('success', 'Imported successfully');
                }   

            }
            else{
                return redirect('hr')->with('error', 'Your File is Empty');
            }
        }
    }


    public function addholiday(Request $request)
    {

        $holiday = new Holiday();

         $holiday->from     = $request->input('from');
         $holiday->to = $request->input('to');
         $holiday->occassion = $request->input('occassion');
         $holiday->save();


           if ($request->ajax())
            {
                return response(['message'=>'success','data'=>$holiday], 200);
            }
         return redirect('hr/pholidays')->with('success', 'Imported successfully');
    }

    public function deleteholiday($id)
    {
        Holiday::whereId($id)->delete();
        return response()->json(['done']);
    }
}
