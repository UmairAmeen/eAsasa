@extends('layout')
@section('header')
@endsection
@section('content')
    <div class="m-subheader ">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h3 class="m-subheader__title m-subheader__title--separator">
                    Human Resource
                </h3>
                <ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
                    <li class="m-nav__item m-nav__item--home">
                        <a href="#" class="m-nav__link m-nav__link--icon">
                            <i class="m-nav__link-icon la la-home"></i>
                        </a>
                    </li>
                    <li class="m-nav__separator">
                        -
                    </li>
                    <li class="m-nav__item">
                        <a href="{{ url('home') }}" class="m-nav__link">
                            <span class="m-nav__link-text">
                                Dashboard
                            </span>
                        </a>
                    </li>
                    <li class="m-nav__separator">
                        -
                    </li>
                    <li class="m-nav__item">
                        <a href="{{ url('hr') }}" class="m-nav__link">
                            <span class="m-nav__link-text">
                                Human Resource
                            </span>
                        </a>
                    </li>
                     <li class="m-nav__separator">
                        -
                    </li>
                    <li class="m-nav__item">
                        <a href="{{ url('hr/attendances') }}" class="m-nav__link">
                            <span class="m-nav__link-text">
                                Attendances
                            </span>
                        </a>
                    </li>
                    <li class="m-nav__separator">
                        -
                    </li>
                    <li class="m-nav__item">
                        <a href="#" class="m-nav__link">
                            <span class="m-nav__link-text">
                                Edit Attendance
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
            
        </div>
    </div>

    <div class="m-content">
        <div class="m-portlet">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <!-- <span class="m-portlet__head-icon">
                            <i class="flaticon-users"></i>
                        </span> -->
                        <h3 class="m-portlet__head-text">
                            Edit Attendance
                        </h3>
                    </div>
                </div>
            </div>
                <form class="form-horizontal" role="form" method="POST" action="{{url('/hr/update_attendance')}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="{{ $attendance->id }}">
                     <input class="form-control m-input" type="hidden" name="day" value="{{ $attendance->day }}" >
                    
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
                                <strong>Day</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
                                <input class="form-control m-input" type="date" name="day" value="{{ $attendance->day }}" disabled >
                            </div>
                        </div>

                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Check In</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
                                <input type="text" name="time_in" class="form-control m_datetimepicker" id="" value="{{ $attendance->day . ' ' . $attendance->time_in }}" placeholder="Select time">
                            </div>
                        </div>

                        
                        <div class="form-group m-form__group row">
                            <label class="col-lg-2 col-form-label">
                                <strong>Check Out</strong>
                            </label>
                            <div class="col-lg-6 col-form-label">
                                <input type="text" name="time_out" class="form-control m_datetimepicker" id="" value="{{ $attendance->day . ' ' . $attendance->time_out }}" placeholder="Select time">
                            </div>
                        </div>
                </div>
                <div class="m-portlet__foot">
                    <div class="row align-items-center">
                        <div class="col-lg-6 m--valign-middle">
                            <a href="{{url('hr/attendances')}}" class="m-link m--font-bold">
                                Back
                            </a>
                        </div>
                        <div class="col-lg-6 m--align-right">
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
@stop

@section('scripts')
<script src="{{asset('assets/js/bootstrap-timepicker.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script>
</script>

@endsection
