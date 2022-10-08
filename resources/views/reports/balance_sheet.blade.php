@extends('reporting')
@section('header')
    <title>Ledger Sheet Reporting</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/buttons.dataTables.min.css') }}">
    <div class="col-md-12 text-center">
        <h3>Ledger Sheet Reporting</h3>
        <h4>Report {{ $from ? date('d-M-Y', strtotime($from)) : 'All time' }} -
            {{ $to ? date('d-M-Y', strtotime($to)) : date('d-M-Y') }}</h4>
        <a href="{{ url('reports') }}" class="btn btn-primary">Back to All Reports</a>
    </div>
@endsection
@section('content')
    <div class="row showback">
        @if ($type == 'supplier')
            <form method="GET" action="{{ url('supplier_reporting') }}/balance_sheet">
            @else
                <form method="GET" action="{{ url('reports') }}/balance_sheet">
        @endif
        <div class="form-group">
            <label>From Date</label>
            <input type="date" class="form-control" name="from" value="{{ $from }}">
        </div>
        <div class="form-group">
            <label>To Date</label>
            <input type="date" class="form-control" name="to" value="{{ $to }}">
        </div>
        <div class="form-group">
            @if ($type == 'supplier')
                <label>Supplier</label>
            @else
                <label>Customer</label>
            @endif
            <select class="form-control customer_selector" name="customer_id">
                @if ($type == 'supplier')
                    <option value="0">--All Suppliers--</option>
                @else
                    <option value="0">--All Customers--</option>
                @endif
                @foreach ($customers as $custom)
                    @if ($custom->id == $request->customer_id)
                        <option value="{{ $custom->id }}" selected="selected">{{ $custom->name }}</option>
                    @else
                        <option value="{{ $custom->id }}">{{ $custom->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success">Search Data</button>
        </div>
        </form>
        {!! Chart::display('id-highchartsnya', $chart) !!}
        @if ($request_customer_id > 0)
            <div class="col-md-12">
                {!! Chart::display('id-highcharx', $payment_type_chart_amount_in, ['jquery.js' => false, 'highcharts.js' => false, 'exporting.js' => false, 'format' => 'chart']) !!}
                @if ($credit_debit_percentage)
                    <h3>Balance: {{ number_format($debit_now - $credit_now) }}</h3>
                    <h4>Total Credit: {{ number_format($credit_now) }}</h4>
                    <h4>Total Debit: {{ number_format($debit_now) }}</h4>
                    @if ($request->customer_id)
                        <pre>According to Total Ledger Profile (Lifetime). For Detailed Reporting 
                        @if ($type == 'supplier')
                            <a href="{{ url('supplier_reporting') }}/customer_reporting?customer_id={{ $request->customer_id }}">Click Here</a>
                        @else
                            <a href="{{ url('reports') }}/customer_reporting?customer_id={{ $request->customer_id }}">Click Here</a>
                        @endif
                      </pre>
                    @endif
                    {!! Chart::display('id-highchxsaf', $credit_debit_percentage, ['jquery.js' => false, 'highcharts.js' => false, 'exporting.js' => false, 'format' => 'chart']) !!}
                    <br>
                @endif
        @endif
        @if ($request->customer_id)
            <h3>Transaction Details</h3>
            <table class="table datatable">
                <thead>
                    <th>Date</th>
                    @if ($type == 'supplier')
                        <th>Supplier</th>
                    @else
                        <th>Customer</th>
                    @endif
                    <th>Doc#</th>
                    <th>Bill #</th>
                    <th>Type</th>
                    <th>Transaction</th>
                    <th>Description</th>
                    <th>Added By</th>
                    <th>Edited By</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Balance</th>
                </thead>
                <tbody>
                    @if ($request->customer_id > 0)
                        <?php $balance = $opening_balance; ?>
                        <tr>
                            <td></td><td></td><td></td><td></td><td></td>
                            <td>Opening Balance</td>
                            <td></td>
                            <th></th>
                            <th></th>
                            {{-- <td>{{ $request->customer_id }}</td> --}}
                            @if ($opening_balance > 0)
                                <td>{{ round(abs($opening_balance),2) }}</td>
                                <td></td>
                            @else
                                <td></td>
                                <td>{{ round(abs($opening_balance), 2) }}</td>
                            @endif
                            <td>{{ amount_cdr($balance) }}</td>
                        </tr>
                    @endif
                    @foreach ($transaction as $trans)
                        <?php
                        if ($trans->type == 'in') {
                            $balance -= $trans->amount;
                        } else {
                            $balance += $trans->amount;
                        }
                        $tran_details = $trans->transaction_id;
                        $tran_details .= $trans->bank_detail ? ' | Bank: ' . $trans->bank_detail->name : '';
                        $tran_details .= strtotime($trans->release_date) > 0 ? ' | Release Date: ' . date_format_app($trans->release_date) : '';
                        ?>
                        <tr>
                            <td>{{ date('d-M-Y', strtotime($trans->date)) }}</td>
                            @if ($type == 'customer')
                                <td>{{ $trans->customer ? $trans->customer->name : '' }}</td>
                            @else
                                <td>{{ $trans->supplier ? $trans->supplier->name : '' }}</td>
                            @endif
                            @if ($trans->invoice)
                                <td>
                                    <a target="_blank" href="{{ url('invoices') }}/{{ $trans->invoice->id }}">{{ $trans->invoice_id }}</a>
                                </td>
                                <td>
                                    <a target="_blank" href="{{ url('invoices') }}/{{ $trans->invoice->id }}">{{ $trans->invoice->bill_number }}</a>
                                </td>
                            @else
                                <td>
                                    <a target="_blank" href="{{ url('transactions') }}/{{ $trans->id }}">{{ $trans->id }}</a>
                                </td>
                                <td></td>
                            @endif
                            @if ($trans->invoice)
                                <td>{{ ($trans->invoice->total < 0 )?"Return Purchase ":studly_case($trans->invoice->type) }} Invoice</td>
                            @else
                                <td>{{ $trans->payment_type }}</td>
                            @endif
                            <td>{{ $tran_details }}</td>
                            <td>{{ $trans->description }}</td>
                            <th>{{ empty($users[$trans->added_by]) ? "-" : $users[$trans->added_by] }}</th>
                            <th>{{ empty($users[$trans->edited_by]) ? "-" : $users[$trans->edited_by] }}</th>
                            <td>{{ $trans->type == 'in' ? formating_price($trans->amount) : '' }}</td>
                            <td>{{ $trans->type == 'in' ? '' : formating_price($trans->amount) }}</td>
                            <td>{{ amount_cdr($balance) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>
                            <h4>BALANCE</h4>
                        </td>
                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                        <td>
                            <h4>{{ amount_cdr($balance) }}</h4>
                        </td>
                    </tr>
                </tfoot>
            </table>
        @else
            <h3>Transaction Details</h3>
            <table class="table datatable">
                <thead>
                    <th>ID</th>
                    @if ($type == 'supplier')
                        <th>Supplier</th>
                    @else
                        <th>Customer</th>
                    @endif
                    <td>Opening Balance</td>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Balance</th>
                </thead>
                <tbody>
                @php($topen_balance = $tcredit = $tdebit = $total = $balance = 0)
                @foreach ($transaction as $trans)
                <tr>
                    <td>{{$trans->id}}</td>
                    @php
                        $opening_balance= abs($trans->open_credit) - abs($trans->open_debit);
                        $balance        = $opening_balance + $trans->credit - $trans->debit;
                        $topen_balance  += $opening_balance;
                        $tcredit        += $trans->credit;
                        $tdebit         += $trans->debit;
                    @endphp
                    <td>{{$persons[$trans->id]}}</td>
                    <td>{{ amount_cdr($opening_balance) }}</td>
                    <td>{{$trans->debit}}</td>
                    <td>{{$trans->credit}}</td>
                    <td>{{ amount_cdr($balance) }}</td>
                </tr>
                @php($total += $balance)
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Balance</th>
                        <th>{{amount_cdr($topen_balance)}}</th>
                        <th>{{$tdebit}}</th>
                        <th>{{$tcredit}}</th>
                        <th>{{amount_cdr($total)}}</th>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
    </div>
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
        const company = "{{ session()->get('settings.profile.company') }}";
        const phone = "{{ session()->get('settings.profile.phone') }}";
        const address = "{{ session()->get('settings.profile.address') }}";
        $(document).ready(function(e) {
            $(".table").show();
            const brandingHtml = "<h1>" + company + "</h1><address><strong>" + address + "<br><abbr>Phone:</abbr>" +
                phone + "</address>";
            const brandingPdf = company + "\n" + address + "\nPhone: " + phone;
            var messageTopContentName = "{{ isset($trans) && $trans->customer ? $trans->customer->name : '' }}";
            var messageTopContentPhone =
                "{{ isset($trans) && $trans->customer ? $trans->customer->phone : '' }}";
            var pdfNewLine = "\n";
            var printNewLine = "<br>";
            var messageTopContent =
                'Report date: {{ $from ? date('d-M-Y', strtotime($from)) : 'All time' }} to {{ $to ? date('d-M-Y', strtotime($to)) : date('d-M-Y') }}';
            var messageBottomContent = "\nReports by  eAsasa ( 0345 4777487)";
            $(".datatable").DataTable({
                "deferRender": true,
                dom: 'Blfrtip',
                "ordering": true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                @if ($request->customer_id)
                stateSave: true,
                "stateLoadParams": function (settings, data) {
                    data.search.search = "";
                    data.length = 10;
                    data.start = 0;
                    data.order = [];
                },
                columnDefs: [
                    { width: 200, targets: 6}
                ],
                @endif
                buttons: [
                    'copy', {
                        extend: 'excel',
                        title: "{{ isset($trans) && $trans->customer ? $trans->customer->name : '' }} Ledger Sheet Overview",
                        messageTop: messageTopContentName + pdfNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }, {
                        extend: 'pdf',
                        title: "{{ isset($trans) && $trans->customer ? $trans->customer->name : '' }} Ledger Sheet Overview",
                        messageTop: brandingPdf + pdfNewLine + pdfNewLine + messageTopContentName +
                            pdfNewLine + messageTopContentPhone + pdfNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }, {
                        extend: 'print',
                        title: "{{ isset($trans) && $trans->customer ? $trans->customer->name : '' }} Ledger Sheet Overview",
                        customize: function(win) {
                            $(win.document.body).css('font-size', '10pt');
                            $(win.document.body).find('table').addClass('compact').css('font-size',
                                'inherit');
                        },
                        messageTop: brandingHtml + printNewLine + printNewLine + messageTopContentName +
                            printNewLine + messageTopContentPhone + printNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }, 'colvis'
                ]
            });
            $(".customer_selector").select2();
        });
    </script>
@endsection
