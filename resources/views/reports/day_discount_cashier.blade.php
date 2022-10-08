@extends('reporting')
@section('header')
<title>Day Sale Reporting</title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Day Discount and Cashier Reporting</h3>
  <h4>Report {{ date_format_app($from) }} to  {{ date_format_app($to) }}</h4>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>
@endsection
@section('content')
<div class="row showback">
	<form method="GET" action="{{url('reports')}}/day_discount">
    <div class="col-md-6">
      <div class="form-group">
        <label>From Date</label>
        <input type="date" class="form-control" name="from" value="{{($from)?:date('Y-m-d')}}">
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label>To Date</label>
        <input type="date" class="form-control" name="to" value="{{($to)?:date('Y-m-d')}}">
      </div>
    </div>
    <center>
      <div class="form-group">
        <button type="submit" class="btn btn-success">Search Data</button>
      </div>
    </center>			
  </form>
	<div class="col-md-12">
    <h3>Discount Details</h3>
    <table class="table datatable">
      <thead>
        <th>Date</th>
        <th>User</th>
        <th>Doc#</th>
        <th>Bill#</th>
        <th>Customer</th>
        <th>Invoice Total</th>
        <th>Discount</th>
        <th>Received</th>
        <th>Description</th>
      </thead>
      <tbody>
      <?php $count = $balance = $tt = $received = [] ?>
        @foreach($total_sale_invoices as $trans)
        <?php 
        $cashier = "Other";//findPaymentUserName($trans->id);
        $balance[($cashier)?:'Other'] += $trans->discount;
        $tt[($cashier)?:'Other'] += ($trans->total + $trans->discount);
        $count[($cashier)?:'Other']++;
        $rec = payment_for_invoice($trans->id);
        $received[($cashier)?:'Other'] += $rec;
        ?>
        <tr>
          <td>{{date('d-M-Y',strtotime($trans->date))}}</td>
          <td>{{isset($cashier)?$cashier:''}}</td>
          <td><a target="_blank" href="{{url('invoices')}}/{{$trans->id}}">{{$trans->id}}</a></td>
          <td>{{$trans->bill_number}}</td>
          <td>{{($trans->customer)?$trans->customer->name:""}}</td>
          <td>{{number_format($trans->total + $trans->discount)}}</td>
          <td>{{number_format($trans->discount)}}</td>
          <td>{{number_format($rec)}}</td>
          <td>{{$trans->description}}</td>
        </tr>
        @endforeach
      </tbody>
        <tfoot>      
        <tr>
          <td colspan="7"><h4>BALANCE</h4></td>
          <td><h4>{{number_format(array_sum($balance))}}</h4></td>
          <td><h4>{{number_format(array_sum($received))}}</h4></td>
        </tr>
        </tfoot>
    </table>
    <h5>Cashier Details</h5>
    <table class="table datatable">
      <thead>
        <th>Name</th>
        <th># invoices</th>
        <th>Total Sale</th>
        <th>Total Discount</th>
        <th>Balance</th>
        <th>Total Amount Received</th>
      </thead>
      <tbody>
        @foreach($balance as $key => $blnc)
        <tr>
          <td>{{$key}}</td>
          <td>{{ $count[$key] }}</td>
          <td>{{ number_format( $tt[$key] ) }}</td>
          <td>{{number_format($blnc) }}</td>
          <td>{{number_format( $tt[$key] - $blnc )}}</td>
          <td>{{ number_format($received[$key]) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>    
	</div>
</div>
<script type="text/javascript" src="{{asset('/assets/js/jquery.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/buttons.colVis.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.buttons.js')}}?v=1"></script>
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
  $(document).ready(function(e) {
    const brandingHtml = "<h1>" + company + "</h1><address><strong>" + address + "<br><abbr>Phone:</abbr>" + phone + "</address>";
    const brandingPdf = company + "\n" + address + "\nPhone: " + phone;
    var messageTopContentName = "";
    var messageTopContentPhone = "";
    var pdfNewLine = "\n";
    var printNewLine = "<br>";
    var messageTopContent	 = 'Report date: {{ date_format_app($from) }} to  {{ date_format_app($to) }}';
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
          title: "Day Discount & Cashier Report",
          messageTop: messageTopContentName+pdfNewLine+messageTopContent,
          messageBottom: messageBottomContent,
          exportOptions: {columns: ':visible'}
        }, {
          extend: 'pdf',
          title: "Day Discount & Cashier Report",
          messageTop: brandingPdf+pdfNewLine+pdfNewLine+messageTopContentName+pdfNewLine+messageTopContentPhone+pdfNewLine+messageTopContent,
          messageBottom: messageBottomContent,
          exportOptions: {columns: ':visible'}
        }, {
          extend: 'print',
          title: "Day Discount & Cashier Report",
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