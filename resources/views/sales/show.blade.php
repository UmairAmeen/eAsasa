@extends('layout')
@section('header')
<div class="page-header">
        <h1>Sales #{{$sale->id}}</h1>
        <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" style="display: inline;" onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="btn-group pull-right" role="group" aria-label="...">
                <a class="btn btn-warning btn-group" role="group" href="{{ route('sales.edit', $sale->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                <button type="submit" class="btn btn-danger">Delete <i class="glyphicon glyphicon-trash"></i></button>
            </div>
        </form>
    </div>
@endsection
@section('content')
    <section id="main-content">
          <section class="wrapper">
             <div class="col-lg-12 mt">
                <div class="row content-panel">
                    <div class="col-lg-10 col-lg-offset-1">
                        <div class="invoice-body">
                            <div class="pull-left"> 
                                <h1>{{session()->get('settings.profile.company')}}</h1>
                                <address>
                                    <strong>{{session()->get('settings.profile.address')}}<br>
                                    <abbr title="Phone">P:</abbr>{{session()->get('settings.profile.phone')}}
                                </address>
                            </div><!-- /pull-left -->
                            <div class="pull-right">
                                <h2>invoice</h2>
                            </div><! --/pull-right -->                            
                            <div class="clearfix"></div>
                            <br>
                            <br>
                            <br>
                            {!! session()->get('settings.misc.invoice_header') !!}
                            <div class="row">
                                <div class="col-md-9">
                                    <h4>Paul Smith</h4>
                                    <address>
                                    <strong>Enterprise Corp.</strong><br>
                                    234 Great Ave, Suite 600<br>
                                    San Francisco, CA 94107<br>
                                    <abbr title="Phone">P:</abbr> (123) 456-7890
                                    </address>
                                </div><! --/col-md-9 -->
                                <div class="col-md-3"><br>
                                    <div>
                                        <div class="pull-left"> INVOICE NO : </div>
                                        <div class=""> {{$sale->id}} </div>
                                        <div class="clearfix"></div>
                                    </div>
                                <div><!-- /col-md-3 -->
                                <div class="pull-left"> INVOICE DATE : </div>
                                <div class="pull-right">{{date('d/m/y',strtotime($sale->created_at))}} </div>
                                <div class="clearfix"></div>
                            </div><! --/row -->
                            <br>
                            <!-- <div class="well well-small green">
                                <div class="pull-left"> Total Due : </div>
                                <div class="pull-right"> 8,000 USD </div>
                                <div class="clearfix"></div>
                            </div> -->
                        </div><!-- /invoice-body -->
                    </div><! --/col-lg-10 -->
                    <table class="table">
                        <thead>
                        <tr>
                        <th style="width:60px" class="text-center">ID</th>
                        <th class="text-left">DESCRIPTION</th>
                        <th style="width:140px" class="text-right">UNIT PRICE</th>
                        <th style="width:140px" class="text-right">QTY</th>
                        <th style="width:90px" class="text-right">TOTAL</th>
                        </tr>
                        </thead>
                            <tbody>
                            <?php $sub_total = 0; ?>
                            @foreach (getAllOrders($sale->order_ids) as $value)
                                <tr>
                                <td class="text-center">{{$value->id}}</td>
                                <td>{{$value->product->name}}</td>
                                <td class="text-right">{{$value->saleprice}}</td>
                                <td class="text-right">{{$value->amount}}</td>
                                <td class="text-right">{{$value->amount * $value->saleprice}}</td>
                                </tr>
                                <?php $sub_total += $value->amount * $value->saleprice; ?>
                           @endforeach
                                <tr>
                                <td colspan="2" rowspan="4" >{!! session()->get('settings.misc.invoice_footer') !!}</td>
                                <td class="text-right"><strong>Subtotal</strong></td>
                                <td class="text-right">{{$sub_total}}</td>
                                </tr>
                                <tr>
                                <td class="text-right no-border"><strong>Shipping</strong></td>
                                <td class="text-right">$0.00</td>
                                </tr>
                                <tr>
                                <td class="text-right no-border"><strong>VAT Included in Total</strong></td>
                                <td class="text-right">$0.00</td>
                                </tr>
                                <tr>
                                <td class="text-right no-border"><div class="well well-small green"><strong>Total</strong></div></td>
                                <td class="text-right"><strong>$1029.00</strong></td>
                                </tr>
                            </tbody>
                    </table>
                    <br>
                    <br>
        </div><!--/col-lg-12 mt -->
    </section><! --/wrapper -->
</section><!-- /MAIN CONTENT -->

@endsection