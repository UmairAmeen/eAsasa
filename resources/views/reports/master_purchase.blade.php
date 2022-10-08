@extends('reporting')

@section('header')
    <script type="text/javascript" src="{{asset('/assets/js/jquery.js')}}"></script>

<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Purchase Detailed Reporting</h3>

	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>

@endsection
@section('content')
<div class="row showback">
	<form method="GET" action="{{url()->current()}}">

			<div class="form-group">
				<label>Product</label>
				<select class="form-control product" name="product_id">
				</select>
			</div>
			<div class="form-group">

			<button type="submit" class="btn btn-success">Search Data</button>
		</div>
			
		</form>
	<div class="col-md-12">
		@if(count($output))

<br>


<div class="row">
<h3>Product Stock Purchased Details</h3>
  <div class="col-md-12">
    <div class="col-md-6">
      <table class="table datatable">
  <thead>
    <th>Month</th>
    <th>Quantity</th>
  </thead>
  <tbody>
    @foreach($output['stk_in'] as $stock)
    <tr>
      <td>{{$stock->mdt}}</td>
      <td>{{$stock->sm}}</td>
    </tr>
    @endforeach
    
  </tbody>
</table>
    </div>
    <div class="col-md-6">{!! Chart::display("id-highchartsnya", $output['stk_in_chart'],  ['jquery.js' => false,'format' => 'chart']) !!}</div>
  </div>
</div>


<hr>
<div class="row">
<h3>Averge Stock Price</h3>
  <div class="col-md-12">
    <div class="col-md-6">
      <table class="table datatable">
  <thead>
    <th>Month</th>
    <th>Average Purchase Price</th>
  </thead>
  <tbody>
    @foreach($output['costing'] as $stock)
    <tr>
      <td>{{$stock->mdt}}</td>
      <td>{{number_format($stock->sm)}}</td>
    </tr>
    @endforeach
    
  </tbody>
</table>
    </div>
    <div class="col-md-6">{!! Chart::display("id-highchartsn2ya", $output['costing_chart'], ['jquery.js' => false , 'highcharts.js' => false,'exporting.js'=> false,'format' => 'chart']) !!}</div>
  </div>
</div>

<hr>
<div class="row">
<h3>Invested on This Product</h3>
  <div class="col-md-12">
    <div class="col-md-6">
      <table class="table datatable">
  <thead>
    <th>Month</th>
    <th>Total Invested</th>
  </thead>
  <tbody>
    @foreach($output['amount'] as $stock)
    <tr>
      <td>{{$stock->mdt}}</td>
      <td>{{number_format($stock->sm)}}</td>
    </tr>
    @endforeach
    
  </tbody>
</table>
    </div>
    <div class="col-md-6">{!! Chart::display("id-highchartsn22ya", $output['amount_chart'], ['jquery.js' => false , 'highcharts.js' => false,'exporting.js'=> false,'format' => 'chart']) !!}</div>
  </div>
</div>

		
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

    <script type="text/javascript" src="{{url('products.json')}}?v={{versioning('products')}}"></script>


       <script type="text/javascript">

    $(document).ready(function(e){
    	var messageTopContent	 = 'Report date: {{($from)?(date("d-M-Y",strtotime($from))):"All time"}} to {{($to)?(date("d-M-Y",strtotime($to))):date("d-M-Y")}}';
    	var messageBottomContent = "\nReports by eAsasa ( 0345 4777487)";
          $(".datatable").DataTable({"deferRender": true
            // ,dom: 'Bfrtip'
    //         ,buttons: [
    //     'copy', {
    //             extend: 'excel',
    //             title: "Product Stock Overview",
    //             messageTop: messageTopContent,
    //             messageBottom: messageBottomContent
    //         },
    //         {
    //             extend: 'pdf',
    //             title: "Product Stock Overview",
    //             messageTop: messageTopContent,
    //             messageBottom: messageBottomContent
    //         },{
    //             extend: 'print',
    //             title: "Product Stock Overview",
    //             messageTop: messageTopContent,
    //             messageBottom: messageBottomContent
    //         }
    // ]
  });

            choices('.product');

            @if($request->product_id)
              $(".product").val({{$request->product_id}}).trigger("change");
            @endif
  
});


    function choices(identifier){
  // var warehouse_id = $("#warehouse_d").val();
      $(identifier).select2({
        
        data: products_json_d,
        // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 0,
    }).focus();
      $(identifier).on('select2:close', function (e) {
       
        //some ajax call to update <br><small id="stock_info'+count+'"></small>
        });

  }
</script>
@endsection