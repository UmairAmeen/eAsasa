@extends('reporting')
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchbuilder/1.0.0/css/searchBuilder.dataTables.min.css">
@endsection
@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/buttons.dataTables.min.css') }}">
    <div class="col-md-12 text-center">
        <h3>Expense Reporting</h3>
        <h4>Report {{ date_format_app($from) }} to {{ date_format_app($to) }}</h4>
        <a href="{{ url('reports') }}" class="btn btn-primary">Back to All Reports</a>
    </div>
@endsection
@section('content')
    <div class="row showback"   style="text-align: center">
        <form method="GET" action="{{ url('reports') }}/expense">
            <div class="col-md-3">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" name="from" value="{{ $from ?$from: date('Y-m-1') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" name="to" value="{{ $to ?$to: date('Y-m-d') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Default Date Ranges</label>
                    <select name="select_date" id="select_date" class="form-control">
                        <option value="0">Select</option>
                        <option value="1">Today</option>
                        <option value="2">Current Week</option>
                        <option value="3">Current Month</option>
                    </select>
                </div>
            </div>
            <input type="hidden" class="form-control" name="today" value="{{ date('Y-m-d') }}">
            <input type="hidden" class="form-control" name="start_of_week" value="{{date('Y-m-d', strtotime( 'monday this week')) }}">
            <input type="hidden" class="form-control" name="end_of_week" value="{{date('Y-m-d', strtotime( 'friday this week')) }}">
            <input type="hidden" class="form-control" name="start_of_month" value="{{date('Y-m-1') }}">
            <input type="hidden" class="form-control" name="end_of_month" value="{{date('Y-m-t') }}">
            <div class="col-lg-12">
                <div class="form-group">
                    <label>Expense Heads</label>
                    <select class="form-control" name="expense_head" id="expense_head">
                        @if ($current_expense)
                            <option id="ce" value="{{ $current_expense->id }}">{{ $current_expense->name }}</option>
                        @else
                            <option value="select" id="ce">Select Expense</option>
                        @endif
                          <option value="all" >All</option>
                        @foreach ($exp as $expense)
                            @if ($expense->id == $current_expense->id)
                                @continue
                            @else
                                <option value="{{ $expense->id }}">{{ $expense->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <center>
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Search Data</button>
                </div>
            </center>
        </form>
        <div class="col-md-12">
            <h3>Expense Details</h3>
            <table class="table datatable">
                <thead>
                    <th>Date</th>
                    <th>User</th>
                    <th>Doc#</th>
                    <th>Expense Head</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Total</th>
                </thead>
                <tbody>
                    <?php $balance = [];
                    $trans_details = $trans->transaction_id . ' ' . $trans->release_date . "\n" . $trans->bank; ?>
                    @foreach ($total_sale_invoices as $trans)
                        <?php $balance[$trans->added_user->name ?: 'Other'] += $trans->amount; ?>
                        <tr>
                            <td>{{ date('d-M-Y', strtotime($trans->date)) }}</td>
                            <td>{{ isset($trans->added_user) ? $trans->added_user->name : '' }}</td>
                            <td><a target="_blank" href="{{ url('transactions') }}/{{ $trans->id }}">{{ $trans->id }}</a></td>
                            <td>{{ getExpenseName($trans->expense_head) }}</td>
                            <td> {{ $trans->description }} - {{ $trans_details }}</td>
                            <td>{{ number_format($trans->amount) }}</td>
                            <td>{{ number_format(array_sum($balance)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <h4>Total</h4>
                        </td>
                        <td>
                            <h4>{{ number_format(array_sum($balance)) }}</h4>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <h5>Cashier Details</h5>
            <table class="table">
                <thead>
                    <th>Name</th>
                    <th>Total Expense</th>
                </thead>
                <tbody>
                    @foreach ($balance as $key => $blnc)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ number_format($blnc) }}</td>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.0.0/js/dataTables.searchBuilder.min.js"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        const company = "{{session()->get('settings.profile.company')}}";
        const phone = "{{session()->get('settings.profile.phone')}}";
        const address = "{{session()->get('settings.profile.address')}}";
        $(document).ready(function(e) {
            $.fn.dataTable.moment( 'DD-MMM-YYYY' );
            const brandingHtml = "<h1>" + company + "</h1><address><strong>" + address + "<br><abbr>Phone:</abbr>" + phone + "</address>";
            const brandingPdf = company + "\n" + address + "\nPhone: " + phone;
            var messageTopContentName = "{{ isset($trans) && $trans->customer ? $trans->customer->name : '' }}";
            var messageTopContentPhone = "{{ isset($trans) && $trans->customer ? $trans->customer->phone : '' }}";
            var pdfNewLine = "\n";
            var printNewLine = "<br>";
            if(document.getElementById('ce').innerHTML) {
                var rr = document.getElementById('ce').innerHTML
            }
            if(rr == 'Select Expense') {
                rr ="Expense Report";
            }
            var messageTopContent = 'Report date: {{ date_format_app($from) }} to  {{ date_format_app($to) }}';
            var messageBottomContent = "\nReports by eAsasa ( 0345 4777487)";
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
                        title: "Day Discount & Cashier Report",
                        messageTop: messageTopContentName + pdfNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {columns: ':visible'}
                    }, {
                        extend: 'pdf',
                        title: "Day Discount & Cashier Report",
                        messageTop: brandingPdf + pdfNewLine + pdfNewLine + messageTopContentName +
                        pdfNewLine + messageTopContentPhone + pdfNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {columns: ':visible'}
                    }, {
                        extend: 'print',
                        title: rr,
                        messageTop: brandingHtml + printNewLine + printNewLine + messageTopContentName +
                        printNewLine + messageTopContentPhone + printNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {columns: ':visible'}
                    }, 'colvis'
                ]
            });
        });
    </script>
@endsection