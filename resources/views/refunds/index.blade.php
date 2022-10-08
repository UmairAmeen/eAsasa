@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="glyphicon glyphicon-align-justify"></i> Refund
            <a class="btn btn-success pull-right" href="{{ route('refunds.create') }}"><i class="glyphicon glyphicon-plus"></i> Add Claim</a>
        </h1>

    </div>
@endsection

@section('content')
    <div class="row">
                

        <div class="col-md-12">
        <div class="content-panel">
            @if($refunds->count())
                <table class="table table-condensed table-striped datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Supplier</th>
                            <th>Quantity</th>
                            <th>Purchase Price (Per Pcs)</th>
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($refunds as $refund)
                            <tr>
                                <td>{{$refund->id}}</td>
                                <td>{{$refund->date}}</td>
                                <td><?=($refund->customer)?"<a href='/customers/".$refund->customer->id."' >".$refund->customer->name."</a>":"N/A"?></td>
                                <td><a href="/products/{{$refund->product->id}}">{{$refund->product->name}}</a></td>
                                <td><a href="/suppliers/{{$refund->supplier->id}}">{{$refund->supplier->name}}</a></td>
                                <td>{{$refund->quantity}}</td>
                                <td>{{$refund->price}}</td>
                                
                                <td class="text-right">
                                    <a class="btn btn-xs btn-primary" href="{{ route('refunds.show', $refund->id) }}"><i class="glyphicon glyphicon-eye-open"></i> View</a>
                                    <a class="btn btn-xs btn-warning" href="{{ route('refunds.edit', $refund->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                    <form action="{{ route('refunds.destroy', $refund->id) }}" method="POST" style="display: inline;" ><div id="log"></div>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="if(confirm('Delete? Are you sure?')) { return true } else {return false };"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $refunds->render() !!}
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif

        </div>
        </div>
    </div>

@endsection