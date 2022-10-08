@extends('layout')
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
        <div class="m-portlet m-portlet--mobile">
            <div class="m-portlet__head">
                <div class="m-portlet__body">
                    @if (session()->has('message.level'))
                        <div class="alert alert-{{ session('message.level') }}">
                            {!! session('message.content') !!}
                        </div>
                    @endif
                    <!--begin: Search Form -->
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-lg-12 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-2">
                                        <div class="m-form__group m-form__group--inline">
                                            @if (is_allowed('access-hr'))
                                                <div class="m-form__label">
                                                    <a href="{{ url('/hr/attendance/add') }}"
                                                        class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                                                        <span>
                                                            <i class="flaticon-add"></i>
                                                            <span>
                                                                Multiple Attendance
                                                            </span>
                                                        </span>
                                                    </a>
                                                </div>
                                            @endif

                                        </div>

                                    </div>
                                    <div class="col-md-4"> <a href="{{ URL('hr/attendance') }}"
                                            class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                                            <span>
                                                <i class="flaticon-app"></i>
                                                <span>
                                                    Single Attendance
                                                </span>
                                            </span>
                                        </a>
                                    </div>

                                </div>
                            </div>
                           
                        </div>
                    </div>


                    <div class="table-responsive">
                        <table class="table table-condensed table-striped datatable1">
                            <thead class="m-datatable__head">
                                <tr class="m-datatable__row">
                                    <!-- <th data-field="RecordID" class="m-datatable__cell--center m-datatable__cell m-datatable__cell--check m-datatable__cell--sort">
                                    <span>
                                    <label class="m-checkbox m-checkbox--single m-checkbox--all m-checkbox--solid m-checkbox--brand">
                                    <input type="checkbox"><span></span></label></span></th> -->
                                    <th data-field="ID" class="m-datatable__cell m-datatable__cell--sort"><span>ID</span>
                                    </th>
                                    <th data-field="Name" class="m-datatable__cell m-datatable__cell--sort"><span>Employee
                                            Name</span></th>
                                    <th data-field="Type" class="m-datatable__cell m-datatable__cell--sort"><span>Employee
                                            Type</span></th>
                                    <th data-field="Date" class="m-datatable__cell m-datatable__cell--sort"><span>Day</span>
                                    </th>
                                    <th data-field="Date" class="m-datatable__cell m-datatable__cell--sort">
                                        <span>Shift</span>
                                    </th>
                                    <th data-field="CheckIn" class="m-datatable__cell m-datatable__cell--sort"><span>Check
                                            In</span></th>
                                    <th data-field="CheckOut" class="m-datatable__cell m-datatable__cell--sort"><span>Check
                                            Out</span></th>
                                    <th data-field="Overtime" class="m-datatable__cell m-datatable__cell--sort">
                                        <span>Overtime</span>
                                    </th>
                                    <th data-field="Action" class="m-datatable__cell m-datatable__cell--sort">
                                        <span>Action</span>
                                    </th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                    <!--end: Datatable -->
                </div>
            </div>
        </div>
    @stop


    @section('scripts')
        <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
        <script type="text/javascript">
            var url = "<?php echo url('hr'); ?>";
            var date = "<?php echo date('Y-m-d'); ?>";

            /* get data from database*/
            $(document).ready(function() {
                $('.datatable1').DataTable({
                    "oSearch": {
                        // "sSearch": date
                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{{ url('hr/attendances_datatable') }}',
                    columns: [

                        {
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'employee_type',
                            name: 'employee_type'
                        },
                        {
                            data: 'day',
                            name: 'day'
                        },
                        {
                            data: 'shift',
                            name: 'shift'
                        },
                        {
                            data: 'time_in',
                            name: 'time_in'
                        },
                        {
                            data: 'time_out',
                            name: 'time_out'
                        },
                        {
                            data: 'overtime',
                            name: 'overtime'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        }
                    ]
                });
            });

            /* Search Data from table*/
            $("body").on("click", ".remove-item", function() {
                if (confirm("Are you sure you want to delete this!")) {

                    var id = $(this).data('id');
                    $.ajax({
                        dataType: 'json',
                        type: 'DELETE',
                        url: url + '/delete_attendance/' + id,
                    }).done(function() {

                        toastr.success('Deleted Successfully.', 'Success Alert', {
                            timeOut: 5000
                        });
                        window.setTimeout(function() {
                            location.reload()
                        }, 1000);
                    }).fail(function(request) {
                        $.each(request.responseJSON, function(d, t) {
                            toastr.error(t.join(""), 'Not Deleted .', 'Failed Alert', {
                                timeOut: 5000
                            });
                        });

                    });
                }
            });

            $(document).ready(function() {
                    $.ajax({
                        dataType: 'json',
                        type: 'GET',
                        url: url + '/machine_attendance',
                    }).done(function() {
                    }).fail(function(request) {
                    });
                });
        </script>

    @endsection
