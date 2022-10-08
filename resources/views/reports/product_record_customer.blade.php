@extends('reporting')

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Product Record Customer</h3>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>

@endsection
@section('content')
<div class="row showback">
	<div class="col-md-4"></div>
	<form method="GET" action="{{url()->current()}}" class=" text-center col-md-4" style="margin-top: 30px">
		<div class="form-group">
        <label>Products</label>
				<select class="form-control supplier_selector" name="product_id">

					@foreach($products as $custom)
					@if($custom->id == $request->product_id)
					<option value="{{$custom->id}}" selected="selected">{{$custom->name}} - {{$custom->brand}}</option>
					@endif
					@endforeach
				
				</select>
			</div>
			<div class="form-group">

			<button type="submit" class="btn btn-success">Search Data</button>
		</div>
	</form>
</div>

@if($product)
<div class="row">
	<div class="card col-md-offset-4 col-md-4 text-center">
		<h3>Product</h3>
		<hr>
		<h4>Id: #{{$product->id}}</h4>
		<h4 class="text-success">{{$product->name}}/{{$product->translation}}</h4>
		<h4>Brand: {{$product->brand}}</h4>
		<h4>Price: {{$product->salePrice}}</h4>
		<h4></h4>
	</div>
	<div class="showback col-md-offset-2 col-md-8">
		<table class="table">
			<thead>
				<tr>
					<th>Date</th>
					<th>Customer</th>
					<th>Order #</th>
					<th>Last Sale Price</th>
					<th>Last Sold Qty</th>
				</tr>
			</thead>
			<tbody>
				@foreach($sales as $key => $sale)
				<tr>
					<td>{{app_date_format($sale['invoice']->date)}}</td>
					<td><a class="btn btn-default" href="{{ url('customers') }}\{{$key}}">#{{$key}} {{get_customer($key)->name}}</a></td>
					<td><a class="btn btn-default" href="{{ url('invoices') }}\{{$sale['invoice']->id}}">{{$sale['invoice']->id}}</a></td>
					<td>{{$sale['price']}}</td>
					<td>{{$sale['qty']}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endif
    <script type="text/javascript" src="{{url('products.json')}}?v={{versioning('products')}}"></script>

        <script src="{{asset('assets/js/jquery.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/jquery.dataTables.min.js')}}"></script>

    <script type="text/javascript" src="{{asset('/assets/js/select2.full.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.bootstrap.min.js')}}"></script>

<script type="text/javascript">
	$(document).ready(function(){
	      $(".supplier_selector").select2({
            placeholder: "Select a product",
            data:products_json_d,
            matcher: function (params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }

            keywords=(params.term).split(" ");

            for (var i = 0; i < keywords.length; i++) {
                if ((((data.text).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1) && (((data.barcode).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1) )  
                return null;
            }
            return data;
        },
            minimumInputLength: 0,
        });

	      $("table").DataTable();
  
});
</script>
@endsection