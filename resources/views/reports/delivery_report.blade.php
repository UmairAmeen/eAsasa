@extends('reporting')
@section('css')
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/searchbuilder/1.0.0/css/searchBuilder.dataTables.min.css">
@endsection
@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/buttons.dataTables.min.css') }}">
    <div class="col-md-12 text-center">
        <h3>Delivery Reporting</h3>
        <h4>Report {{ date_format_app($from) }} to {{ date_format_app($to) }}</h4>
        <a href="{{ url('reports') }}" class="btn btn-primary">Back to All Reports</a>
    </div>
@endsection
@section('content')
    <div class="row showback" style="text-align: center">
        <form method="GET" action="{{ url('reports') }}/delivery_report">
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
                    <label>Default Delivery Status</label>
                    <select name="delivery_status" id="delivery_status" class="form-control">
                        @foreach ($delivery_status_array as $key => $value)
                            <option @if (isset($delivery_status) && $delivery_status == $key) selected @endif value="{{ $key }}">{{ $value }}</option>
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
            <h3>Delivery Details</h3>
            <table class="table datatable">
                <thead>
                    <th>Invoice #</th>
                    <th>Bill #</th>
                    <th>Sale Order #</th>
                    <th>Delivery Challan #</th>
                    <th>Customer</th>
                    <th>Products</th>
                    <th>Products Status</th>
                    <th>Quantity</th>
                    <th>Date</th>
                    <th>Date To Be Delivered</th>
                    <th>Status</th>
                    <th>Balance</th>
                </thead>
                <tbody>
                  @if($total_saleOrders > 0)
                    @foreach ($saleOrders as $sale_order)
                        <tr>
                            <td><a target="_blank" href="{{ route('invoices.show', $sale_order->invoice_id)}}">{{ $sale_order->invoice_id }}</a></td>
                            <td>{{ $sale_order->invoice->bill_number }}</td>
                            <td><a target="_blank" href="{{ route('sale_orders.show', $sale_order->id)}}">{{ $sale_order->id }}</a></td>
                            <td>
                                @foreach ($sale_order->invoice->deliveries as $d)
                                <a target="_blank" href="{{ route('deliverychallans.show', $d->id)}}">{{ $d->id }}</a>
                                &nbsp;
                                @endforeach
                            </td>
                            <td>{{ isset($sale_order->invoice->customer_id) ? $customers[$sale_order->invoice->customer_id] : '' }}</td>
                            <?php 
                              $count = count($sale_order->invoice->orders);
                              $prod = $prod_status = $prod_quantity = [];
                              foreach ($sale_order->invoice->orders as $key => $order){
                                $prod[$key] = $products[$order->product_id];
                                $prod_status[$key] = $order->delivery_status;
                                $prod_quantity[$key] = $order->quantity + 0;
                                $qty_sum += $prod_quantity[$key];
                              }
                            ?>
                            <td>{{  implode(",",$prod) }}</td>
                            <td>{{  implode(",",$prod_status) }}</td>
                            <td>{{  implode(",",$prod_quantity) }}</td>
                            <td>{{ date('d-M-Y', strtotime($sale_order->date)) }}</td>
                            <td>{{ date('d-M-Y', strtotime($sale_order->delivery_date)) }}</td>
                            <td> {{ $sale_order_status[$sale_order->status] }}</td>
                            <td>{{ number_format(getInvoiceBalance($sale_order->invoice_id)) }}</td>
                        </tr>
                        <?php
                            $total_qty = $qty_sum;
                            $total_amount += getInvoiceBalance($sale_order->invoice_id);
                        ?>
                    @endforeach
                  @else
                  <tr>
                    <td colspan="10"><h3>No Record Found</h3></td>
                  </tr>
                  @endif
                </tbody>
                @if($total_saleOrders > 0)
                  <tfoot>
                    <tr>
                        <td colspan="7"><h4> TOTAL BALANCE </h4></td>
                        <td><h4>{{number_format(abs($total_qty))}}</h4></td>
                        <td colspan="3"></td>
                        <td><h4>{{number_format(abs($total_amount))}}</h4></td>
                    </tr>
                      <tr>
                        <td colspan="5"></td>
                        <td>
                            <h4>Total Sale Orders Filtered:</h4>
                        </td>
                        <td>
                            <h4>{{ number_format($total_saleOrders) }}</h4>
                        </td>
                        <td colspan="5"></td>
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
