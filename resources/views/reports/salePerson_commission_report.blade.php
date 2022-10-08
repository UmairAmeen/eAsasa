@extends('reporting')
@section('css')
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/searchbuilder/1.0.0/css/searchBuilder.dataTables.min.css">
@endsection
@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/buttons.dataTables.min.css') }}">
    <div class="col-md-12 text-center">
        <h3>Sale Person Commission Reporting</h3>
        <h4>Report {{ date_format_app($from) }} to {{ date_format_app($to) }}</h4>
        <a href="{{ url('reports') }}" class="btn btn-primary">Back to All Reports</a>
    </div>
@endsection
@section('content')
    <div class="row showback" style="text-align: center">
        <form method="GET" action="{{ url('reports') }}/salePerson_commission">
            <div class="col-md-3">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" name="from" value="{{ $from ? $from : date('Y-m-1') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" name="to" value="{{ $to ? $to : date('Y-m-d') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Sale Person</label>
                    <select name="sale_person" id="sale_person" class="form-control">
                        <option value=""> Select </option>
                        @foreach ($sale_persons as $key => $value)
                            <option @if (isset($selected_person) && $selected_person == $value->id) selected @endif value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <center>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Search Data</button>
                    </div>
                </center>
            </div>
        </form>
        <div class="col-md-12">
            <h3>Commission Details</h3>
            <table class="table datatable">
                <thead>
                    <th>Invoice #</th>
                    <th>Bill #</th>
                    <th>Sale Order #</th>
                    <th>Date</th>
                    <th>Sale Person</th>
                    <th>Commission %</th>
                    <th>Total Worth</th>
                    <th>Commission Price</th>
                </thead>
                <tbody>
                  @if(count($sale_orders) > 0)
                    @foreach ($sale_orders as $sale_order)
                        @php
                            $total_worth_price += $sale_order->invoice->total;
                            $commission_price = $sale_order->invoice->total * ($sale_order->saleOrder_person->commission / '100');
                            $total_commission_price += $commission_price;
                        @endphp
                        <tr>
                            <td><a target="_blank" href="{{ route('invoices.show', $sale_order->invoice_id)}}">{{ $sale_order->invoice_id }}</a></td>
                            <td>{{ $sale_order->invoice->bill_number }}</td>
                            <td><a target="_blank" href="{{ route('sale_orders.show', $sale_order->id)}}">{{ $sale_order->id }}</a></td>
                            <td>{{ date('d-M-Y', strtotime($sale_order->date)) }}</td>
                            <td>{{ $sale_order->saleOrder_person->name }}</td>
                            <td>{{ number_format(abs($sale_order->saleOrder_person->commission + 0)) }}</td>
                            <td>{{ number_format(abs($sale_order->invoice->total + 0)) }}</td>
                            <td>{{ number_format(abs($commission_price)) }}</td>
                        </tr>
                    @endforeach
                  @else
                  <tr>
                    <td colspan="10"><h3>No Record Found</h3></td>
                  </tr>
                  @endif
                </tbody>
                @if($sale_orders && $total_worth_price > 0)
                  <tfoot>
                    <tr>
                        <td colspan="6"><h4> TOTAL Commission</h4></td>
                        <td><h4>{{number_format(abs($total_worth_price + 0))}}</h4></td>
                        <td><h4>{{number_format(abs($total_commission_price + 0))}}</h4></td>
                    </tr>
                  </tfoot>
                @endif
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
        const company = "{{ session()->get('settings.profile.company') }}";
        const phone = "{{ session()->get('settings.profile.phone') }}";
        const address = "{{ session()->get('settings.profile.address') }}";
        $(document).ready(function(e) {
            $('#sale_person').select2();
            $.fn.dataTable.moment('DD-MMM-YYYY');
            const brandingHtml = "<h1>" + company + "</h1><address><strong>" + address + "<br><abbr>Phone:</abbr>" +
                phone + "</address>";
            const brandingPdf = company + "\n" + address + "\nPhone: " + phone;
            var messageTopContentName = "{{ isset($trans) && $trans->customer ? $trans->customer->name : '' }}";
            var messageTopContentPhone =
                "{{ isset($trans) && $trans->customer ? $trans->customer->phone : '' }}";
            var pdfNewLine = "\n";
            var printNewLine = "<br>";
            // if (document.getElementById('ce').innerHTML) {
            //     var rr = document.getElementById('ce').innerHTML
            // }
            // if (rr == 'Select Expense') {
            //     rr = "Expense Report";
            // }
            var messageTopContent =
                'Report date: {{ date_format_app($from) }} to  {{ date_format_app($to) }}';
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
                        title: "Delivery Report",
                        messageTop: messageTopContentName + pdfNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }, {
                        extend: 'pdf',
                        title: "Delivery Report",
                        messageTop: brandingPdf + pdfNewLine + pdfNewLine + messageTopContentName +
                            pdfNewLine + messageTopContentPhone + pdfNewLine + messageTopContent,
                        messageBottom: messageBottomContent,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }, {
                        extend: 'print',
                        title: "Delivery Report",
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
