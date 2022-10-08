@extends('reporting')
@section('header')
<div class="col-md-12 text-center no-print">
	<h3>Warehouse Transfer Report</h3>
</div>
@endsection
@section('content')
<div class="row showback text-center no-print">
	<form method="GET" action="{{url('reports')}}/warehouse_transfer">
		<div class="col-md-3">
			<div class="form-group">
				<label>Source</label>
				{!! Form::Select('from_warehouse', $warehouses, $from_warehoues, ['class' => 'form-control']) !!}
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Target</label>
				{!! Form::Select('to_warehouse', $warehouses, $to_warehoues, ['class' => 'form-control']) !!}
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>From</label>
				<input type="date" class="form-control" name="from" value="{{$from}}">
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>To</label>
				<input type="date" class="form-control" name="to" value="{{$to}}">
			</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
				<button type="submit" class="btn btn-success">Search</button>
				<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
			</div>
		</div>
	</form>
</div>
<div class="row showback">
	<center>
		<h3>Warehouse Transfer {{ date_format_app($from) }}</h3>
		{{-- <a onclick="window.print();" class="btn btn-primary btn-xl no-print">Print</a> --}}
		{{-- <a href="{{url('reports/log_report')}}?from={{$from}}" class="btn btn-default btn-xl no-print">Full Details</a> --}}
	</center>
	@php($columns = 6)
	@if(session()->get('settings.products.is_image_enable')) @php($columns++) @endif
	@if(session()->get('settings.barcode.is_enable')) @php($columns++) @endif
	<table class="table table-striped table-bordered table-hover datatable">
		<thead class="thead-dark">
			<tr>
				<th>Date</th>
				@if(session()->get('settings.barcode.is_enable')) <th>Barcode</th> @endif
				@if(session()->get('settings.products.is_image_enable')) <th>Image</th> @endif
				<th>Name</th>
				<th>Source</th>
				<th>Target</th>
				<th>Quantity</th>
				<th>Added By</th>
			</tr>
		</thead>
		<tbody>
			@if(count($result) > 0)
			@foreach ($result as $data)
				<tr>
					<td>{{ date_format_app($data['date']) }}</td>
					@if(session()->get('settings.barcode.is_enable')) <td>{{ $data['barcode'] }}</td> @endif
					@if(session()->get('settings.products.is_image_enable')) <td>{{ $data['image'] }}</td> @endif
					<td>{{ $data['name'] }}</td>
					<td>{{ $warehouses[$data['from']] }}</td>
					<td>{{ $warehouses[$data['to']] }}</td>
					<td>{{ $data['quantity'] }}</td>
					<td>{{ empty($users[$data['added_by']]) ? "" : $users[$data['added_by']] }}</td>
				</tr>
			@endforeach
			@else
			<tr><th colspan="{{$columns}}">No data found</th></tr>
			@endif
		</tbody>
	</table>
	{{-- <small class="onlyPrint">Report by eAsasa ( 0345 4777487)</small>
	<small class="onlyPrint">Print Timestamp: {{ date("d M Y H:i:s") }}</small> --}}
</div>
<script type="text/javascript" src="{{asset('/assets/js/jquery.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.colVis.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.buttons.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.flash.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.print.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.html5.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/datatables/jszip.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/datatables/pdfmake.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/datatables/vfs_fonts.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
  const company = "{{session()->get('settings.profile.company')}}";
  const phone = "{{session()->get('settings.profile.phone')}}";
  const address = "{{session()->get('settings.profile.address')}}";
  $(document).ready(function(e){
	$("#message_show").hide();
	$(".table").show();
	const brandingHtml = "<h1>" + company + "</h1><address><strong>" + address + "<br><abbr>Phone:</abbr>" + phone + "</address>";
	const brandingPdf = company + "\n" + address + "\nPhone: " + phone;
	var messageTopContentName = "";
	var messageTopContentPhone = "";
	var pdfNewLine = "\n";
	var printNewLine = "<br>";
	var messageTopContent	 = 'Stock Worth Report';
	var messageBottomContent = "\nReports by FireWorks Inventory";
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
		  title: "Warehouse Transfer",
		  messageTop: messageTopContentName+pdfNewLine+messageTopContent,
		  messageBottom: messageBottomContent
		}, {
		  extend: 'pdf',
		  title: "Warehouse Transfer",
		  messageTop: brandingPdf+pdfNewLine+pdfNewLine+messageTopContentName+pdfNewLine+messageTopContentPhone+pdfNewLine+messageTopContent,
		  messageBottom: messageBottomContent
		},{
		  extend: 'print',
		  title: "Warehouse Transfer",
		  messageTop: brandingHtml+printNewLine+printNewLine+messageTopContentName+printNewLine+messageTopContentPhone+printNewLine+messageTopContent,
		  messageBottom: messageBottomContent
		},
		'colvis'
	  ]
	});
  });
</script>
@endsection
