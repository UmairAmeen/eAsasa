@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fa fa-line-chart"></i> Warehouse / Product Stock
           
        </h1>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
        <div class="content-panel">
            @if($stocks->count())
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>From Supplier</th>
                            <th>Product</th>
                            <th>To Customer</th>
                            <th>Type</th>
                            <th>Stock</th>
                            
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($stocks as $value)
                            <tr>
                                <td>{{$value->id}}</td>
                                <td>{{date(session()->get('settings.misc.date_format'), strtotime($value->date))}}</td>
                                <td><?=(!isset($value->supplier))?"None":"<a href='/suppliers/".$value->supplier->id."' >".$value->supplier->name."</a>"?></td>
                                <td><a href="/products/{{$value->product->id}}">{{$value->product->name}}</a></td>
                                 <td><?=(!isset($value->customer))?"None":"<a href='/customers/".$value->customer->id."' >".$value->customer->name."</a>"?></td>
                                <td><label class="label label-info">{{$value->type}}</label></td>
                                @if ($value->type == "in" || $value->type == "purchase")
                                <td><label class="label label-success">+{{$value->quantity}}</label></td>
                                @else
                                <td><label class="label label-danger">-{{$value->quantity}}</label></td>
                                @endif

                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $stocks->render() !!}
            @else
                <h3 class="text-center alert alert-info">No Product Transaction in this Warehouse</h3>
            @endif
            </div>

        </div>
    </div>

@endsection