@extends('layout')
@section('css')
    <link href="{{asset('assets/js/bootstrap-datetimepicker/css/datetimepicker.css')}}" rel="stylesheet">
@endsection
@section('header')
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
                    <div class="m-portlet__head-title">
                        <!-- <span class="m-portlet__head-icon">
                                <i class="flaticon-users"></i>
                            </span> -->
                        <h3 class="m-portlet__head-text">
                            Add Attendance
                        </h3>
                    </div>
                </div>
            </div>
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/hr/all_attendance') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="m-portlet__body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ implode('', $errors->all(':message')) }}
                        </div>
                    @endif

                    @if (session()->has('message.level'))
                        <div class="alert alert-{{ session('message.level') }}">
                            {!! session('message.content') !!}
                        </div>
                    @endif

                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label">
                            <strong>Day</strong>
                        </label>
                        <div class="col-lg-6 col-form-label">
                            <input class="form-control m-input" id="m_datepicker_2" type="text" name="day"
                                value="{{ old('day') }}">
                            <!-- <input class="form-control m-input" type="date" name="day" value="{{ old('day') }}" > -->
                        </div>
                    </div>

                    <div class="form-group m-form__group row">
                        <label class="col-lg-4 col-form-label">
                            <strong>Employee</strong>
                        </label>
                        <label class="col-lg-2 col-form-label">
                            <strong>Attendance</strong>
                        </label>
                        <label class="col-lg-2 col-form-label">
                            <strong>Shift</strong>
                        </label>
                        <label class="col-lg-2 col-form-label">
                            <strong>Check In</strong>
                        </label>
                        <label class="col-lg-2 col-form-label">
                            <strong>Check Out</strong>
                        </label>
                    </div>

                    @foreach ($employees as $key => $employee)
                        <div class="form-group m-form__group row">
                            <div class="col-lg-4 col-form-label">
                                <input class="form-control m-input" type="hidden" name="employee[]"
                                    value="{{ $employee->id }}">
                                <strong>{{ $employee->name }}</strong>
                            </div>
                            <div class="col-lg-2 col-form-label">
                                <select name="attendance[]" class="form-control m-input">
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-form-label">
                                <select name="shift[]" class="form-control m-input">
                                    <option value="day">Day</option>
                                    <option value="night">Night</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-form-label">
                                <input type="text" name="time_in[]" value="{{ old('time_in.' . $key) }}" style="display: inline-flex;width: 100%"
                                    class="form-control m_datetimepicker" id="datetimepicker" readonly=""
                                    placeholder="Select time">
                            </div>
                            <div class="col-lg-2 col-form-label">
                                <input type="text" name="time_out[]" value="{{ old('time_out.' . $key) }}" style="display: inline-flex;width: 100%"
                                    class="form-control m_datetimepicker" id="datetimepicker_1" readonly=""
                                    placeholder="Select time">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="m-portlet__foot">
                    <div class="row align-items-center">
                        <div class="col-lg-6 m--valign-middle">
                            <a href="{{ url('hr/attendances') }}" class="m-link m--font-bold">
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
