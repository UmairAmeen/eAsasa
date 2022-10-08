@extends('layout')
@section('header')
    <div class="page-header">
        <h1>Warehouse: {{$warehouse->name}}</h1>
        <h3>Warehouse Address: {{($warehouse->address)?$warehouse->address:"N/A"}}</h3>
        <form action="{{ route('warehouses.destroy', $warehouse->id) }}" method="POST" style="display: inline;" onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>
    </div>
@endsection
@section('content')
<div class="content-panel">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-condensed table-hover datatable">
                <thead>
                <tr>
                    <th>Name</th>
                    @if(strpos(session()->get('settings.products.optional_items'),'description') !== false)
                    <th>Description</th>
                    @endif
                    @if(strpos(session()->get('settings.products.optional_items'),'size') !== false)
                    <th>Size</th>
                    @endif
                    @if(strpos(session()->get('settings.products.optional_items'),'color') !== false)
                    <th>Color</th>
                    @endif
                    @if(strpos(session()->get('settings.products.optional_items'),'pattern') !== false)
                    <th>Pattern</th>
                    @endif
                    <th>Brand</th>
                    @if(session()->get('settings.barcode.is_enable'))
                    <th>Barcode</th>
                    @endif
                    <th>Total Stock in Warehouse</th>
                    <th>Stock Log</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($pro as $value)
                <tr>
                    <td>{{$value->first()->product->name}} &ensp; {{$value->first()->product->translation}}</td>
                    @if(strpos(session()->get('settings.products.optional_items'),'description') !== false)
                    <td>{{$value->first()->product->description}}</td>
                    @endif
                    @if(strpos(session()->get('settings.products.optional_items'),'size') !== false)
                    <td>{{$value->first()->product->size}}</td>
                    @endif
                    @if(strpos(session()->get('settings.products.optional_items'),'color') !== false)
                    <td>{{$value->first()->product->color}}</td>
                    @endif
                    @if(strpos(session()->get('settings.products.optional_items'),'pattern') !== false)
                    <td>{{$value->first()->product->pattern}}</td>
                    @endif
                    <td>{{$value->first()->product->brand}}</td>
                    @if(session()->get('settings.barcode.is_enable'))
                    <td>{{$value->first()->product->barcode}}</td>
                    @endif
                    <td>{{warehouse_stock($value->first()->product, $warehouse->id)}}</td>
                    <td><a href="/report/product/{{$value->first()->product->id}}/warehouse/{{$value->first()->warehouse->id}}">View All Stock Log</a></td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <a class="btn btn-link" href="{{ route('warehouses.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
        </div>
    </div>
</div>
@endsection