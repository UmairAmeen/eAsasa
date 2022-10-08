@extends('layout')
@section('css')
    <script src="{{ asset('assets/js/chart-master/Chart.js') }}"></script>
@endsection
@section('content')
    @if (package('report'))
        <div class="row mt">
            <!-- SERVER STATUS PANELS -->
            <div class="col-md-3 col-sm-4 mb">
                <div class="white-panel pn donut-chart">
                    <div class="white-header">
                        <h5>Average Available Stock above notify level</h5>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 goleft">
                            <p><i class="fa fa-database"></i>Total {{ $total_product }}</p>
                            <p><i class="fa fa-database"></i>Out of stock {{ $notice_stock }}</p>
                        </div>
                    </div>
                    <canvas id="serverstatus01" height="120" width="120"></canvas>
                    <script>
                        var doughnutData = [{
                            value: {{ $total_product - $notice_stock }},
                            color: "#8BC34A"
                        }, {
                            value: {{ $notice_stock }},
                            color: "#fdfdfd"
                        }];
                        var myDoughnut = new Chart(document.getElementById("serverstatus01").getContext("2d")).Doughnut(doughnutData);
                    </script>
                </div>
                <!--/grey-panel -->
            </div><!-- /col-md-4-->
            <!-- TWITTER PANEL -->
            <div class="col-md-3 mb">
                <a href="{{ url('appointment_calendars') }}">
                    <div class="darkblue-panel pn">
                        <div class="darkblue-header">
                            <h5>Pending Cheque Clearance Today</h5>
                        </div>
                        <div class="chart mt">
                            <h1>{{ $chque }}</h1>
                        </div>
                        <p style="color: white" class="mt"><b>{{ number_format($chque_worth) }}</b><br />Total
                            Worth</p>
                    </div><!-- /darkblue panel -->
                </a>
            </div><!-- /col-md-4 -->
            <div class="col-md-3 col-sm-4 mb">
                <a href="{{ url('appointment_calendars') }}">
                    <!-- REVENUE PANEL -->
                    <div class="green-panel pn">
                        <div class="green-header">
                            <h5>Pending Contact Today</h5>
                        </div>
                        <div class="chart mt">
                            <h1>{{ $customer_call_count }}</h1>
                        </div>
                        <!-- <p class="mt"><b>Rs. 5840</b><br/>Annual Income</p> -->
                    </div>
                </a>
            </div><!-- /col-md-4 -->
            {{-- <figure class="highcharts-figure">
                <div class="col-md-3 col-sm-4 mb" style="font-size: 1.4em;padding:4px;text-decoration:none" id="container">
                </div>
            </figure> --}}

            <div class="col-md-3 col-sm-4 mb" style="font-size: 1.4em;padding:4px;text-decoration:none">
                <!-- REVENUE PANEL -->
                <div class="white-panel pn">
                    <a href="{{ url('transactions') }}">
                        <div class="col-xs-12">
                            @if (!is_admin())
                                <h1 style="color: red">
                                    <i class="fa fa-ban"></i>&nbsp; Access Denied
                                </h1>
                            @elseif (!empty($finStats))
                                <div style="font-size: 1.4em;padding:4px;text-decoration:none" id="finStats">
                                </div>
                            @else
                                <div>
                                    <h1 style="color: red">
                                        &nbsp; No Transactions recorded yet
                                    </h1>
                                </div>
                            @endif
                        </div>
                </div>
                </a>
            </div><!-- /col-xs-4 -->
        </div><!-- /row -->
    @endif
    <div class="row">
        <div class="col-lg-9 main-chart">
            <div class="row mtbox">
                <div class="col-md-2 col-sm-2 col-md-offset-1 box0">
                    <a href="/products">
                        <div class="box1">
                            <span class="fa fa-archive"></span>
                            <h4>Products</h4>
                        </div>
                    </a>
                    <p>Manage your Product</p>
                </div>
                <div class="col-md-2 col-sm-2 box0">
                    <a href="/warehouses">
                        <div class="box1">
                            <span class="fa fa-warehouse"></span>
                            <h4>Warehouse</h4>
                        </div>
                    </a>
                    <p>Manage Your Warehouses</p>
                </div>
                <div class="col-md-2 col-sm-2 box0">
                    <a href="/pos/direct_barcode">
                        <div class="box1">
                            <span class="fa fa-plus-circle"></span>
                            <h4> Advance POS </h4>
                        </div>
                    </a>
                    <p> Sale with Advance Layout </p>
                </div>
                @if (package('sales'))
                    <div class="col-md-2 col-sm-2 box0">
                        <a href="/sales">
                            <div class="box1">
                                <span class="fa fa-receipt"></span>
                                <h4>Sales</h4>
                            </div>
                        </a>
                        <p>Manage Sales and Invoices</p>
                    </div>
                @endif
                @if (package('sales') && is_allowed('sale-list'))
                    <div class="col-md-2 col-sm-2 box0">
                        <a href="{{ url('sale_orders') }}">
                            <div class="box1">
                                <span class="fas fa-truck-loading"></span>
                                <h4>Sales Order</h4>
                            </div>
                        </a>
                        <p>Manage Sales Order</p>
                    </div>
                @endif
                @if(package('deliverychallans'))
                    <div class="col-md-2 col-sm-2 box0">
                        <a href="{{url('deliverychallans')}}">
                            <div class="box1">
                                <span class="fa fa-clipboard"></span>
                                <h4>Delivery Challans</h4>
                            </div>
                        </a>
                        <p>Manage Delivery Challans</p>
                    </div>
                @endif
                @if (package('purchase'))
                    <div class="col-md-2 col-sm-2 box0">
                        <a href="/purchases">
                            <div class="box1">
                                <span class="fa fa-credit-card"></span>
                                <h4>Purchase</h4>
                            </div>
                        </a>
                        <p>Manage Purchases</p>
                    </div>
                @endif
                @if (package('transaction') && is_allowed('transaction-list'))
                    <div class="col-md-2 col-sm-2 box0">
                        <a href="{{ url('transactions') }}">
                            <div class="box1">
                                <span class="fa fa-database"></span>
                                <h4>Transactions</h4>
                            </div>
                        </a>
                        <p>Manage Transactions</p>
                    </div>
                @endif
                @if (package('stock_adjustments'))
                    <div class="col-md-2 col-sm-2 box0">
                        <a href="/stock">
                            <div class="box1">
                                <span class="fa fa-bars"></span>
                                <h4>Stocks</h4>
                            </div>
                        </a>
                        <p>Manage Stocks</p>
                    </div>
                @endif
                <!-- </div> -->
                <!-- <div class="row mt"> -->
                @if (package('customer'))
                    <div class="col-md-2 col-sm-2  col-md-offset-1 box0">
                        <a href="/customers">
                            <div class="box1">
                                <span class="fa fa-user"></span>
                                <h4>Customer</h4>
                            </div>
                        </a>
                        <p>Manage your customers.</p>
                    </div>
                @endif
                @if (package('supplier'))
                    <div class="col-md-2 col-sm-2 box0">
                        <a href="/suppliers">
                            <div class="box1">
                                <span class="fa fa-truck"></span>
                                <h4>Supplier</h4>
                            </div>
                        </a>
                        <p>Manage Suppliers</p>
                    </div>
                @endif
                @if (package('settings'))
                    <div class="col-md-2 col-sm-2 box0">
                        <a href="/settings">
                            <div class="box1">
                                <span class="fas fa-sliders-h"></span>
                                <h4>Setting</h4>
                            </div>
                        </a>
                        <p>Setup your application.</p>
                    </div>
                @endif
            </div>
            <!--/row mt-->
        </div> <!-- /col-lg-9 END SECTION MIDDLE -->
        <!-- ****************************************************************************************
                RIGHT SIDEBAR CONTENT
        ********************************************************************************************* -->
        <div class="col-lg-3 ds">
            <div id="scrollDiv" style="padding-left:5px;height:400px;overflow:scroll;">
                <!--COMPLETED ACTIONS DONUTS CHART-->
                <h3>ALERTS & DELIVERIES</h3>
                @if ($notice_stock <= 0)
                    <div class="desc">
                        <div class="thumb">
                            <span class="badge bg-theme"><i class="fa fa-clock-o"></i></span>
                        </div>
                        <div class="details">No Alert<br /></div>
                    </div>
                @else
                    <!-- First Action -->
                    <div class="desc">
                        <div class="thumb">
                            <span class="badge bg-theme04"><i class="fa fa-2x fa-exclamation-triangle"></i></span>
                        </div>
                        <div class="details">
                            Last Updated: <muted> {{ date(session()->get('settings.misc.date_format')) }} </muted>
                            <br />
                            You have <a href="/products_out_of_stock" style="color: #f44336">{{ $notice_stock }}
                                products</a>
                            out of stock<br />
                        </div>
                    </div>
                @endif
                @php($days = Session::get('license_info')['days_left_in_expiry'])
                @if ($days < 5)
                    <div class="desc">
                        <div class="thumb">
                            <span class="badge bg-theme04"><i class="fa fa-2x fa-bell"></i></span>
                        </div>
                        <div class="details">
                            Your License Key is Expiring in {{ $days }} days, Contact at <a
                                href="tel:+923454777487">+92
                                345 4777 487</a>
                        </div>
                    </div>
                @endif
                <?php
                $pendingSales = getPendingOrders();
                $countPendingSales = count($pendingSales);
                ?>
                {{-- used for border in scroller=> border:1px solid black; --}}
                @if ($pendingSales)
                    <div>
                        @foreach ($pendingSales as $pendingSale)
                            <div class="pendingSales desc">
                                <div class="thumb">
                                    <span class="badge bg-theme04"><i class="fa fa-2x fa-clock"></i></span>
                                </div>
                                <div class="details">
                                    <a href="{{ url('/sale_orders/' . $pendingSale->id) }}"
                                        style="color: #4caf50;font-weight: bold;">Sale Order # {{ $pendingSale->id }}</a>
                                    was made on
                                    <b>{{ $pendingSale->date }}</b>, is
                                    <b>{{ $pendingSale->status == 0 ? 'PENDING' : 'ACTIVE' }}</b> and to be DELIVERED on
                                    <b>{{ $pendingSale->delivery_date }}</b>.</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div><!-- /col-lg-3 -->
    </div>
    <!--/row -->
@endsection
@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/js/gritter/js/jquery.gritter.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/gritter-conf.js') }}"></script>
    <!--script for this page-->
    <script src="{{ asset('assets/js/jquery.sparkline.js') }}"></script>
    <script src="{{ asset('assets/js/sparkline-chart.js') }}"></script>
    <script src="{{ asset('assets/js/zabuto_calendar.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.scrollTo.min.js') }}"></script>

    {{-- <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/dumbbell.js"></script>
    <script defer src="https://code.highcharts.com/modules/exporting.js"></script>
    <script defer src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script> --}}


    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

    <!--common script for all pages-->
    {{-- <script type="text/javascript" src="{{ asset('assets/js/gritter/js/jquery.gritter.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/gritter-conf.js') }}"></script> --}}
    <!--script for this page-->
    {{-- <script src="{{ asset('assets/js/sparkline-chart.js') }}"></script>
<script src="{{ asset('assets/js/zabuto_calendar.js') }}"></script> --}}
    <!-- <script src="http://cdn.oesmith.co.uk/morris-0.4.3.min.js"></script>
                                                                                <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script> -->
    <script type="application/javascript">
        const finData = JSON.parse("{!! addcslashes($finData, '"') !!}");

        console.log(finData);
        $(document).ready(function() {
            $('#scrollDiv').scroll();
            $("#date-popover").popover({
                html: true,
                trigger: "manual"
            });
            $("#date-popover").hide();
            $("#date-popover").click(function(e) {
                $(this).hide();
            });
            $("#my-calendar").zabuto_calendar({
                action: function() {
                    return myDateFunction(this.id, false);
                },
                action_nav: function() {
                    return myNavFunction(this.id);
                },
                ajax: {
                    url: "show_data.php?action=1",
                    modal: true
                },
                legend: [{
                    type: "text",
                    label: "Special event",
                    badge: "00"
                }, {
                    type: "block",
                    label: "Regular event",
                }]
            });
        });

        Highcharts.chart('finStats', {
            chart: {
                type: 'column',
                height: 300,
                width: 300,
            },
            title: {
                text: 'Financial Statistics'
            },
            xAxis: {
                categories: [
                    'Debit',
                    'Credit',
                    'Expense',
                    'Balance',
                ],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Amount in Rs'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} RS</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: finData
        });
    </script>
@endsection
