@extends('layout')
@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="glyphicon glyphicon-user"></i> Sales Person
            @if (is_allowed('salesPerson-create'))
                <a accesskey="n" class="btn btn-success pull-right" href="{{ route('salesPerson.create') }}"><i
                        class="glyphicon glyphicon-plus"></i> Create <br><small>ALT + N</small></a>
            @endif
        </h1>

    </div>
@endsection

@section('content')
    <div class="row mt">

        <div class="col-md-12">


            @if (is_allowed('salesPerson-import-export'))
                <a href="{{ url('salesPerson.xlsx') }}">
                    <div class="col-md-2 col-sm-2 box0">
                        <div class="box1">
                            <span class="fa fa-arrow-down"></span>
                            <h3>Download Excel</h3>
                            <span>Sales Person Data</span>
                        </div>
                    </div>
                </a>

                <a data-toggle="modal" data-target="#uploadExcel">
                    <div class="col-md-2 col-sm-2 box0">
                        <div class="box1">
                            <span class="fa fa-arrow-up"></span>
                            <h3>Import Excel</h3>
                        </div>
                    </div>
                </a>

            @endif

        </div>
        <div class="col-md-12 p-x-2">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="content-panel">
                        <table class="table table-condensed table-striped salesPerson_datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    @if(is_admin())
                                    <th>Commission</th>
                                    @endif
                                    <th>Address</th>
                                    <th>OPTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div aria-hidden="true" aria-labelledby="Upload Excel" role="dialog" data-backdrop="static" id="uploadExcel"
                class="modal fade">
                <div class="modal-dialog" style="width: 90%">
                    <div class="modal-content col-md-12">
                        <form id="uploadExcelFile" action="{{ url('uploadSalesPersonExcel') }}" method="POST"
                            enctype="multipart/form-data" class="no-ajax">
                            <div class="col-md-12">
                                <div class="content-panel">
                                    <div id="log"></div>
                                    <center>
                                        <h1>Please Attach Excel File (xls/xlsx)</h1>
                                    </center>
                                    <div class="form-group">
                                        <input type="file" name="importexcel" class="form-control" accept=".xlsx,.xls">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
                                <center>
                                    <button class="btn btn-primary btn-lg" type="submit">Upload</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"
                                        aria-label="Close">Close</button>
                                </center>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @endsection
        @section('scripts')
            <?php $messageTop = 'Sales Person List';
            $exportingCol = '0,1,2,3,4,8,9'; ?>
            <script type="text/javascript">
                var table = false;
                $(document).ready(function() {
                    table = $('.salesPerson_datatable').DataTable({
                        "ajax": "/salesPerson_listing_datatable",
                        "processing": true,
                        "serverSide": true,
                        responsive: true,
                        dom: "Blfrtip",
                        lengthMenu: [
                            [10, 25, 50, -1],
                            [10, 25, 50, "All"]
                        ],
                        "order": [
                            [0, 'asc']
                        ],
                        "columns": [{
                                "data": "id",
                                "orderable": true,
                                "searchable": true
                            },
                            {
                                "data": 'name'
                            },
                            {
                                "data": "phone"
                            },
                            @if(is_admin())
                            {
                                "data": "commission"
                            },
                            @endif
                            {
                                "data": "address",
                                "defaultContent": "-"
                            },
                            {
                                "data": "options",
                                "orderable": false,
                                "searchable": false
                            }

                        ],
                        buttons: [
                            <?= get_print_template('.datatable', 'copy', 'SalesPerson', $messageTop, $exportingCol) ?>,
                            <?= get_print_template('.datatable', 'excel', 'SalesPerson', $messageTop, $exportingCol) ?>,
                            <?= get_print_template('.datatable', 'print', 'SalesPerson', $messageTop, $exportingCol) ?>
                        ],
                        "fnDrawCallback": function(oSettings) {}
                    });


                });
            </script>
        @endsection
