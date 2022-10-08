@extends('reporting')

@section('header')
<title>Balance Sheet Reporting</title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Stock Reporting</h3>
	<h4>Report {{($from)?(date('d-M-Y',strtotime($from))):"All time"}} - {{($to)?(date('d-M-Y',strtotime($to))):date('d-M-Y')}}</h4>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>

@endsection
@section('content')
<div class="row showback">
	<form method="GET" action="{{url('reports')}}/stock_detail">

			<div class="form-group">
				<label>From Date</label>
				<input type="date" class="form-control" name="from" value="{{$request->from}}">
			</div>
			<div class="form-group">
				<label>To Date</label>
				<input type="date" class="form-control" name="to" value="{{$request->to}}">
			</div>

			<div class="form-group">
				<label>Product</label>
				<select class="form-control product_selector" name="product_id">
				</select>
			</div>
			<div class="form-group">

			<button type="submit" class="btn btn-success">Search Data</button>
		</div>
			
		</form>
	<div class="col-md-12">
 {!! Chart::display("id-highchartsnya", $chart) !!}
		@if(count($stocks_out) || count($stocks_in) )

<h4>Current Available Stock: {{calculateStockFromProductId($product_id)}}</h4>
<br>
<h3>Product Stock Details</h3>
<table class="table datatable">
	<thead>
		<th>Date</th>
		<th>Type</th>
		<th>Stock</th>
	</thead>
	<tbody>
		@foreach($stocks_in as $stock)
		<tr>
			<td>{{date_format_app($stock->date)}}</td>
			<td>{{$stock->type}}</td>
			<td>{{$stock->qty}}</td>
		</tr>
		@endforeach
		@foreach($stocks_out as $stock)
		<tr>
			<td>{{date_format_app($stock->date)}}</td>
			<td>{{$stock->type}}</td>
			<td>{{$stock->qty}}</td>
		</tr>
		@endforeach
	</tbody>
</table>
		
	</div>
	@endif
</div>

    <script type="text/javascript" src="{{asset('/assets/js/datatable/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.buttons.js')}}"></script>
     <script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.flash.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.print.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.html5.js')}}"></script>

    <script type="text/javascript" src="{{asset('vendor/datatables/jszip.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/datatables/pdfmake.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/datatables/vfs_fonts.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/select2.full.min.js')}}"></script>

       <script type="text/javascript">

    $(document).ready(function(e){
    	var messageTopContent	 = 'Report date: {{($from)?(date("d-M-Y",strtotime($from))):"All time"}} to {{($to)?(date("d-M-Y",strtotime($to))):date("d-M-Y")}}';
    	var messageBottomContent = "\nReports by eAsasa ( 0345 4777487)";
          $(".datatable").DataTable({"deferRender": true,dom: 'Bfrtip',
    buttons: [
        'copy', {
                extend: 'excel',
                title: "Product Stock Overview",
                messageTop: messageTopContent,
                messageBottom: messageBottomContent
            },
            {
                extend: 'pdf',
                title: "Product Stock Overview",
                messageTop: messageTopContent,
                messageBottom: messageBottomContent
            },{
                extend: 'print',
                title: "Product Stock Overview",
                messageTop: messageTopContent,
                messageBottom: messageBottomContent
            }
    ]});

      $(".product_selector").select2({
        ajax: {
          url: "/pagination_product_json/",
          dataType: 'json',
          delay: 50,
          data: function (params) {
            return {
              q: params.term, // search term
              page: params.page
            };
          },
          processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
              results: data.items,
              pagination: {
                more: (params.page * 10) < data.total_count
              }
            };
          },
          // cache: true
        },
        // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 0,
    });
  
});
</script>
@endsection