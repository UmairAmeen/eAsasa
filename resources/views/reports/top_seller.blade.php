@extends('reporting')

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Top Product Reporting</h3>
	<h4>Report {{($from)?(date('d-M-Y',strtotime($from))):"All time"}} - {{($to)?(date('d-M-Y',strtotime($to))):date('d-M-Y')}}</h4>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>

@endsection
@section('content')
<div class="row showback">
	<form method="GET" action="{{url('reports')}}/top_selling">

			<div class="form-group">
				<label>From Date</label>
				<input type="date" class="form-control" name="from" value="{{$request->from}}">
			</div>
			<div class="form-group">
				<label>To Date</label>
				<input type="date" class="form-control" name="to" value="{{$request->to}}">
			</div>
			<div class="form-group">
				<label>Limit</label>
				<input type="number" min="1" max="500" class="form-control" name="limit" value="{{$request->limit}}">
			</div>
			
			<div class="form-group">

			<button type="submit" class="btn btn-success">Search Data</button>
		</div>
			
		</form>
	<div class="col-md-12">
 {!! Chart::display("id-hasi", $type_chart_amount) !!}

<br>
<h3>Top Selling Product</h3>
<table class="table datatable">
	<thead>
		<th>Product Name</th>
		<th>Product Brand</th>
		<th>Quantity</th>
	</thead>
	<tbody>
		@foreach($stocks as $stc)
		<tr>
			<td>{{$stc->product->name}}</td>
			<td>{{$stc->product->brand}}</td>
			<td>{{$stc->qty}}</td>
		</tr>
		@endforeach
	</tbody>
</table>
		
	</div>
</div>

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

       <script type="text/javascript">

    $(document).ready(function(e){
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
                title: "Top Selling Product Overview",
                messageTop: messageTopContent,
                messageBottom: messageBottomContent
            },
            {
                extend: 'pdf',
                title: "Top Selling Product Overview",
                messageTop: messageTopContent,
                messageBottom: messageBottomContent
            },{
                extend: 'print',
                title: "Top Selling Product Overview",
                messageTop: messageTopContent,
                messageBottom: messageBottomContent
            },
			'colvis'
    ]});


});
</script>
@endsection