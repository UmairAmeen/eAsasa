@extends('reporting')
@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Product Record</h3>
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
							<option value="{{$custom->id}}" selected="selected">{{$custom->name}}</option>
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
		@if ((strpos(session()->get('settings.products.optional_items'),'size') !== false) && !empty($product->size))
		<h4>Size: {{$product->size}}</h4>
		@endif
		@if ((strpos(session()->get('settings.products.optional_items'), 'color') !== false) && !empty($product->color))
		<h4>Color: {{$product->color}}</h4>
		@endif
		@if ((strpos(session()->get('settings.products.optional_items'), 'pattern') !== false) && !empty($product->pattern))
		<h4>Pattern: {{$product->pattern}}</h4>
		@endif
		<h4>Price: {{$product->salePrice + 0}}</h4>
		<h4></h4>
	</div>
	<div class="showback col-md-offset-2 col-md-8">
		<table class="table">
			<thead>
				<tr>
					<th>Date</th>
					<th>Supplier</th>
					<th>Invoice #</th>
					<th>Purchased Price</th>
					<th>Purchased Qty</th>
				</tr>
			</thead>
			<tbody>
				@foreach($purchases as $key => $purchase)
				<tr>
					<td>{{app_date_format($purchase['invoice']->date)}}</td>
					@if($purchase['supplier_id'])
					<td><a class="btn btn-default" href="{{ url('suppliers') }}\{{$purchase['supplier_id']}}">{{get_supplier($purchase['supplier_id'])->name}}</a></td>
					@else
					<td>-</td>
					@endif
					<td><a class="btn btn-default" target="_blank" href="{{ url('invoices') }}\{{$purchase['invoice']->id}}">{{$purchase['invoice']->id}} {{$purchase['invoice']->bill_number}}</a></td>
					<td>{{$purchase['price'] + 0}}</td>
					<td>{{$purchase['qty'] + 0}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endif
<script src="{{asset('assets/js/jquery.js')}}"></script>
<script type="text/javascript" src="{{url('products.json')}}?v={{versioning('products')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/select2.full.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.bootstrap.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".supplier_selector").select2({
        	data:products_json_d,
        	// escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        	minimumInputLength: 0,
         	matcher: function (params, data) {
            	if ($.trim(params.term) === '') {
                	return data;
            	}
            	keywords=(params.term).split(" ");
            	for (var i = 0; i < keywords.length; i++) {
                	if ((((data.text).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1) && (((data.barcode).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1)) {
						return null;
					}
            	}
            	return data;
        	},
    	});
		$("table").DataTable();
	});
</script>
@endsection