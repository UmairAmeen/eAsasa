@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fa fa-truck"></i> Suppliers
            <a class="btn btn-success pull-right" href="{{ route('suppliers.create') }}" accesskey="n"><i
                    class="glyphicon glyphicon-plus"></i> Create <br><small>ALT + N</small></a>
        </h1>

    </div>
@endsection

@section('content')
    <div class="col-md-12">
        <a href="{{ url('expenseHeads.xlsx') }}">
            <div class="col-md-2 col-sm-2 box0">
                <div class="box1">
                    <span class="fa fa-arrow-down"></span>
                    <h3>Download Excel</h3>
                    <span>Suppliers Data</span>
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
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="content-panel">

                @if ($suppliers->count())
                    <table class="table table-condensed table-striped customer_databale">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Address</th>
                                <th>Description</th>
                                <th>Balance</th>
                                <th class="text-right">OPTIONS</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($suppliers as $supplier)
                                <tr>
                                    <td>{{ $supplier->id }}</td>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>{{ $supplier->address ? $supplier->address : 'N/A' }}</td>
                                    <td>{{ $supplier->description }}</td>
                                    <td>{{ amount_cdr(getSupplierBalance($supplier->id), true) }}</td>
                                    <td class="text-right">
                                        <ul class="list-inline">

                                            <li><a class="btn btn-xs btn-primary"
                                                    href="{{ route('suppliers.show', $supplier->id) }}"><i
                                                        class="glyphicon glyphicon-eye-open"></i> View Details</a></li>

                                            <li><a class="btn btn-xs btn-default" target="_blank"
                                                    href="{{ route('supplier_price_records.show', $supplier->id) }}">Price
                                                    List</a></li>

                                            <li><a class="btn btn-xs btn-success" target="_blank"
                                                    href="{{ url('supplier_reporting') }}/balance_sheet?customer_id={{ $supplier->id }}">Ledger</a>
                                            </li>

                                            <li> <a class="btn btn-xs btn-warning"
                                                    href="{{ route('suppliers.edit', $supplier->id) }}"><i
                                                        class="glyphicon glyphicon-edit"></i> Edit</a></li>

                                            <li>
                                                <form action="{{ route('suppliers.destroy', $supplier->id) }}"
                                                    method="POST" style="display: inline;">
                                                    <div id="log"></div>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button type="submit" class="btn btn-xs btn-danger"
                                                        onclick="return confirm('Are you sure?')"><i
                                                            class="glyphicon glyphicon-trash"></i> Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <h3 class="text-center alert alert-info">Empty!</h3>
                @endif
            </div>

        </div>
    </div>
    <div aria-hidden="true" aria-labelledby="Upload Excel" role="dialog" data-backdrop="static" id="uploadExcel"
        class="modal fade">
        <div class="modal-dialog" style="width: 90%">
            <div class="modal-content col-md-12">
                <form id="uploadExcelFile" action="{{ url('uploadSuppliersExcel') }}" method="POST"
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
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('.customer_databale').DataTable({
                dom: "Blfrtip",
                responsive: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "order": [
                    [0, 'desc']
                ],
                buttons: [
                    <?= get_print_template('.datatable', 'copy', 'Suppliers', $messageTop, $exportingCol) ?>,
                    <?= get_print_template('.datatable', 'excel', 'Suppliers', $messageTop, $exportingCol) ?>,
                    <?= get_print_template('.datatable', 'print', 'Suppliers', $messageTop, $exportingCol) ?>
                ],
                "fnDrawCallback": function(oSettings) {
                    // init();
                    //   del_init()
                    // <th>ID</th>
                    //                     <th>TYPE</th>
                    //                     <th>PAYMENT TYPE</th>
                    //                     <th>AMOUNT</th>
                    //                     <th>INVOICE ID/ TRANSACTION ID</th>
                    //                     <th>CUSTOMER ID/ SUPPLIER ID</th>
                    //                     <th class="text-right">OPTIONS</th>
                }
            });
        });
    </script>
@endsection
