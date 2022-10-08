@extends('layout')
@section('css')
    <link href="{{asset('assets/js/bootstrap-datetimepicker/css/datetimepicker.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="page-header clearfix">
        <div class="align-items-center">
            <div class="mr-auto" style="display: flex; justify-content: space-between; align-items: center; ">
                <div>
                    <h1 class="m-subheader__title m-subheader__title--separator">
                        Attendances
                    </h1>
                </div>
            </div>

        </div>
    </div>
    <div class="content-panel">
        <div class="m-portlet">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title" style="margin-bottom: 30px;">
                        <!-- <span class="m-portlet__head-icon">
                            <i class="flaticon-users"></i>
                        </span> -->
                        <h3 class="m-portlet__head-text">
                            Add Attendance
                        </h3>
                    </div>
                </div>
            </div>
            <form class="form-horizontal" role="form" method="POST" action="{{url('/hr/single_attendance')}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="m-portlet__body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ implode('', $errors->all(':message')) }}
                        </div>
                    @endif

                    @if(session()->has('message.level'))
                        <div class="alert alert-{{ session('message.level') }}">
                            {!! session('message.content') !!}
                        </div>
                    @endif

                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label">
                            <strong>Employee</strong>
                        </label>
                        <div class="col-lg-6 col-form-label">
                            <select name="employee" class="form-control m-input">
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label">
                            <strong>Date</strong>
                        </label>
                        <div class="col-lg-6 col-form-label">
                            <input class="form-control m-input" id="m_datepicker_2" type="text" name="day">
                        </div>
                    </div>

                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label">
                            <strong>Check In</strong>
                        </label>
                        <div class="col-lg-6 col-form-label">
                            <input type="text" name="time_in" class="form-control"
                                   style="display: inline-flex;width: 100%" id="datetimepicker" readonly=""
                                   placeholder="Check In">
                        </div>
                    </div>

                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label">
                            <strong>Check Out</strong>
                        </label>
                        <div class="col-lg-6 col-form-label">
                            <input type="text" name="time_out" class="form-control"
                                   style="display: inline-flex;width: 100%" id="datetimepicker_1" readonly=""
                                   placeholder="Check Out">
                        </div>
                    </div>


                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label">
                            <strong>Shift</strong>
                        </label>
                        <div class="col-lg-6 col-form-label">
                            <select class="form-control m-input" name="shift">
                                <option value="Full">Day</option>
                                <option value="Half">Night</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="m-portlet__foot">
                    <div class="row align-items-center">
                        <div class="col-lg-6 m--valign-middle">
                            <a href="{{url('hr/leaves')}}" class="m-link m--font-bold">
                                Back
                            </a>
                        </div>
                        <div class="col-lg-6 m--align-right" style="text-align: right;">
                            <span class="m--margin-left-10">
                                <button type="submit" class="btn btn-success">
                                    Submit
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('assets/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script>
    <script type="text/javascript">
        $('#m_datepicker_2').datepicker({
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        });
        $("#datetimepicker,#datetimepicker_1").datetimepicker({
            pickDate: false,
            minuteStep: 5,
            pickerPosition: 'bottom-right',
            format: 'yyyy-mm-dd hh:ii:ss',
            autoclose: true,
            showMeridian: true,
            startView: 1,
            maxView: 1,
        });
    </script>
@endsection
