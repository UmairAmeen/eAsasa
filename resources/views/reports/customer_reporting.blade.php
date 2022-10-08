@extends('reporting')
@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	@if($type == "supplier")
		<h3>Supplier Reporting</h3>
	@else
		<h3>Customer Reporting</h3>
	@endif
	<h4>{{($customer)?$customer->name:""}}</h4>
	<h4>Report {{($from)?(date('d-M-Y',strtotime($from))):"All time"}} - {{($to)?(date('d-M-Y',strtotime($to))):date('d-M-Y')}}</h4>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>
@endsection
@section('content')
<div class="row showback">
	<form method="GET" action="{{url()->current()}}">
		<div class="form-group">
			<label>From Date</label>
			<input type="date" class="form-control" name="from" value="{{$request->from}}">
		</div>
		<div class="form-group">
			<label>To Date</label>
			<input type="date" class="form-control" name="to" value="{{$request->to}}">
		</div>
		@if($type=="supplier")
			<div class="form-group">
				<label>Supplier</label>
				<select class="form-control customer_selector" name="customer_id">
					<option value="0">--All Supplier--</option>
					@foreach($customers as $custom)
						@if($custom->id == $request->customer_id)
							<option value="{{$custom->id}}" selected="selected">{{$custom->name}}</option>
						@else
							<option value="{{$custom->id}}">{{$custom->name}}</option>
						@endif
					@endforeach				
				</select>
			</div>
		@else
			<div class="form-group">
				<label>Customer</label>
				<select class="form-control customer_selector" name="customer_id">
					<option value="0">--All Customers--</option>
					@foreach($customers as $custom)
						@if($custom->id == $request->customer_id)
							<option value="{{$custom->id}}" selected="selected">{{$custom->name}}</option>
						@else
							<option value="{{$custom->id}}">{{$custom->name}}</option>
						@endif
					@endforeach				
				</select>
			</div>
		@endif
		<div class="form-group">
			<button type="submit" class="btn btn-success">Search Data</button>
		</div>			
	</form>
</div>
@if($customer)
	<div class="row showback">
		<div class="col-md-12 text-center" style="font-size: 40px;">
			<div class="col-md-3" style="border-right: 1px solid #ad9393;">
				<h2>Credit</h2>{{number_format($credit_now,1)}}</div>
			<div class="col-md-3" style="border-right: 1px solid #ad9393;"><h2>Debit</h2>{{number_format($debit_now,1)}}</div>
			<div class="col-md-3" style="border-right: 1px solid #ad9393;">
				<h2>Recovery</h2> 
				@if (!$debit_now || !$credit_now)
					N/A
				@else
					@if($type == "supplier")
						{{number_format(($credit_now/$debit_now)*100,1)}}%
					@else
						{{number_format(($debit_now/$credit_now)*100,1)}}%
					@endif
				@endif
			</div>
			<div class="col-md-3">
				<h2>All Time Recorvery %</h2>
				@if (!$all_debit_now || !$all_credit_now)
					N/A
				@else
					@if($type == "supplier")
						{{number_format(($all_credit_now/$all_debit_now)*100,1)}}%
					@else
						{{number_format(($all_debit_now/$all_credit_now)*100,1)}}%
					@endif
				@endif
			</div>
		</div>
	</div>
	<div class="row showback">
		{!! Chart::display("id-highchartsnya", $chart, ['jquery.js' => true , 'highcharts.js' => true,'exporting.js'=> true,'format' => 'chart']) !!}
	</div>
	@if($type!="supplier")
		<div class="row showback">
		<h3>Sale Details</h3>
		<table class="table datatable">
		<thead>
			<th>Date</th>
			<th>invoice#</th>
			<th>Bill #</th>
			<th>Product</th>
			<th>Quantity</th>
			<th>Price</th>
		</thead>
		<tbody>
			@foreach($orders as $order)
				@foreach($order->orders as $odr)
			<tr>
				<td>{{date_format_app($order->date)}}</td>
				<td>{{$order->id}}</td>
				<td>{{$order->bill_number}}</td>
				<td>{{$odr->product->name." ".$odr->product->brand}}</td>
				<td>{{$odr->quantity}}</td>
				<td>{{$odr->salePrice}}</td>
			</tr>
				@endforeach
			@endforeach
			</tbody>
		</table>
		</div>
	@endif
@else
    <script src="{{asset('assets/js/jquery.js')}}"></script>
@endif
    <script type="text/javascript" src="{{asset('/assets/js/datatable/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/select2.full.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.colVis.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.buttons.js')}}?v=1"></script>
     <script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.flash.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.print.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.html5.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/datatables/jszip.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/datatables/pdfmake.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/datatables/vfs_fonts.js')}}"></script>
	<script type="text/javascript">
	const company = "{{session()->get('settings.profile.company')}}";
    const phone = "{{session()->get('settings.profile.phone')}}";
    const address = "{{session()->get('settings.profile.address')}}";
    $(document).ready(function(e) {
      	$(".customer_selector").select2();
		const brandingHtml = "<h1>" + company + "</h1><address><strong>" + address + "<br><abbr>Phone:</abbr>" + phone + "</address>";
      	const brandingPdf = company + "\n" + address + "\nPhone: " + phone;
		var messageTopContentName = "{{(isset($trans) && $trans->customer)?$trans->customer->name:""}}";
		var messageTopContentPhone = "{{(isset($trans) && $trans->customer)?$trans->customer->phone:""}}";
		var pdfNewLine = "\n";
		var printNewLine = "<br>";
 		var messageTopContent	 = 'Report date: {{($from)?(date("d-M-Y",strtotime($from))):"All time"}} to {{($to)?(date("d-M-Y",strtotime($to))):date("d-M-Y")}}';
    	var messageBottomContent = "\nReports by eAsasa ( 0345 4777487)";
		$(".datatable").DataTable({
			"deferRender": true,
			dom: 'Bfrtip',
			"ordering": true, 
			stateSave: true,
			"stateLoadParams": function (settings, data) {
				data.search.search = "";
				data.length = 10;
				data.start = 0;
				data.order = [];
			},           
    		buttons: [
        		'copy', {
					extend: 'excel',
					title: "Customer Reporting Overview",
					messageTop: messageTopContentName+pdfNewLine+messageTopContent,
					messageBottom: messageBottomContent,
					exportOptions: {columns: ':visible'}
            	}, {
					extend: 'pdf',
					title: "Customer Reporting Overview",
					messageTop: brandingPdf+pdfNewLine+pdfNewLine+messageTopContentName+pdfNewLine+messageTopContentPhone+pdfNewLine+messageTopContent,
					messageBottom: messageBottomContent,
					exportOptions: {columns: ':visible'}
				}, {
					extend: 'print',
					title: "Customer Reporting Overview",
					messageTop: brandingHtml+printNewLine+printNewLine+messageTopContentName+printNewLine+messageTopContentPhone+printNewLine+messageTopContent,
					messageBottom: messageBottomContent,
					exportOptions: {columns: ':visible'}
				},
				'colvis'
    		]
		});
	});
</script>
@endsection