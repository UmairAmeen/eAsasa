@extends('reporting')
@section('header')
<?php
// If arrives here, is a valid user.
// echo "<p>Welcome $user.</p>";
// echo "<p>Congratulation, you are into the system.</p>";
?>
<title>Purchase Dashboard</title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
  <h3>Purchase Dashboard Report</h3>
  <h4>Report Uptil {{($from) ? (date(session('settings.misc.date_format','d-M-Y'), strtotime($from))) : date(session('settings.misc.date_format','d-M-Y'))}} to {{($to) ? (date(session('settings.misc.date_format','d-M-Y'), strtotime($to))) : date(session('settings.misc.date_format','d-M-Y'))}}</h4>
  <form method="GET" action="{{url()->current()}}">
    <div class="form-group col-md-6">
        <label>From Date</label>
        <input type="date" class="form-control" name="from" value="{{$from}}">
      </div>
      
      <div class="form-group  col-md-6">
        <label>To Date</label>
        <input type="date" class="form-control" name="to" value="{{$to}}">
      </div>
      <input type="submit" class="btn btn-success" name="Generate Report">
    </form>
    <br><br>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>
@endsection
@section('content')
<div class="row showback">
	<div class="col-md-12">
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-heading">Suppliers</div>
                <div class="panel-body text-center">
                 <h2> {{count($suppliers)}}</h2>
                </div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading">Total Purchase</div>
                <div class="panel-body text-center">
                 <h2> {{check_negative($total_order_value)}}</h2>
                </div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading">Total Return</div>
                <div class="panel-body text-center">
                 <h2> {{check_negative($total_return_value)}}</h2>
                </div>
              </div>
            <div class="panel panel-default">
                <div class="panel-heading">Products Ordered</div>
                <div class="panel-body text-center">
                  <h2>  {{count($products)}}</h2>
                </div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading">Total Order Qty</div>
                <div class="panel-body text-center">
                 <h2> {{check_negative($total_order_count)}}</h2>
                </div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading">Total Return Qty</div>
                <div class="panel-body text-center">
                 <h2> {{check_negative($total_return_count)}}</h2>
                </div>
              </div>
              
        </div> 
        {{-- end col-md-3 --}}
        <h3>Supplier Stats</h3>
        <div class="col-md-8">
            <table class="table">
                <thead>
                    <th>#</th>
                    <th>Supplier Name</th>
                    <th>Item Ordered</th>
                    <th>Order Value</th>
                    <th>Item Return</th>
                    <th>Return Value</th>
                    <th>Return Analysis</th>
                </thead>
                <tbody>
                    @foreach ($suppliers as $id=> $supplier)
                        <tr>
                            <td>{{$id}}</td>
                            <td>{{$supplier['name']}}</td>
                            <td>{{check_negative($supplier['ordered_quantity'])}}</td>
                            <td>{{check_negative($supplier['total'])}}</td>
                            <td>{{check_negative($supplier['return_quantity'])}}</td>
                            <td>{{check_negative($supplier['return'])}}</td>
                            <td>{{check_negative($supplier['return_quantity']/$supplier['ordered_quantity']*100)}}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <br>
        <p></p>
        <div class="col-md-8">
            <h3>Order Stats</h3>
            <table class="table">
                <thead>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Item Ordered</th>
                    <th>Order Value</th>
                    <th>Item Return</th>
                    <th>Return Value</th>
                    <th>Return Analysis</th>
                </thead>
                <tbody>
                    @foreach ($products as $id=> $product)
                        <tr>
                            <td>{{$id}}</td>
                            <td>{{$product['name']}}</td>
                            <td>{{check_negative($product['quantity'])}}</td>
                            <td>{{check_negative($product['total'])}}</td>
                            <td>{{check_negative($product['return_qty'])}}</td>
                            <td>{{check_negative($product['return_total'])}}</td>
                            <td>{{check_negative($product['return_qty']/$product['quantity']*100)}}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
	</div>
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
  <script>
      $(".table").DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
      });
  </script>
 
@endsection