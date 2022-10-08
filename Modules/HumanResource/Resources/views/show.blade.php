@extends('layout')
<!-- Latest compiled and minified JavaScript -->
@section('css')
    {{-- <!--begin::Base Styles -->
    <link rel="stylesheet" href="{{ asset('vendors/base/vendors.bundle.css') }}?v=1.2" />
    <link rel="stylesheet" type="text/css" href="{{URL::asset('vendors/base/main.css')}}?v=0.1">
    <link rel="stylesheet" href="{{ asset('base/style.bundle.css') }}?v=1.3.8" />
    <!--end::Base Styles --> --}}
    <link href="{{ asset('assets/js/bootstrap-datetimepicker/css/datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/bootstrap-timepicker/compiled/timepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('base/style.bundle.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.0/fullcalendar.min.css"
        integrity="sha512-LPrOACFtmEJvoUjBN9WGv1ztStvmzPXlY+tmyZKmDBlKTdDXBa3M0RPVRNfWNp2vP0TRUoxZrv2AzdgJVhS7uw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
    <div class="m-subheader ">
        <div class="align-items-center">
            <div class="mr-auto">
                <h3 class="m-subheader__title m-subheader__title--separator">
                    Human Resource
                </h3>
                <div style="    float: right;padding: 5px 0px 0px 0px;">
                    <input style="    width: 277px; border: none;border-radius: 72px;padding: 5px 7px 5px 7px;"
                        type="search" name="search" placeholder="Search">
                </div>
            </div>
        </div>
    </div>
    <br>
    <!-- END: Subheader -->
    <div class="content">
        <div class="row">
            <div class="col-lg-3">
                <div class="m-portlet m-portlet--full-height ">
                    <div class="m-portlet__body">
                        <div class="m-card-profile">
                            <div class="m-card-profile__title m--hide">
                                Your Profile
                            </div>
                            <div class="m-card-profile__pic">
                                <div class="m-card-profile__pic-wrapper">
                                    <img id="blah" src="{{ profilepic($employee->picture) }}"
                                        style="width: 130px;height: 130px;" alt=""
                                        onerror="this.onerror=null;this.src='{{ asset('assets/images/user.png') }}';" />
                                </div>
                            </div>
                            <div class="m-card-profile__details">
                                <span class="m-card-profile__name">
                                    {{ $employee->name }}
                                </span>
                            </div>
                            <div class="m-card-profile__details">
                                <a href="" class="m-card-profile__email m-link">
                                    {{ $employee->position }}
                                </a>
                            </div>
                        </div>
                        
                        <ul class="m-nav m-nav--hover-bg m-portlet-fit--sides">
                            <li class="m-nav__separator m-nav__separator--fit"></li>
                        </ul>

                        <div class="m-card-profile_details">
                            <span class="m-card-profile__name">
                                {{ $employee->description }}
                            </span>
                        </div>

                        {{-- <ul class="m-nav m-nav--hover-bg m-portlet-fit--sides">
                            <li class="m-nav__separator m-nav__separator--fit"></li>
                            <li class="m-nav__section m--hide">
                                <span class="m-nav__section-text">
                                    Section
                                </span>
                            </li>
                            <li class="m-nav__item">
                                <a href="../header/profile&amp;demo=default.html" class="m-nav__link">
                                    <i class="m-nav__link-icon flaticon-profile-1"></i>
                                    <span class="m-nav__link-title">
                                        <span class="m-nav__link-wrap">
                                            <span class="m-nav__link-text">
                                                My Profile
                                            </span>
                                            <span class="m-nav__link-badge">
                                                <span class="m-badge m-badge--success">
                                                    2
                                                </span>
                                            </span>
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="m-nav__item">
                                <a href="../header/profile&amp;demo=default.html" class="m-nav__link">
                                    <i class="m-nav__link-icon flaticon-share"></i>
                                    <span class="m-nav__link-text">
                                        Activity
                                    </span>
                                </a>
                            </li>
                            <li class="m-nav__item">
                                <a href="../header/profile&amp;demo=default.html" class="m-nav__link">
                                    <i class="m-nav__link-icon flaticon-chat-1"></i>
                                    <span class="m-nav__link-text">
                                        Messages
                                    </span>
                                </a>
                            </li>
                            <li class="m-nav__item">
                                <a href="../header/profile&amp;demo=default.html" class="m-nav__link">
                                    <i class="m-nav__link-icon flaticon-graphic-2"></i>
                                    <span class="m-nav__link-text">
                                        Sales
                                    </span>
                                </a>
                            </li>
                            <li class="m-nav__item">
                                <a href="../header/profile&amp;demo=default.html" class="m-nav__link">
                                    <i class="m-nav__link-icon flaticon-time-3"></i>
                                    <span class="m-nav__link-text">
                                        Events
                                    </span>
                                </a>
                            </li>
                            <li class="m-nav__item">
                                <a href="../header/profile&amp;demo=default.html" class="m-nav__link">
                                    <i class="m-nav__link-icon flaticon-lifebuoy"></i>
                                    <span class="m-nav__link-text">
                                        Support
                                    </span>
                                </a>
                            </li>
                        </ul> --}}
                        <div class="m-portlet__body-separator"></div>
                        {{-- <div class="m-widget1 m-widget1--paddingless">
                            <div class="m-widget1__item">
                                <div class="row m-row--no-padding align-items-center">
                                    <div class="col">
                                        <h3 class="m-widget1__title">
                                            Member Profit
                                        </h3>
                                        <span class="m-widget1__desc">
                                            Awerage Weekly Profit
                                        </span>
                                    </div>
                                    <div class="col m--align-right">
                                        <span class="m-widget1__number m--font-brand">
                                            +$17,800
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="m-widget1__item">
                                <div class="row m-row--no-padding align-items-center">
                                    <div class="col">
                                        <h3 class="m-widget1__title">
                                            Orders
                                        </h3>
                                        <span class="m-widget1__desc">
                                            Weekly Customer Orders
                                        </span>
                                    </div>
                                    <div class="col m--align-right">
                                        <span class="m-widget1__number m--font-danger">
                                            +1,800
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="m-widget1__item">
                                <div class="row m-row--no-padding align-items-center">
                                    <div class="col">
                                        <h3 class="m-widget1__title">
                                            Issue Reports
                                        </h3>
                                        <span class="m-widget1__desc">
                                            System bugs and issues
                                        </span>
                                    </div>
                                    <div class="col m--align-right">
                                        <span class="m-widget1__number m--font-success">
                                            -27,49%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div>
                    <div class="m-portlet m-portlet--full-height m-portlet--tabs ">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-tools">
                            <ul class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--left m-tabs-line--primary"
                                role="tablist">
                                <li class="nav-item m-tabs__item active">
                                    <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_user_profile_tab_1"
                                        role="tab">
                                        <i class="flaticon-share m--hide"></i>
                                        Update Profile
                                    </a>
                                </li>
                                <li class="nav-item m-tabs__item">
                                    <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_user_profile_tab_2"
                                        role="tab">
                                        Attendance
                                    </a>
                                </li>
                                <li class="nav-item m-tabs__item">
                                    <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_user_profile_tab_3"
                                        role="tab">
                                        Attendance Report
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content" style="background-color: white">
                        <div class="tab-pane active" id="m_user_profile_tab_1">
                            <form class="m-form m-form--fit m-form--label-align-right" style="margin-left: 5px" role="form" method="POST"
                                enctype="multipart/form-data" action="{{ url('/hr/update') }}">

                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="id" value="{{ $employee->id }}">
                                <div class="m-portlet__body">
                                    <div class="form-group m-form__group m--margin-top-10 m--hide">
                                        <div class="alert m-alert m-alert--default" role="alert">
                                            <!-- Alert -->
                                        </div>
                                    </div>

                                    <div class="form-group m-form__group">
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <div class="col-10 ml-auto">
                                            <h3 class="m-form__section">
                                                1. Employee's Details
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group">
                                        <label for="example-text-input" class="col-2 col-form-label">
                                            Full Name
                                        </label>
                                        <div class="col-7">
                                            <input class="form-control m-input" type="text" name="name"
                                                value="{{ $employee->name }}">
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group">
                                        <label for="example-text-input" class="col-2 col-form-label">
                                            Father Name
                                        </label>
                                        <div class="col-7">
                                            <input class="form-control m-input" type="text" name="father_name"
                                                value="{{ $employee->father_name }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group">
                                        <label for="example-text-input" class="col-2 col-form-label">
                                            CNIC
                                        </label>
                                        <div class="col-7">
                                            <input class="form-control m-input" type="tel" placeholder="XXXXX-XXXXXXX-X"
                                                data-mask="99999-9999999-9" {{-- pattern="^\d{5}-\d{7}-\d{1}$" --}} name="cnic"
                                                value="{{ $employee->cnic }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group m-form__group">
                                        <label for="example-text-input" class="col-2 col-form-label">
                                            Phone No.
                                        </label>
                                        <div class="col-7">
                                            <input class="form-control m-input" type="tel" {{-- data-mask="9999-9999999" --}}
                                                name="phone" pattern="^\d{11}$" value="{{ $employee->phone }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group m-form__group">
                                        <label for="example-text-input" class="col-2 col-form-label">
                                            Position
                                        </label>
                                        <div class="col-7">
                                            <input class="form-control m-input" type="text" name="position"
                                                value="{{ $employee->position }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group m-form__group">
                                        <label for="example-text-input" class="col-2 col-form-label">
                                            Profile Pic
                                        </label>
                                        <div class="col-7">
                                            <input type="file" name="profile_pic" id="imgInp" class="form-control m-input"
                                                placeholder="" value="{{ $employee->picture }}" accept="image/*">
                                        </div>
                                    </div>

                                    <div class="form-group m-form__group">
                                        <label for="example-text-input" class="col-2 col-form-label">
                                            Type
                                        </label>
                                        <div class="col-7">
                                            <input class="form-control m-input" name="type" type="text"
                                                value="{{ $employee->type }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group m-form__group">
                                        <label for="example-text-input" class="col-2 col-form-label">
                                            Salary
                                        </label>
                                        <div class="col-7">
                                            <input class="form-control m-input" name="salary" type="text"
                                                value="{{ $employee->salary }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group m-form__group">
                                        <label for="example-text-input" class="col-2 col-form-label">
                                            Date of joining
                                        </label>
                                        <div class="col-7">
                                            <div class="date">
                                                <input type="date" name="date_of_joining" class="form-control"
                                                    value="{{ $employee->date_of_joining }}"
                                                    placeholder="Select date" required/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar-check-o"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group m-form__group">

                                        <label class="form-control-label col-2 col-form-label">* Working days:</label>
                                        <div class="col-7">
                                            <select class="form-control m-bootstrap-select m_selectpicker" required name="w_days[]"
                                                multiple>

                                                <?php if (in_array('monday', $week)):?>
                                                <option selected value="monday">Monday</option>
                                                <?php else:?>
                                                <option value="monday">Monday</option>
                                                <?php endif;?>
                                                <?php if (in_array('tuesday', $week)):?>
                                                <option selected value="tuesday">Tuesday</option>

                                                <?php else:?>
                                                <option value="tuesday">Tuesday</option>
                                                <?php endif;?>
                                                <?php if (in_array('wednesday', $week)):?>
                                                <option selected value="wednesday">Wednesday</option>
                                                <?php else:?>
                                                <option value="wednesday">Wednesday</option>
                                                <?php endif;?>
                                                <?php if (in_array('thursday', $week)):?>
                                                <option selected value="thursday">Thursday</option>

                                                <?php else:?>
                                                <option value="thursday">Thursday</option>
                                                <?php endif;?>
                                                <?php if (in_array('friday', $week)):?>
                                                <option selected value="friday">Friday</option>

                                                <?php else:?>
                                                <option value="friday">Friday</option>
                                                <?php endif;?>
                                                <?php if (in_array('saturday', $week)):?>
                                                <option selected value="saturday">Saturday</option>

                                                <?php else:?>
                                                <option value="saturday">Saturday</option>
                                                <?php endif;?>

                                                <?php if (in_array('sunday', $week)):?>
                                                <option selected value="sunday">Sunday</option>

                                                <?php else:?>
                                                <option value="sunday">Sunday</option>
                                                <?php endif;?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group m-form__group">
                                        <div class="col-lg-5">
                                            <label class="form-control-label">* Start time:</label>
                                            <input class="form-control" name="start_time"
                                                value="{{ date('h:i A',strtotime($employee->start_time)) }}" onfocus="setStartTime(this)" id="m_timepicker_1"
                                                placeholder="Select time" type="text"  required/>
                                        </div>

                                        <div class="col-lg-5">
                                            <label class="form-control-label">* Ends time:</label>
                                            <input class="form-control" name="end_time"
                                                value="{{ date('h:i A',strtotime($employee->end_time)) }}" onfocus="setEndTime(this)" id="m_timepicker_2"
                                                placeholder="Select time" type="text" required/>
                                        </div>
                                    </div>
                                    <br><br><br>
                                    <br>
                                    <div class="form-group m-form__group ">
                                        <div class="col-2"></div>

                                        <label class="form-control-label col-10 col-form-label" for="billing_type">Billing
                                            Type:</label>
                                        <div class="col-7">
                                            <select class="form-control m-bootstrap-select m_selectpicker"
                                                name="billing_type" required>
                                                <option value="per_day"
                                                    {{ is_selected($employee->billing_type, 'per_day') }}>
                                                    Per Day
                                                </option>
                                                <option value="per_hour"
                                                    {{ is_selected($employee->billing_type, 'per_hour') }}>
                                                    Per Hour
                                                </option>
                                                <option value="no_deduction"
                                                    {{ is_selected($employee->billing_type, 'no_deduction') }}>
                                                    No Time/Day Based Deduction
                                                </option>
                                            </select>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>


                                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x">
                                    </div>
                                    <div class="form-group m-form__group">
                                        <div class="col-10 ml-auto">
                                            <h3 class="m-form__section">
                                                2. Address
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group">
                                        <label for="example-text-input" class="col-2 col-form-label">
                                            Address
                                        </label>
                                        <div class="col-7">
                                            <input class="form-control m-input" name="address" type="text"
                                                value="{{ $employee->address }}">
                                        </div>
                                    </div>
                                    <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x">
                                    </div>
                                    <div class="form-group m-form__group">
                                        <div class="col-10 ml-auto">
                                            <h3 class="m-form__section">
                                                2. Point to Note
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group">
                                        <label for="example-text-input" class="col-2 col-form-label">
                                            Description
                                        </label>
                                        <div class="col-7">
                                            <textarea placeholder="Comment If any" name="description"
                                                style="width: 100%">{{ $employee->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="m-portlet__foot m-portlet__foot--fit">
                                    <div>
                                        <div class="row m-form__actions">
                                            <div class="col-2"></div>
                                            <div class="col-7">
                                                <button type="submit"
                                                    class="btn btn-info m-btn m-btn--air m-btn--custom btn-save">
                                                    Save changes
                                                </button>

                                                &nbsp;&nbsp;
                                                <button type="reset"
                                                    class="btn btn-light m-btn m-btn--air m-btn--custom btn-cancel">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                
                            </form>
                        </div>
                        <div class="tab-pane" id="m_user_profile_tab_3">
                            <div class="m-portlet__body">

                                <div id="bar-chart" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

                            </div>
                        </div>
                        <div class="tab-pane" id="m_user_profile_tab_2">
                            <div class="m-portlet__body">
                                <div id="m_calendar"></div>
                            </div>
                        </div>

                    </div>
                </div>
                </div>
                
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    {{-- <script type="text/javascript" src="{{ asset('vendors/base/vendors.bundle.js') }}?v=1.7"></script>
    <script type="text/javascript" defer src="{{ asset('base/scripts.bundle.js') }}"></script> --}}
    <script src="{{ asset('assets/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js') }}"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/dumbbell.js"></script>
    <script defer src="https://code.highcharts.com/modules/exporting.js"></script>
    <script defer src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="{{ asset('base/style.bundle.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment.min.js"
        integrity="sha512-Izh34nqeeR7/nwthfeE0SI3c8uhFSnqxV0sI9TvTcXiFJkMd6fB644O64BRq2P/LA/+7eRvCw4GmLsXksyTHBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.0/fullcalendar.min.js"
        integrity="sha512-Za1rKe2s67q5DwADR4ciRWJKTA7rM4mAzmwyVZZq6UwaJpsXxfrPhGKAwTFNbxI4jpVWRhW33fqrM8KX/xCgEg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript">

        $('.date-picker').datepicker({
        });
        $(document).ready(function() {
            // $('.m_selectpicker').selectpicker();
            $('.m_selectpicker').select2();
            // $('#m_timepicker_1,#m_timepicker_2').timepicker({ timeFormat: 'yyyy-mm-dd hh:ii:ss' });
        });
        
        function setStartTime(e){
            $(e).timepicker({ timeFormat: 'yyyy-mm-dd hh:ii:ss' });
        }

        function setEndTime(e){
            $(e).timepicker({ timeFormat: 'yyyy-mm-dd hh:ii:ss' });
        }

        function getColorsForEvent(type) {
            switch (type) {
                case 'present':
                    return '#9ccc65';
                    break;

                case 'absent':
                    return '#ff2e7c';
                    break;

                case 'holiday':
                    return '#80DEEA';
                    break;

                case 'leave':
                    return '#FFC107';
                    break;

                default:
                    return "#000";
                    break;
            }
        }

        $('#m_calendar').fullCalendar({
            // eventClick: function(event, element) {
            //     	showbuton=true;
            //    	if(event.color==getColorsForEvent("leave"))
            //    	{
            //    		showbuton=false;
            //    	}

            //    	swal({
            //  			title: 'Select Your action',
            //  			text: "You want to Update or delete",
            //  			type: 'warning',
            //  			showCancelButton: showbuton,
            //  			confirmButtonText: 'Delete',
            //  			cancelButtonText: 'Update',
            //  			reverseButtons: true
            // 	}).then(function (result) => {
            //  			if (result.value) {
            //   				swal({
            //  					title: 'Are you sure?',
            //  					text: "You won't be able to revert this!",
            //  					type: 'warning',
            //  					showCancelButton: true,
            //  					confirmButtonText: 'Yes, delete it!',
            //  					cancelButtonText: 'No, cancel!',
            //  					reverseButtons: true
            // 			}).then(function (result) => {
            // 				if (result.value) {
            //    					var delete_url = "{{ url('/hr/delete_attendance') }}/";
            //    					if(event.color==getColorsForEvent("leave"))
            //    					{
            //    						delete_url = "{{ url('/hr/delete_leave') }}/";
            //    					}

            //    					var ontrue=function(data)
            //    					{
            //    						swal('Deleted!','Your file has been deleted.','success');
            //    						$('#m_calendar').fullCalendar( "removeEvents" , event._id);
            //    					}
            //    					fetch(delete_url+event.id,false,"DELETE","JSON",ontrue,false, false, true);
            // 				} else if (result.dismiss === swal.DismissReason.cancel) {
            //    					swal('Cancelled','Your file is safe :)','error');
            //  					}
            // 				})
            // 		}
            // 		else if (result.dismiss === swal.DismissReason.cancel) {
            //  				html= "<h1>CheckOut</h1> <br><input id='datetimepicker3'>"
            //  				if(event.end)
            //  				{
            //  					html= "<h1>CheckIn</h1> <br><input id='datetimepicker' value='"+event.start.format('Y-MM-DD hh:mm:ss')+"'>"+"<h1>CheckOut</h1> <br><input class='form-control' id='datetimepicker1' value='"+event.end.format('Y-MM-DD hh:mm:ss')+"'>";
            // 			}

            //    			swal({
            //  					html: html,
            //  				}).then(function (result){
            //  					var CheckOut=$('#datetimepicker3').val();
            //  					if(event.end) {
            // 					var checkIn=$('#datetimepicker').val();
            // 	  				var CheckOut=$('#datetimepicker1').val();
            //  					}

            //  					if(!CheckOut) {
            //  						return;
            //  					}

            //     				var eventData;
            //          			eventData = {
            //            			title: CheckOut,
            // 		            start: event.start.format('Y-MM-DD hh:mm:ss'),
            // 		            end: event.start.format('Y-MM-DD hh:mm:ss'),
            // 		            color: getColorsForEvent("present")
            //           		};

            //          			var data = {"employee":{{ $employee->id }},"id": event.id,"day":event.start.format('Y-MM-DD'),"from":event.start.format('Y-MM-DD'),"to":event.start.format('Y-MM-DD'),"time_in":event.start.format('Y-MM-DD hh:mm:ss'),"time_out":CheckOut, "shift":"day"};

            //          			if(event.end) {
            //          				var data = {"employee":{{ $employee->id }},"id": event.id,"day":event.start.format('Y-MM-DD'),"from":event.start.format('Y-MM-DD'),"to":event.start.format('Y-MM-DD'),"time_in":checkIn,"time_out":CheckOut, "shift":"day"};
            //          			}

            //         			var ontrue = function(data) {
            // 		          	$('#m_calendar').fullCalendar( "removeEvents" , event._id);
            // 		          	$('#m_calendar').fullCalendar('renderEvent', eventData, true);
            //          	  		};

            //          			fetch("{{ url('/hr/update_attendance') }}",data,"POST","JSON",ontrue,false, false, true);
            //  				});

            //    			init_pickers_time(event.start.format('Y-MM-DD hh:mm:ss'));
            //    			init_pickers_times(event.start.format('Y-MM-DD hh:mm:ss'));
            //  			}
            // 	});
            // },
            selectable: true,
            selectHelper: true,
            // select: function(startDate, endDate) {
            //    	swal({
            //  			title: 'Please Select',
            //  			input: 'select',
            //  			inputOptions: {
            //    			'present': 'Present',
            //    			'leave': 'Leave',
            //  			},
            //  			showCancelButton: true,
            //  			inputValidator: function (value) {
            //    			return new Promise(function (resolve, reject) {
            //      				if (value !== '') {
            //        				resolve();
            //      				} else {
            //        				reject('You need to select a Tier');
            //      				}
            //    			});
            //  			}
            // 	}).then(function (result) {
            //  			if (result.value) {
            //  				if (result.value == "leave") {
            //  					var title = prompt('Enter the Reason for Leave:');
            // 				var eventData;
            //          			eventData = {
            //            			title: title,
            // 		            start: startDate,
            // 		            end: endDate,
            // 		            color: getColorsForEvent("leave")
            //          			};

            //          			var data = {"employee":{{ $employee->id }},"leave_type":title,"from":startDate.format(),"to":endDate.format(), "type":"full","shift":'day'};

            //          			var ontrue=function(data){ $('#m_calendar').fullCalendar('renderEvent', eventData, true);  };

            //            		fetch("{{ url('/hr/leave') }}",data,"POST","JSON",ontrue,false, false, true);
            //        		}
            //   			else {
            //   				swal({
            //   					html: "<h1>CheckIn</h1> <br><input class='form-control' id='datetimepicker'>"+"<h1>CheckOut</h1> <br><input class='form-control' id='datetimepicker1'>",
            //   				}).then(function (result){
            //   					var checkIn=$('#datetimepicker').val();
            //   					var CheckOut=$('#datetimepicker1').val();
            //   					if(!checkIn) {
            //   						return;
            // 		  			}

            //      				var eventData;
            //           			eventData = {
            //             			title: checkIn + " - " + CheckOut,
            // 			            start: startDate,
            // 			            end: endDate,
            // 			            color: getColorsForEvent("present")
            //           			};

            //          			var data = {"employee":{{ $employee->id }},"day":startDate.format(),"from":startDate.format(),"to":endDate.format(),"time_in":checkIn,"time_out":CheckOut, "shift":"day"};

            // 		          	var ontrue=function(data){
            // 	          			eventData={
            // 	          				title:data.data.time_in + " - " + data.data.time_out,
            // 	      					id: data.data.id,
            // 	          				start: data.data.time_in,
            // 	          				color: getColorsForEvent("present"),
            // 	          				end: data.data.time_out
            // 	          			}

            // 	          			$('#m_calendar').fullCalendar('renderEvent', eventData, true);
            //           	 		};

            //           			fetch("{{ url('/hr/add_attendance') }}",data,"POST","JSON",ontrue,false, false, true);
            //   				});

            //   				init_pickers_time(startDate.format('Y-MM-DD hh:mm:ss'));
            // 	  		}
            //  			}
            //        	$('#m_calendar').fullCalendar('unselect');
            // 	});
            //    },
            eventLimit: true, // allow "more" link when too many events
            events: [
                @foreach ($attendance as $attend) {
                    id: {{ $attend->id }},
                    start: '{{ $attend->day }} {{ $attend->time_in }}',
                    end: '{{ $attend->day }} {{ $attend->time_out }}',
                    @if ($attend->time_out != '00:00:00')
                        title: '- {{ $attend->check_out }}\n{{ $attend->shift }}',
                        color: getColorsForEvent('present'),
                    @else
                        title: '{{ $attend->shift }}',
                        color: getColorsForEvent('absent'),
                    @endif
                    },
                @endforeach

                @foreach ($leaves as $leave) {
                    id: {{ $leave->id }},
                    title: '{{ $leave->leave_type }}',
                    start: '{{ $leave->day }}',
                    color: getColorsForEvent('leave')
                    },
                @endforeach

                @foreach ($holiday as $holi) {
                    id: {{ $holi->id }},
                    title: '{{ $holi->occassion }}',
                    start: '{{ $holi->from }}',
                    end: '{{ $holi->to }}',
                    color: getColorsForEvent('holiday'),
                    },
                @endforeach
            ],
        });
        //$('#example').DataTable();


        function readURL(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#blah').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").change(function() {
            readURL(this);
        });

        $('#m_datepicker_2, #m_datepicker_2_validate').datepicker({
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        });
    </script>
    <script src="{{ asset('assets/js/bootstrap-timepicker/js/bootstrap-timepicker.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
    <script type="text/javascript">
        function init_pickers_time(dateis) {
            $("#datetimepicker, #datetimepicker1").datetimepicker({
                pickDate: true,
                minuteStep: 5,
                startDate: dateis,
                /* pickerPosition: 'bottom-right',*/
                format: 'yyyy-mm-dd hh:ii:ss',
                autoclose: true,
                showMeridian: false,
                startView: 1,
                maxView: 1,
            });
        }

        function init_pickers_times(dateis) {
            $("#datetimepicker3").datetimepicker({
                pickDate: true,
                minuteStep: 5,
                startDate: dateis,
                /* pickerPosition: 'bottom-right',*/
                format: 'yyyy-mm-dd hh:ii:ss',
                autoclose: true,
                showMeridian: false,
                startView: 1,
                maxView: 1,
            });
        }

        Highcharts.chart('bar-chart', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Monthly Average Rainfall'
            },
            subtitle: {
                text: 'Source: WorldClimate.com'
            },
            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: ''
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Present',
                data: [@foreach ($Finalarrayattend as $att) {{ $att }}, @endforeach]
            }, {
                name: 'Leave',
                data: [@foreach ($Finalarrayleave as $le) {{ $le }}, @endforeach]
            }, {
                name: 'Holiday',
                data: [@foreach ($holidayfinal as $holiday) {{ $holiday }}, @endforeach]
            }]
        });
    </script>
@endsection
