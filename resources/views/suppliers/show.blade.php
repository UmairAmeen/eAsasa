@extends('layout')
@section('header')
<div class="page-header">
        <h1>Supplier</h1>
            <a class="btn btn-link" href="{{ route('suppliers.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back to All Suppliers</a>
        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display: inline;" >
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="btn-group pull-right" role="group" aria-label="...">
                <a class="btn btn-success btn-group" role="group" href="{{url('supplier_reporting')}}/balance_sheet?customer_id={{$supplier->id}}"><i class="glyphicon glyphicon-file"></i> Ledger</a>
                <a class="btn btn-warning btn-group" role="group" href="{{ route('suppliers.edit', $supplier->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                <a class ="btn btn-group btn-default" target="_blank" href="{{route('supplier_price_records.show', $supplier->id)}}" ><i class="glyphicon glyphicon-usd"></i>Price List</a>
                <button type="submit" class="btn btn-danger" onclick ="return (confirm('Delete? Are you sure?'))">Delete <i class="glyphicon glyphicon-trash"></i></button>
            </div>
        </form>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div id="log"></div>
           <div class="card">
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><b>ID:</b> {{$supplier->id}}</li>
    <li class="list-group-item"><b>Name:</b> {{$supplier->name}}</li>
    <li class="list-group-item"><b>Phone Number:</b> {{$supplier->phone}}</li>
    <li class="list-group-item"><b>Address:</b> {{($supplier->address)?$supplier->address:"N/A"}}</li>
    <li class="list-group-item"><b>Description:</b> {{($supplier->description)?$supplier->description:"N/A"}}</li>
  </ul>
</div>

        </div>
    </div>


        </div>
    </div>

@endsection