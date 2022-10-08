@extends('layout')
@section('header')
@section('css')
    <style>

        .employee-card {

            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            padding-bottom: 20px;
        }

        .head-body {
            background: linear-gradient(to right, #909 20%, #4F30A2 120%);
            padding-bottom: 30px;
        }

        .card-head {
            display: flex;
            flex-flow: column;
            text-align: center;
            padding-top: 10px;
            color: white;
        }

        .delete-icon {
            cursor: pointer;
            margin-left: 8px;
            font-size: 18px;
        }

        .employee-picture {
            border-radius: 50%;
            text-align: center;
            margin: 40px 0px;
        }

        .employee-name {
            text-align: center;
            margin-top: -20px;
        }

        .employee-name a {
            border: 1px solid #ebedf2;
            background-color: white;
            border-radius: 25px;
            padding: 10px 25px;
            color: #7b7e8a;
        }

        .card-footer {
            display: flex;
            justify-content: space-around;
            margin-top: 50px;
            margin-bottom: 30px;
        }

    </style>
@endsection
@section('content')

    <!-- <div class="m-subheader "> -->
    <div class="page-header clearfix">
        <div class="align-items-center">
            <div class="mr-auto" style="display: flex; justify-content: space-between; align-items: center; ">
                <div>
                    <h1 class="m-subheader__title m-subheader__title--separator">Human Resource</h1>
                </div>

                <div style="    float: right;padding: 5px 0px 0px 0px;">
                    <input id="myInput" style="width: 277px; border: none;border-radius: 72px;padding: 5px 7px 5px 7px;"
                           type="search" name="search" placeholder="Search">
                </div>
            </div>

        </div>
    </div>

    <div class="content-panel">
        <div class="m-portlet m-portlet--mobile">
            <div class="m-portlet__body">
                @if(session()->has('message.level'))
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
                                        <div class="m-form__label">
                                            <a href="{{ URL('hr/leaves') }}"
                                               class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--pill">
                                                <span><i class="flaticon-danger"></i><span>Leaves</span></span>
                                            </a>
                                        </div>

                                        <div class="m-form__control">

                                        </div>
                                    </div>
                                    <!-- <div class="d-md-none m--margin-bottom-10"></div> -->
                                </div>
                                <div class="col-md-2">
                                    <div class="m-form__label">
                                        <a href="{{ URL('hr/attendances') }}"
                                           class="btn btn-warning m-btn m-btn--custom m-btn--icon m-btn--pill">
                                                <span><i class="flaticon-clock-1"></i><span>Attendances</span></span>
                                        </a>
                                    </div>
                                </div>


                                <div class="col-md-2">
                                    <div class="m-form__label">
                                        <a href="{{ URL('hr/bonuslist') }}"
                                           class="btn btn-warning m-btn m-btn--custom m-btn--icon m-btn--pill">
                                                <span><i class="flaticon-add"></i><span>Add Bonus</span></span>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="m-form__label">
                                        <a href="{{ URL('hr/salary') }}"
                                           class="btn btn-warning m-btn m-btn--custom m-btn--icon m-btn--pill">
                                                <span><i class="flaticon-clock-1"></i><span>Salary Reporting</span></span>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="m-form__label">
                                        <a href="{{ URL('hr/pholidays') }}"
                                           class="btn btn-warning m-btn m-btn--custom m-btn--icon m-btn--pill">
                                                <span><i class="flaticon-clock-1"></i><span>Public Holidays</span></span>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="m-form__label">
                                        <a href="{{ url('/hr/create') }}"
                                           class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--pill">
                                            <span><i class="flaticon-app"></i><span>Add New</span></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end: Search Form -->
                <!--begin: Datatable -->
                <div class="panel-body">
                    <div class="row">
                        @foreach($employee as $employ)
                            @include('humanresource::card')
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript">
        var url = "<?php echo url('hr')?>";

        /* get data from database*/
        $(document).ready(function () {
            $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url("hr/datatable") }}',
                columns: [

                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'father_name', name: 'father_name'},
                    {data: 'phone', name: 'phone'},
                    {data: 'cnic', name: 'cnic'},
                    {data: 'position', name: 'position'},
                    {data: 'action', name: 'action'}
                ]
            });
        });

        /* Search Data from table*/
        $(document).ready(function () {
            $("#myInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("[data-id='searchthis']").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // var table = $('.datatable').DataTable();
            // Event listener to the two range filtering inputs to redraw on input
            $('#m_form_search').keyup(function () {
                table.search($(this).val()).draw();
            });
        });

        function deletethis(ele, id) {
            swal({
                animation: false,
                title: 'Are you sure?',
                text: "You wish to delete Employe",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1e8fe1',
                cancelButtonColor: '#ffffff',
                confirmButtonText: 'Delete',
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.value) {
                    var onsucess = function (data) {
                        $(ele).parentsUntil('.remove').parent().remove();
                    }
                    ajaxcall("{{url('hr/del')}}/" + id, "DELETE", false, false, onsucess);
                }
            });
        }

        $("body").on("click", ".remove-item", function () {
            if (confirm("Are you sure you want to delete this!")) {

                var id = $(this).data('id');
                $.ajax({
                    dataType: 'json',
                    type: 'DELETE',
                    url: url + '/del/' + id,
                }).done(function () {

                    toastr.success('Deleted Successfully.', 'Success Alert', {timeOut: 5000});
                    window.setTimeout(function () {
                        location.reload()
                    }, 1000);
                }).fail(function (request) {
                    $.each(request.responseJSON, function (d, t) {
                        toastr.error(t.join(""), 'Not Deleted .', 'Failed Alert', {timeOut: 5000});
                    });

                });
            }
        });

        @if(Session::has('success'))
        toastr.success("{{ Session::get('success') }}", 'Success Alert', {timeOut: 5000});
        @endif

        @if(Session::has('error'))
        toastr.error("{{ Session::get('error') }}", "Failed Alert", {timeOut: 5000});
        @endif

    </script>

@endsection