@extends('reporting')
@php($to = empty($to) ? date('d-m-Y', strtotime('now')) : date('d-m-Y', strtotime($to)))
@section('header')
    <title>Balance Sheet Reporting</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/buttons.dataTables.min.css') }}">
    <div class="col-md-12 text-center">
        <h3>All Stock In/Out</h3>
        <h4>Report:{{($from)?(date('d-M-Y',strtotime($from))):date('1-M-Y')}}  - {{($to)?(date('d-M-Y',strtotime($to))):date('d-M-Y')}}</h4>
        <a href="{{ url('reports') }}" class="btn btn-primary">Back to All Reports</a>
    </div>
@endsection
@section('content')
    <div class="row showback">
        <form method="GET" action="{{ url('reports') }}/stock_in_out_view">
            <div class="form-group">
				<label>From Date</label>
				<input type="date" class="form-control" name="from" value="{{$request->from}}">
			</div>
            <div class="form-group">
                <label>To Date</label>
                <input type="date" class="form-control" name="to" value="{{$request->to}}">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success">Search Data</button>
            </div>
        </form>
        <div class="col-md-12">
            <h3>All Stock Details</h3>
            <table class="table datatable">
                <thead>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>In</th>
                    <th>Out</th>
                    <th>Balance</th>
                </thead>
                <tbody>
                    @php
                    $totalIn = $totalOut = $totalbalance = 0;
                    $size_enable = strpos(session()->get('settings.products.optional_items'), 'size');
                    @endphp
                    @foreach ($stock_in_out as $stock)
                        <tr>
                            <td>{{ $stock->id }}</td>
                            <td>{{ $stock->name . ' ' . $stock->itemcode . ' ' . ($size_enable!==false ? $stock->size : '') }}</td>
                            <td>{{ $product_categories[$stock->category_id]}}</td>
                            <td>{{ $stock->brand }}</td>
                            <td>{{ $stock->stockIn }}</td>
                            <td>{{ $stock->stockOut }}</td>
                            <td>{{ $stock->stockIn - $stock->stockOut }}</td>
                        </tr>
                        @php
                            $totalIn += $stock->stockIn;
                            $totalOut += $stock->stockOut;
                            $totalbalance += $stock->stockIn - $stock->stockOut;
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <th colspan="4">Total</th>
                    <th>{{ $totalIn }}</th>
                    <th>{{ $totalOut }}</th>
                    <th>{{ $totalbalance }}</th>
                </tfoot>
            </table>
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('/assets/js/jquery.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/datatable/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/datatable/dataTables.bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/datatable/buttons.colVis.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/datatable/dataTables.buttons.js') }}?v=1"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/datatable/buttons.flash.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/datatable/buttons.print.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/datatable/buttons.html5.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatables/jszip.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatables/pdfmake.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/datatables/vfs_fonts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
    const company = "{{session()->get('settings.profile.company')}}";
    const phone = "{{session()->get('settings.profile.phone')}}";
    const address = "{{session()->get('settings.profile.address')}}";
        $(document).ready(function(e) {
            const brandingHtml = "<h1>" + company + "</h1><address><strong>" + address + "<br><abbr>Phone:</abbr>" + phone + "</address>";
            const brandingPdf = company + "\n" + address + "\nPhone: " + phone;
            var messageTopContentName = "";
            var messageTopContentPhone = "";
            var pdfNewLine = "\n";
            var printNewLine = "<br>";
            var messageTopContent = 'Stock In/Out Report date: {{ date_format_app($from) }} to  {{ date_format_app($to) }}';
            var messageBottomContent = "\nReports by eAsasa ( 0345 4777487)";
            $(".datatable").DataTable({
                "deferRender": true,
                dom: 'Bfrtip',
                stateSave: true,
                "stateLoadParams": function (settings, data) {
                    data.search.search = "";
                    data.length = 10;
                    data.start = 0;
                    data.order = [];
                },
                buttons: [
                    'copy', {
                        extend: 'excel',
                        title: "Stock In/Out Report",
                        messageTop: messageTopContentName + pdfNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {columns: ':visible'}
                    }, {
                        extend: 'pdf',
                        title: "Stock In/Out Report",
                        messageTop: brandingPdf + pdfNewLine + pdfNewLine + messageTopContentName + pdfNewLine + messageTopContentPhone + pdfNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {columns: ':visible' }
                    }, {
                        extend: 'print',
                        title: "Stock In/Out Report",
                        messageTop: brandingHtml + printNewLine + printNewLine + messageTopContentName + printNewLine + messageTopContentPhone + printNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {columns: ':visible'}
                    }, 'colvis'
                ]
            });
        });
    </script>
@endsection
