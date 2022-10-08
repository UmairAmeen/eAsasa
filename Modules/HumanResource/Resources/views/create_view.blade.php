@extends('layout')
@section('css')
    <!-- <link href="{{ asset('assets/js/bootstrap-datetimepicker/css/datetimepicker.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('assets/js/bootstrap-datepicker/css/datepicker.css') }}" rel="stylesheet">
    <style>
        body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #eef5f9;
            font-size: 17px;
        }

        .panel {
            border: 1px solid #fff;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0px 1px 3px #ccc;
        }

        .wizard-content .wizard>.steps>ul>li:after,
        .wizard-content .wizard>.steps>ul>li:before {
            content: '';
            z-index: 9;
            display: block;
            position: absolute
        }

        .wizard-content .wizard {
            width: 100%;
            overflow: hidden
        }

        .wizard-content .wizard .content {
            margin-left: 0 !important
        }

        .wizard-content .wizard>.steps {
            position: relative;
            display: block;
            width: 100%
        }

        .wizard-content .wizard>.steps .current-info {
            position: absolute;
            left: -99999px
        }

        .wizard-content .wizard>.steps>ul {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin: 0;
            padding: 0;
            list-style: none
        }

        .wizard-content .wizard>.steps>ul>li {
            display: table-cell;
            width: auto;
            vertical-align: top;
            text-align: center;
            position: relative
        }

        .wizard-content .wizard>.steps>ul>li a {
            position: relative;
            padding-top: 52px;
            margin-top: 20px;
            margin-bottom: 20px;
            display: block
        }

        .wizard-content .wizard>.steps>ul>li:before {
            left: 0
        }

        .wizard-content .wizard>.steps>ul>li:after {
            right: 0
        }

        .wizard-content .wizard>.steps>ul>li:first-child:before,
        .wizard-content .wizard>.steps>ul>li:last-child:after {
            content: none
        }

        .wizard-content .wizard>.steps>ul>li.current>a {
            color: #2f3d4a;
            cursor: default
        }

        .wizard-content .wizard>.steps>ul>li.current .step {
            border-color: #009efb;
            background-color: #fff;
            color: #009efb
        }

        .wizard-content .wizard>.steps>ul>li.disabled a,
        .wizard-content .wizard>.steps>ul>li.disabled a:focus,
        .wizard-content .wizard>.steps>ul>li.disabled a:hover {
            color: #999;
            cursor: default
        }

        .wizard-content .wizard>.steps>ul>li.done a,
        .wizard-content .wizard>.steps>ul>li.done a:focus,
        .wizard-content .wizard>.steps>ul>li.done a:hover {
            color: #999
        }

        .wizard-content .wizard>.steps>ul>li.done .step {
            background-color: #009efb;
            border-color: #009efb;
            color: #fff
        }

        .wizard-content .wizard>.steps>ul>li.error .step {
            border-color: #f62d51;
            color: #f62d51
        }

        .wizard-content .wizard>.steps .step {
            background-color: #fff;
            display: inline-block;
            position: absolute;
            top: 0;
            left: 50%;
            margin-left: -24px;
            z-index: 10;
            text-align: center
        }

        .wizard-content .wizard>.content {
            overflow: hidden;
            position: relative;
            width: auto;
            padding: 0;
            margin: 0
        }

        .wizard-content .wizard>.content>.title {
            position: absolute;
            left: -99999px
        }

        .wizard-content .wizard>.content>.body {
            padding: 0 20px
        }

        .wizard-content .wizard>.content>iframe {
            border: 0;
            width: 100%;
            height: 100%
        }

        .wizard-content .wizard>.actions {
            position: relative;
            display: block;
            text-align: right;
            padding: 0 20px 20px
        }

        .wizard-content .wizard>.actions>ul {
            float: right;
            list-style: none;
            padding: 0;
            margin: 0
        }

        .wizard-content .wizard>.actions>ul:after {
            content: '';
            display: table;
            clear: both
        }

        .wizard-content .wizard>.actions>ul>li {
            float: left
        }

        .wizard-content .wizard>.actions>ul>li+li {
            margin-left: 10px
        }

        .wizard-content .wizard>.actions>ul>li>a {
            background: #009efb;
            color: #fff;
            display: block;
            padding: 7px 12px;
            border-radius: 4px;
            border: 1px solid transparent;
        }

        .wizard-content .wizard>.actions>ul>li>a:focus,
        .wizard-content .wizard>.actions>ul>li>a:hover {
            -webkit-box-shadow: 0 0 0 100px rgba(0, 0, 0, .05) inset;
            box-shadow: 0 0 0 100px rgba(0, 0, 0, .05) inset
        }

        .wizard-content .wizard>.actions>ul>li>a:active {
            -webkit-box-shadow: 0 0 0 100px rgba(0, 0, 0, .1) inset;
            box-shadow: 0 0 0 100px rgba(0, 0, 0, .1) inset
        }

        .wizard-content .wizard>.actions>ul>li>a[href="#previous"] {
            background-color: #fff;
            color: #54667a;
            border: 1px solid #d9d9d9
        }

        .wizard-content .wizard>.actions>ul>li>a[href="#previous"]:focus,
        .wizard-content .wizard>.actions>ul>li>a[href="#previous"]:hover {
            -webkit-box-shadow: 0 0 0 100px rgba(0, 0, 0, .02) inset;
            box-shadow: 0 0 0 100px rgba(0, 0, 0, .02) inset
        }

        .wizard-content .wizard>.actions>ul>li>a[href="#previous"]:active {
            -webkit-box-shadow: 0 0 0 100px rgba(0, 0, 0, .04) inset;
            box-shadow: 0 0 0 100px rgba(0, 0, 0, .04) inset
        }

        .wizard-content .wizard>.actions>ul>li.disabled>a,
        .wizard-content .wizard>.actions>ul>li.disabled>a:focus,
        .wizard-content .wizard>.actions>ul>li.disabled>a:hover {
            color: #999
        }

        .wizard-content .wizard>.actions>ul>li.disabled>a[href="#previous"],
        .wizard-content .wizard>.actions>ul>li.disabled>a[href="#previous"]:focus,
        .wizard-content .wizard>.actions>ul>li.disabled>a[href="#previous"]:hover {
            -webkit-box-shadow: none;
            box-shadow: none
        }

        .wizard-content .wizard.wizard-circle>.steps>ul>li:after,
        .wizard-content .wizard.wizard-circle>.steps>ul>li:before {
            top: 45px;
            width: 50%;
            height: 3px;
            background-color: #009efb
        }

        .wizard-content .wizard.wizard-circle>.steps>ul>li.current:after,
        .wizard-content .wizard.wizard-circle>.steps>ul>li.current~li:after,
        .wizard-content .wizard.wizard-circle>.steps>ul>li.current~li:before {
            background-color: #F3F3F3
        }

        .wizard-content .wizard.wizard-circle>.steps .step {
            width: 50px;
            height: 50px;
            line-height: 45px;
            border: 3px solid #F3F3F3;
            font-size: 1.3rem;
            border-radius: 50%
        }

        .wizard-content .wizard.wizard-notification>.steps>ul>li:after,
        .wizard-content .wizard.wizard-notification>.steps>ul>li:before {
            top: 39px;
            width: 50%;
            height: 2px;
            background-color: #009efb
        }

        .wizard-content .wizard.wizard-notification>.steps>ul>li.current .step {
            border: 2px solid #009efb;
            color: #009efb;
            line-height: 36px
        }

        .wizard-content .wizard.wizard-notification>.steps>ul>li.current .step:after,
        .wizard-content .wizard.wizard-notification>.steps>ul>li.done .step:after {
            border-top-color: #009efb
        }

        .wizard-content .wizard.wizard-notification>.steps>ul>li.current:after,
        .wizard-content .wizard.wizard-notification>.steps>ul>li.current~li:after,
        .wizard-content .wizard.wizard-notification>.steps>ul>li.current~li:before {
            background-color: #F3F3F3
        }

        .wizard-content .wizard.wizard-notification>.steps>ul>li.done .step {
            color: #FFF
        }

        .wizard-content .wizard.wizard-notification>.steps .step {
            width: 40px;
            height: 40px;
            line-height: 40px;
            font-size: 1.3rem;
            border-radius: 15%;
            background-color: #F3F3F3
        }

        .wizard-content .wizard.wizard-notification>.steps .step:after {
            content: "";
            width: 0;
            height: 0;
            position: absolute;
            bottom: 0;
            left: 50%;
            margin-left: -8px;
            margin-bottom: -8px;
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-top: 8px solid #F3F3F3
        }

        .wizard-content .wizard.vertical>.steps {
            display: inline;
            float: left;
            width: 20%
        }

        .wizard-content .wizard.vertical>.steps>ul>li {
            display: block;
            width: 100%
        }

        .wizard-content .wizard.vertical>.steps>ul>li.current:after,
        .wizard-content .wizard.vertical>.steps>ul>li.current:before,
        .wizard-content .wizard.vertical>.steps>ul>li.current~li:after,
        .wizard-content .wizard.vertical>.steps>ul>li.current~li:before,
        .wizard-content .wizard.vertical>.steps>ul>li:after,
        .wizard-content .wizard.vertical>.steps>ul>li:before {
            background-color: transparent
        }

        @media (max-width:768px) {
            .wizard-content .wizard>.steps>ul {
                margin-bottom: 20px
            }

            .wizard-content .wizard>.steps>ul>li {
                display: block;
                float: left;
                width: 50%
            }

            .wizard-content .wizard>.steps>ul>li>a {
                margin-bottom: 0
            }

            .wizard-content .wizard>.steps>ul>li:first-child:before {
                content: ''
            }

            .wizard-content .wizard>.steps>ul>li:last-child:after {
                content: '';
                background-color: #009efb
            }

            .wizard-content .wizard.vertical>.steps {
                width: 15%
            }
        }

        @media (max-width:480px) {
            .wizard-content .wizard>.steps>ul>li {
                width: 100%
            }

            .wizard-content .wizard>.steps>ul>li.current:after {
                background-color: #009efb
            }

            .wizard-content .wizard.vertical>.steps>ul>li {
                display: block;
                float: left;
                width: 50%
            }

            .wizard-content .wizard.vertical>.steps {
                width: 100%;
                float: none;
            }
        }

        .panel-heading .accordion-toggle:after {
            font-family: 'Glyphicons Halflings';
            content: "\e114";
            float: right;
            color: grey;
        }

        .panel-heading .accordion-toggle.collapsed:after {
            content: "\e080";
        }

    </style>
@endsection
@section('content')
    <div class="page-header clearfix">
        <div class="align-items-center">
            <div class="mr-auto" style="display: flex; justify-content: space-between; align-items: center; ">
                <div>
                    <h1 class="m-subheader__title m-subheader__title--separator">Add New Employee</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="content-panel">

        <!--Begin::Main Portlet-->
        <div class="m-portlet">
            <div class="panel">
                <div class="panel-body wizard-content">
                    <form id="formCreateEmployee" action="{{ url('/hr/store') }}" method="POST" multiple="multiple"
                          enctype="multipart/form-data" class="tab-wizard wizard-circle wizard clearfix">
                        {{ csrf_field() }}
                       
                        <h6>Personal Information</h6>
                        <section id="personalInformation">
                            <br />
                            <div class="col-xl-8 offset-xl-2">
                                <div class="m-form__section m-form__section--first">
                                    <div class="m-form__heading">
                                        <h3 class="m-form__heading-title">Employee Details</h3>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xl-3 col-lg-3 col-form-label">* Name:</label>
                                        <div class="col-xl-9 col-lg-9">
                                            <input type="text" name="name" class="form-control m-input"
                                                placeholder="Enter Employee Name" value="">
                                            <span class="m-form__help"></span>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xl-3 col-lg-3 col-form-label">* Father Name:</label>
                                        <div class="col-xl-9 col-lg-9">
                                            <input type="text" name="father_name" class="form-control m-input"
                                                placeholder="Enter Father Name" value="">
                                            <span class="m-form__help"></span>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xl-3 col-lg-3 col-form-label">* Position:</label>
                                        <div class="col-xl-9 col-lg-9">
                                            <input type="text" name="position" class="form-control m-input"
                                                placeholder="Enter Employee Position" value="">
                                            <span class="m-form__help"></span>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xl-3 col-lg-3 col-form-label">* Joining Date:</label>
                                        <div class="col-xl-9 col-lg-9">
                                            <input type="text" name="date_of_joining" class="form-control m-input" readonly
                                                placeholder="Select date" id="m_datepicker_2" />
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xl-3 col-lg-3 col-form-label">* Phone:</label>
                                        <div class="col-xl-9 col-lg-9">
                                            <input name="phone" type="tel" pattern="^\d{11}$" maxlength="11"
                                                class="form-control m-input" placeholder="XXXXXXXXXXXX" value="">
                                            <span class="m-form__help"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="m-separator m-separator--dashed m-separator--lg"></div>

                                <div class="m-form__section">
                                    <div class="m-form__heading">
                                        <h3 class="m-form__heading-title">
                                            Mailing Address
                                            <i data-toggle="m-tooltip" data-width="auto"
                                                class="m-form__heading-help-icon flaticon-info"
                                                title="Some help text goes here"></i>
                                        </h3>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xl-3 col-lg-3 col-form-label">* Address:</label>
                                        <div class="col-xl-9 col-lg-9">
                                            <input type="text" name="address" class="form-control m-input"
                                                placeholder="Enter Employe address" value="">
                                            <span class="m-form__help"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="m-separator m-separator--dashed m-separator--lg"></div>

                                <div class="m-form__section">
                                    <div class="m-form__heading">
                                        <h3 class="m-form__heading-title">
                                            Point to Note
                                            <i data-toggle="m-tooltip" data-width="auto"
                                                class="m-form__heading-help-icon flaticon-info"
                                                title="Some help text goes here"></i>
                                        </h3>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xl-3 col-lg-3 col-form-label">Description:</label>
                                        <div class="col-xl-9 col-lg-9">
                                            <textarea class="form-control" rows="4" placeholder="Comments" name="description" style="width: 100%"></textarea>
                                            <span class="m-form__help"></span>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <!-- </div> -->
                        </section>
                        <h6> Legal Information </h6>
                        <section id="legalInformation">
                            <div class="col-xl-8 offset-xl-2">
                                <div class="m-form__heading">
                                    <h3>Legal Information</h3>
                                </div>
                                <div class="form-group m-form__group row">
                                    <div class="col-lg-6">
                                        <label class="col-xl-3 col-lg-3 col-form-label">* CNIC:</label>
                                        <div class="col-xl-9 col-lg-9">
                                            <input type="tel" name="cnic" data-mask="99999-9999999-9" {{-- pattern="^\d{5}-\d{7}-\d{1}$" --}}
                                                class="form-control m-input" placeholder="XXXXX-XXXXXXX-X" value="">
                                            {{-- <span class="m-form__help"></span> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <div class="col-lg-6">
                                        <label class="col-xl-3 col-lg-3 col-form-label">Profile Picture:</label>
                                        <div class="col-xl-9 col-lg-9">
                                            <input type="file" name="profile_pic" id="imgInp"
                                                class="form-control m-input" accept="image/*">
                                            <span class="m-form__help"></span>
                                            <div class="col-xl-2" align="center">
                                                {{-- <div class="m-card-profile__pic">
                                                    <div class="m-card-profile__pic-wrapper">
                                                        <img id="blah" src="{{ profilepic("abc") }}"  style="width: 130px;height: 130px; border-radius: 70px; " alt="" />
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-separator m-separator--dashed m-separator--lg"></div>
                                <div class="m-form__section">
                                    <div class="m-form__heading">
                                        <h3 class="m-form__heading-title">Job type Settings</h3>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <div class="col-lg-8">
                                            <label class="col-xl-3 col-lg-3 col-form-label">* Employee Type:</label>
                                            <div class="col-xl-9 col-lg-9 m-radio-inline">
                                                <label class="m-radio m-radio--solid m-radio--brand">
                                                    <input type="radio" name="type" value="Salary"> Salary
                                                    <span></span>
                                                </label>
                                                <label class="m-radio m-radio--solid m-radio--brand">
                                                    <input type="radio" name="type" value="Daily Wage"> Daily Wage
                                                    <span></span>
                                                </label>
                                                <label class="m-radio m-radio--solid m-radio--brand">
                                                    <input type="radio" name="type" value="Contract"> Contract
                                                    <span></span>
                                                </label>
                                            </div>
                                            <span class="m-form__help">Please select Employee type</span>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <div class="col-lg-6">
                                            <label class="col-xl-3 col-lg-3 col-form-label" for="billing_type">Billing
                                                Type:</label>
                                            <div class="col-xl-9 col-lg-9 m-radio-inline">
                                                <select class="form-control" name="billing_type">
                                                    <option value="per_day"
                                                        {{ is_selected(settings('hr_billing_type'), 'per_day') }}>
                                                        Per Day
                                                    </option>
                                                    <option value="per_hour"
                                                        {{ is_selected(settings('hr_billing_type'), 'per_hour') }}>
                                                        Per Hour
                                                    </option>
                                                    <option value="no_deduction"
                                                        {{ is_selected(settings('hr_billing_type'), 'no_deduction') }}>
                                                        No Time/Day Based Deduction
                                                    </option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <h6>Employment Information</h6>
                        <section id="employmentInformation">
                            <div class="col-xl-8 offset-xl-2">
                                <div class="m-form__section m-form__section--first">
                                    <div class="m-form__heading">
                                        <h3 class="m-form__heading-title">Employee Setting</h3>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xs-3 form-control-label">* Salary:</label>
                                        <div class="col-xs-6">
                                            <input type="number" name="salary" min="1" class="form-control m-input"
                                                placeholder="" value="Nick Stone">
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xs-3 form-control-label">* Start time:</label>
                                        <div class="col-xs-6">
                                            <input class="form-control" name="start_time" id="m_timepicker_1" readonly
                                                placeholder="Select time" type="text" />
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xs-3 form-control-label">* Ends time:</label>
                                        <div class="col-xs-6">
                                            <input class="form-control" name="end_time" id="m_timepicker_2" readonly
                                                placeholder="Select time" type="text" />
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-xs-3 form-control-label">* Working days:</label>
                                        <div class="col-xs-6">
                                            <select class="form-control m-bootstrap-select m_selectpicker" name="w_days[]"
                                                multiple>
                                                <option selected value="monday">Monday</option>
                                                <option selected value="tuesday">Tuesday</option>
                                                <option selected value="wednesday">Wednesday</option>
                                                <option selected value="thursday">Thursday</option>
                                                <option selected value="friday">Friday</option>
                                                <option selected value="saturday">Saturday</option>
                                                <option value="sunday">Sunday</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-separator m-separator--dashed m-separator--lg"></div>
                                <div class="form-group m-form__group m-form__group--sm row">
                                    <div class="col-xl-12">
                                        <div class="m-checkbox-inline">
                                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                                <input type="checkbox" name="accept" value="">
                                                Click here to indicate that you have put all information according to your
                                                best knowledge
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit">Add Employee</button>
                                </div>
                            </div>
                        </section>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!--End::Main Portlet-->
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.js"
        integrity="sha512-bE0ncA3DKWmKaF3w5hQjCq7ErHFiPdH2IGjXRyXXZSOokbimtUuufhgeDPeQPs51AI4XsqDZUK7qvrPZ5xboZg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript">
        function initialize() {}

        $(document).ready(function() {
            $("#formCreateEmployee").steps({
                headerTag: "h6",
                bodyTag: "section",
                transitionEffect: "fade",
                titleTemplate: '<span class="step">#index#</span> #title#'
            });
        //     function readURL(input) {
        //     if (input.files && input.files[0]) {
        //         var reader = new FileReader();
        //         reader.onload = function(e) {
        //             $('#blah').attr('src', e.target.result);
        //         }
        //         reader.readAsDataURL(input.files[0]);
        //     }
        // }
        // $("#imgInp").change(function() {
        //     readURL(this);
        // });

            // $('#m_timepicker_1,#m_timepicker_2').timepicker({ timeFormat: 'H:mm:ss' });
            $("#m_timepicker_1, #m_timepicker_2").datetimepicker({
                pickDate: true,
                minuteStep: 5,
                format: 'hh:ii:ss',
                autoclose: true,
                showMeridian: false,
                startView: 1,
                maxView: 1,
            });
            // $('.m_selectpicker').selectpicker();
            $('.m_selectpicker').select2();
            $('#m_datepicker_2, #m_datepicker_2_validate').datepicker({
                todayHighlight: true,
                format: 'yyyy-mm-dd',
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="fa fa-angle-left"></i>',
                    rightArrow: '<i class="fa fa-angle-right"></i>'
                }
            });

            /*$('#m_wizard').steps({headerTag: "h3", bodyTag: "section", transitionEffect: "slideLeft", titleTemplate: '<span class="step">#index#</span> #title#'});*/
        });
        $("#m_form").submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: "json",
                success: function(resultData) {
                    toastr.success("Employee Information has been Saved");
                    return window.location = "{{ url('hr') }}";
                    // redirect to hr
                },
                error: function(error) {
                    var msg = "";
                    var isshow = false;
                    msg = $.map(error.responseJSON, function(n) {
                        return msg + n;
                    })

                    $(msg).each(function(in2, mesg) {
                        isshow = true;
                        toastr.error(mesg);
                    });

                    if (!isshow) {
                        toastr.error("Some Issue Occured, Unable to save");
                    }
                }
            });
        });
        $('.revalue').click(function(index, ele) {
            $("#first_tab").html('');
            $("#m_wizard_form_step_1 input, textarea").each(function(index, element) {
                var title = $(element).parent().parent().find("label").first().html();
                var value = $(element).val();
                // console.log("Show: "+title+"--"+value);
                var html = '<div class="form-group m-form__group m-form__group--sm row">\
                        <label class="col-xl-4 col-lg-4 col-form-label">' + title + '</label>\
                        <div class="col-xl-8 col-lg-8">\
                            <span class="m-form__control-static">' + value + '</span>\
                        </div>\
                    </div>';
                $("#first_tab").append(html);
            });
            $("#second_tab").html('');
            $("#m_wizard_form_step_2 input").each(function(index, element) {
                var title = $(element).parent().parent().find("label").first().html();
                var value = $(element).val();

                // console.log("Show: "+title+"--"+value);
                var html = '<div class="form-group m-form__group m-form__group--sm row">\
                        <label class="col-xl-4 col-lg-4 col-form-label">' + title + '</label>\
                        <div class="col-xl-8 col-lg-8">\
                            <span class="m-form__control-static">' + value + '</span>\
                        </div>\
                    </div>';
                $("#second_tab").append(html);
            });
            $("#third_tab").html('');
            $("#m_wizard_form_step_3 input").each(function(index, element) {
                // console.log(element);
                var title = $(element).parent().parent().find("label").first().html();
                var value = $(element).val();
                // console.log("Show: "+title+"--"+value);
                var html = '<div class="form-group m-form__group m-form__group--sm row">\
                        <label class="col-xl-4 col-lg-4 col-form-label">' + title + '</label>\
                        <div class="col-xl-8 col-lg-8">\
                            <span class="m-form__control-static">' + value + '</span>\
                        </div>\
                    </div>';
                $("#third_tab").append(html);
            });
        });
    </script>
@endsection
