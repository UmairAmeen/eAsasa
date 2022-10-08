@extends('layout')
@section('header')
@endsection
@section('content')
<center class="no-print"><button class="btn btn-lg printMe no-print">Print Invoice</button></center>
    <section class="wrapper">
        <div class="col-lg-12 mt">
            <div class="row content-panel">
                <div class="col-lg-10 col-lg-offset-1">
                    <div class="invoice-body">
                        <div class="pull-left"> 
                            <h1>{{session()->get('settings.profile.company')}}</h1>
                                <address>
                                    <strong>{{session()->get('settings.profile.address')}}<br>
                                    <abbr>Phone:</abbr>{{session()->get('settings.profile.phone')}}
                                </address>
                        </div><!-- /pull-left -->
                        <div class="pull-right">
                            <h2>{{studly_case($invoice->type)}} Invoice</h2>
                        </div><!--/pull-right -->
                        <br>
                        <br>
                        <div class="clearfix"></div>
                        <br>
                        <div class="row">
                            <div class="col-md-9">
                                <h4>{{$invoice->customer->name}}</h4>
                                <abbr>P:</abbr> {{$invoice->customer->phone}}
                                </address>
                            </div><!--/col-md-9 -->
                            <div class="col-md-3"><br>
                                <div>
                                    <div class="pull-left"> INVOICE NO : </div>
                                    <div class="pull-right"> {{$invoice->id}} </div>
                                    <div class="clearfix"></div>
                                </div>
                            <div><!-- /col-md-3 -->
                            <div class="pull-left"> ORDER DATE : </div>
                            <div class="pull-right">{{app_date_format($invoice->sale_order->date)}} </div>
                            <div class="clearfix"></div>
                        </div><!--/row -->
                        <br>
                    </div><!-- /invoice-body -->
                </div><!--/col-lg-10 -->
                {!! session()->get('settings.misc.invoice_header') !!}
                <table class="table">
                    <thead>
                        <tr>
                        <th class="text-left">Name</th>
                        <th style="width:140px" class="text-right">UNIT PRICE</th>
                        <th style="width:60px" class="text-center">QTY</th>
                        <th style="width:90px" class="text-right">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $grandTotal = 0; ?>
                    @foreach($fixing_orders as $key => $value)
                    <?php $grandTotal += $value['sale_price']*$value['quantity']; ?>
                        <tr>
                            <td>{{$value['name']}}</td>
                            <td class="text-right">{{$value['sale_price']}}</td>
                            <td class="text-center">{{$value['quantity']}}</td>
                            <td class="text-right">{{$value['sale_price']*$value['quantity']}}</td>
                        </tr>
                    @endforeach
                        <tr>
                            <td colspan="2" rowspan="5" >
                                {!! session()->get('settings.misc.invoice_footer') !!} 
                                <br><br>
                                {{$invoice->description}}
                            </td>
                            <td class="text-right"><strong>Subtotal</strong></td>
                            <td class="text-right">{{number_format($grandTotal)}}</td>
                            </tr>
                            <tr>
                            <td class="text-right no-border"><strong>Packing</strong></td>
                            <td class="text-right">{{number_format($invoice->shipping)}}</td>
                        </tr>
                        <tr>
                            <!-- <td class="text-right no-border"></td> -->
                            <td colspan="2" class="text-right"><center><div class="well well-small green">Total: <strong>{{number_format($grandTotal + $invoice->shipping)}}</strong></div></center></td>
                        </tr>
                        <tr>    
                            <td colspan="2" class="text-right">
                                <center><div class="well well-small green"> Total Due :<strong> 
                                @if($invoice->customer)
                                    {{number_format(getCustomerBalance($invoice->customer_id))}}
                                @else
                                    {{number_format(getSupplierBalance($invoice->supplier_id))}}
                                @endif
                                </strong></div></center>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <br>
            </div><!--/col-lg-12 mt -->
                <!--
                    <div class="well well-small green">
                        <div class="pull-left"> Total Due : </div>
                        @if($invoice->customer)
                        <div class="pull-right"> {{number_format(getCustomerBalance($invoice->customer_id))}} </div>
                        @else
                            <div class="pull-right"> {{number_format(getSupplierBalance($invoice->supplier_id))}}</div>
                        @endif
                        <div class="clearfix"></div>
                    </div>
                -->
    </section><!--/wrapper -->
@endsection
@section('scripts')
<script type="text/javascript">
    $('.printMe').click(function(){
     window.print();
});
</script>
@endsection