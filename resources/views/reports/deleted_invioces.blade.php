@extends('reporting')
@section('css')
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/searchbuilder/1.0.0/css/searchBuilder.dataTables.min.css">
@endsection
@section('header')
    <title>Deleted Invoices Reporting</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/buttons.dataTables.min.css') }}">
    <div class="col-md-12 text-center">
        <h3>Deleted Invoices Reporting</h3>
        <h4>Report {{ date_format_app($from) }} to {{ date_format_app($to) }}</h4>
        <a href="{{ url('reports') }}" class="btn btn-primary">Back to All Reports</a>
        <br>
    </div>
@endsection
@section('content')
    <div class="row showback">

        <form method="GET" action="{{ url('reports') }}/deleted_sale_orders">

            <div class="col-md-6">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" name="from" value="{{ $from ?: date('Y-m-1') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" name="to" value="{{ $to ?: date('Y-m-t') }}">
                </div>
            </div>

            <center>
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Search Data</button>
                </div>
            </center>

        </form>
        <div class="col-md-12">


            <h3>Deleted Invoices Details</h3>
            <table class="table datatable">
                <thead>
                    <th>Sale Order/Invoice #</th>
                    <th>Bill #</th>
                    <th>Booking Date</th>
                    <th>Type</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Related To</th>
                    <th>Quantity</th>
                    <th>Total Worth</th>
                    <th>Sale Person</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Delivery Date</th>
                    <th>Deleted Date</th>
                </thead>
                <tbody>

                    <?php $balance = 0; ?>
                    @foreach ($sale_orders as $key => $trans)
                        <?php $balance += $trans->total; ?>
                        <tr>
                            <td>{{ $trans->id }}</td>
                            <td>{{ $trans->bill_number }}</td>
                            <td>{{ date('d-M-Y', strtotime($trans->date)) }}</td>
                            <td>{{ str_replace('_', ' ', strtoupper($trans->type)) }}</td>
                            <td>{{ $trans->customer->name }}</td>
                            <td>{{ $trans->customer->phone }}</td>
                            <td>{{ $trans->customer->address }}</td>
                            @php
                                $prods = [];
                                $quantity = [];
                                $notes = [];
                                foreach ($trans->trashed_orders as $key => $value) {
                                    $prods[$key] = $value->product->name;
                                    $quantity[$key] = $value->quantity + 0;
                                    $notes[$key] = $value->notes;
                                }
                                $imploded_p = implode(',', $prods);
                                $imploded_q = implode(',', $quantity);
                                $imploded_n = implode(',', $notes);
                            @endphp
                            <td>{{ $imploded_p }}</td>
                            <td>{{ $imploded_n ?: '-' }}</td>
                            <td>{{ $trans->related_to }}</td>
                            <td>{{ $imploded_q }}</td>
                            <td>{{ $trans->total + 0 }}</td>
                            <td>{{ $trans->sales_person ?: '-' }}</td>
                            <td>{{ $trans->trashed_sale_order->source ?: '-' }}</td>
                            <?php
                            $status_array = ['PENDING', 'ACTIVE', 'null', 'QUOTATION', 'COMPLETED'];
                            ?>
                            <td>{{ $status_array[$trans->trashed_sale_order->status] ?: '-' }}</td>
                            @if ($trans->trashed_sale_order->delivery_date)
                                <td>{{ date('d-M-Y', strtotime($trans->trashed_sale_order->delivery_date)) }}</td>
                            @else
                                <td>-</td>
                            @endif
                            <td>{{ date('d-M-Y h:i:s', strtotime($trans->deleted_at)) }}</td>
                        </tr>
                    @endforeach

                </tbody>

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
        integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js" type="text/javascript">
    </script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.0.0/js/dataTables.searchBuilder.min.js">
    </script>
    <script type="text/javascript" src="{{ asset('/assets/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(e) {
            $.fn.dataTable.moment('DD-MMM-YYYY');
            var brandingHtml = "<h1>{{ getSetting('company') }}</h1>\
                                        <address>\
                                        <strong>{{ getSetting('address') }}<br>\
                                        <abbr>Phone:</abbr>{{ getSetting('phone') }}\
                                        </address>";
            var brandingPdf = "{{ getSetting('company') }}\n\
              {{ getSetting('address') }}\n\
              Phone: {{ getSetting('phone') }}";
            var messageTopContentName = "";
            var messageTopContentPhone = "";
            var pdfNewLine = "\n";
            var printNewLine = "<br>";

            var messageTopContent =
                'Deleted Invoices Report date: {{ date_format_app($from) }} to  {{ date_format_app($to) }}';
            var messageBottomContent = "\nReports by eAsasa ( 0345 4777487)";
            $("tbody tr:odd").css("background-color", "#f2f2f2");
            $("thead").css("background-color", "#adadad");

            $(".datatable").DataTable({
                "deferRender": true,
                dom: 'QBfrtlip',
                "ordering": true,
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
                        title: "Deleted Invoices Report",
                        messageTop: messageTopContentName + pdfNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        title: "Deleted Invoices Report",
                        messageTop: brandingPdf + pdfNewLine + pdfNewLine + messageTopContentName +
                            pdfNewLine + messageTopContentPhone + pdfNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }, {
                        extend: 'print',
                        title: "Deleted Invoices Report",
                        messageTop: brandingHtml + printNewLine + printNewLine + messageTopContentName +
                            printNewLine + messageTopContentPhone + printNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }, 'colvis'
                ]
            });


        });
    </script>
@endsection
