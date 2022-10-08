@extends('layout')
@section('header')
<div class="page-header">
        <h1>Purchases Receipt #{{$purchase->id}}</h1>
         <a class="btn btn-link" href="{{ route('purchases.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back To All Purchases</a>   
        <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" style="display: inline;" >
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="btn-group pull-right" role="group" aria-label="...">
                <a class="btn btn-warning btn-group" role="group" href="{{ route('purchases.edit', $purchase->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                <button type="submit" class="btn btn-danger" onclick="if(confirm('Delete? Are you sure?')) { return true } else {return false };">Delete <i class="glyphicon glyphicon-trash"></i></button>
            </div>
        </form>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

          <div class="card">
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><b>ID:</b> {{$purchase->id}}</li>
    <li class="list-group-item"><b>Date:</b> {{date(session()->get('settings.misc.date_format'), strtotime($purchase->date))}}</li>
    <li class="list-group-item"><b>Supplier:</b> {{$purchase->supplier->name}}</li>
    <li class="list-group-item"><b>Warehouse:</b> {{$purchase->warehouse->name}}</li>
    <li class="list-group-item"><b>Product Purchased:</b> {{$purchase->product->name}} &emsp; {{$purchase->product->translation}}</li>
    <li class="list-group-item"><b>Brand:</b> {{$purchase->product->brand}}</li>
    <li class="list-group-item"><b>Stock Purchased:</b> {{$purchase->stock}}</li>
    <li class="list-group-item"><b>Per Pcs Price:</b> {{($purchase->price < 0)?"**Inital Stock**":$purchase->price}}</li>
    @if ($purchase->price > 0)
        <li class="list-group-item"><b>Total Cost:</b> {{$purchase->price * $purchase->stock}}</li>
    @endif
  </ul>
</div>
           

           

        </div>
    </div>

@endsection