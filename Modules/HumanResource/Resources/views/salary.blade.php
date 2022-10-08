@extends('layout')
@section('header')
@endsection
@section('content')

    <div class="page-header clearfix">
        <div class="align-items-center">
            <div class="mr-auto" style="display: flex; justify-content: space-between; align-items: center; ">
                <div>
                    <h1 class="m-subheader__title m-subheader__title--separator">
                    Human Resource
                    </h1>
                </div>
                
                <div style="    float: right;padding: 5px 0px 0px 0px;">
                    <input id="myInput" style="width: 277px; border: none;border-radius: 72px;padding: 5px 7px 5px 7px;" type="search" name="search" placeholder="Search">
                </div>
            </div>
            
        </div>
    </div>

    <div class="content-panel">
        <div class="m-portlet">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <!-- <span class="m-portlet__head-icon">
                            <i class="flaticon-users"></i>
                        </span> -->
                        <h3 class="m-portlet__head-text">
                            Salary Report
                        </h3>

                    </div>
                </div>
            </div>

            <div class="m-portlet__body">
            <form id="month_ch">
            <!-- <button class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--pill"><a style="color: white" href="{{ url('hr/salary_list')}}?month_year={{date('Y-m', strtotime($start_month))}}"> Export to excel</a></button> -->
            <center>
                       <input value="{{date('Y-m', strtotime($start_month))}}" type="month" name="month_year" onchange="$('#month_ch').submit()"><br>
            <small>
                {{date_to_str($start_month)}} to {{date_to_str($end_month)}}
            </small>
                
            </center>
            </form>
            <div class="table-responsive">
                <table id="exampledt" class="table table-condensed table-striped" style="width:100%;text-align: center">
                        <thead>
                            <tr>
                            <th>ID</th>
                            <th>Employee Name</th>
                            <th>Father Name</th>
                            <th>CNIC</th>
                            <th>Calculation</th>
                            <th>Salary</th>
                            <th>Present</th>
                            <th>Leaves</th>
                            <th>Absents</th>
                            <th>Bonus</th>
                            <th>Overtime (hrs)</th>
                            <th>Overtime Amount</th>
                            <th>Deduction</th>
                            <th>Total Salary</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                            $total = 0;
                            @endphp
                       @foreach ($employees as $employee)
                            <tr>
                                <td>{{$employee->id}}</td>
                                <td>{{$employee->name}}</td>
                                <td>{{$employee->father_name}}</td>
                                <td>{{$employee->cnic}}</td>
                                <td>{{$employee->billing_type}}</td>
                                <td>{{check_negative($employee->salary)}}</td>
                                <td>{{$employee->total_attendance}}</td>
                                <td>{{ $employee->total_leaves }}</td>
                                <td>{{$employee->absent}}</td>
                                <td>{{ check_negative($employee->bonus )}}</td>
                                <td>{{$employee->total_overtime}}</td>
                                <td>{{check_negative($employee->total_overtime_amount)}}</td>
                                <td>{{ $employee->deduction }}</td>
                                <td>{{ check_negative($employee->total) }}</td>
                            </tr>
                            @php
                            $total += $employee->total;
                            @endphp
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>{{count($employees)}} Records</td>
                                <td colspan="11"></td>
                                <td>Total</td>
                                <td>{{check_negative($total)}}</td>
                            </tr>
                        </tfoot>
                </table>
            </div>
            </div>
                
            
        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
$(document).ready(function() {
   // daterangepickerIn();
   <?php $messageTop = "Employee Salary List "; /*date_to_str($start_month)." to ".date_to_str($end_month)*/; ?>
    $('#exampledt').DataTable({
        dom: 'Blfrtip',
        "ordering": false,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        buttons: [<?=get_print_template("#exampledt", 'copy', 'Salary List', $messageTop)?>, <?=get_print_template("#exampledt", 'excel', 'Salary List', $messageTop)?>, <?=get_print_template("#exampledt", 'print', 'Salary List', $messageTop)?>]
    });
});
</script>
@endsection