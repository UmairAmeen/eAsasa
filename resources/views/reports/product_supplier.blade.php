@extends('reporting')

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Product Purchased from Supplier</h3>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>

@endsection
@section('content')
<div class="row showback">
	<div class="col-md-4"></div>
	<form method="GET" action="{{url()->current()}}" class=" text-center col-md-4" style="margin-top: 30px">
		<div class="form-group">
        <label>Supplier</label>
				<select class="form-control supplier_selector" name="supplier_id">

					@foreach($suppliers as $custom)
					@if($custom->id == $request->supplier_id)
					<option value="{{$custom->id}}" selected="selected">{{$custom->name}}</option>
					@else
					<option value="{{$custom->id}}" >{{$custom->name}}</option>
					@endif
					@endforeach
				
				</select>
			</div>
			<div class="form-group">

			<button type="submit" class="btn btn-success">Search Data</button>
		</div>
	</form>
</div>

@if($supplier)
<div class="row">
	<div class="card col-md-offset-4 col-md-4 text-center">
		<h3>Profile</h3>
		<hr>
		<h4 class="text-success">{{$supplier->name}}</h4>
		<h4>{{$supplier->phone}}</h4>
		<h4>{{$supplier->address}}</h4>
		<h4>{{$supplier->description}}</h4>
	</div>
	<div class="showback col-md-offset-2 col-md-8">
		<table class="table">
			<thead>
				<tr>
					<th>Date</th>
					<th>Product</th>
					<th>Brand</th>
					<th>Order #</th>
					<th>Bill #</th>
					<th>Last Purchased Price</th>
					<th>Last Purchased Qty</th>
				</tr>
			</thead>
			<tbody>
				@foreach($purchases as $key => $purchase)
				<tr>
					<?php $produ = get_product($key); ?>
					<td>{{app_date_format($purchase['date'])}}</td>
					<td><a class="btn btn-default" href="{{ url('products') }}\{{$key}}">{{$produ->name}}</a></td>
					<td>{{$produ->brand}}</td>
					<td><a class="btn btn-default" href="{{ url('invoices') }}\{{$purchase['invoice_id']}}">{{$purchase['invoice_id']}}</a></td>
					<td><a class="btn btn-default" href="{{ url('invoices') }}\{{$purchase['invoice_id']}}">{{$purchase['invoice']->bill_number}}</a></td>
					<td>{{$purchase['price']}}</td>
					<td>{{$purchase['qty']}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endif
        <script src="{{asset('assets/js/jquery.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{url('supplier.json')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/select2.full.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.bootstrap.min.js')}}"></script>

<script type="text/javascript">
	$(document).ready(function(){
	      $(".supplier_selector").select2({
        data: supplier_json_d,
        // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 0,
    });

	      $("table").DataTable();
  
});
</script>
@endsection