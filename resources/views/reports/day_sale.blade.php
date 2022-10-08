@extends('reporting')
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchbuilder/1.0.0/css/searchBuilder.dataTables.min.css">
@endsection
@section('header')
<title>Day Sale Reporting</title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Day Sale Reporting</h3>
	<h4>Report {{ date_format_app($from) }} to  {{ date_format_app($to) }}</h4>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
  <br>
</div>
@endsection
@section('content')
<div class="row showback">
	<form method="GET" action="{{url('reports')}}/day_sale">
    <div class="col-md-6">
        <div class="form-group">
        <label>From Date</label>
        <input type="date" class="form-control" name="from" value="{{($from)?:date('Y-m-1')}}">
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label>To Date</label>
        <input type="date" class="form-control" name="to" value="{{($to)?:date('Y-m-t')}}">
      </div>
    </div>
    <center>
      <div class="form-group">
        <button type="submit" class="btn btn-success">Search Data</button>
      </div>
    </center>			
  </form>
	<div class="col-md-12">
    <h3>Sale Details</h3>
    <table class="table datatable">
      @php
        $users = [];
      @endphp
      <thead>
        <th>Date</th>
        <th>Doc#</th>
        <th>Bill#</th>
        <th>Customer</th>
        <th>Sale (ex. Discount)</th>
        <th>Discount</th>
        <th>Description</th>
        <th>Balance</th>
        <th>User</th>
      </thead>
      <tbody>
      @php($exTotal = $totalDiscount = $balance = 0)
        @foreach($total_sale_invoices as $trans)
        @php
          $totalDiscount += $trans->discount;
          $balance += $trans->total;
          $exTotal += $trans->total + $trans->discount;
          if (!array_key_exists($trans->user, $users))
          {
            if (!$trans->user)
            {
              $trans->user = "unknown";
            }
            $users[$trans->user] = ['sales'=>0, 'discount'=>0];
          }
          $users[$trans->user]['sales'] += $trans->total;
          $users[$trans->user]['discount'] += $trans->discount;
        @endphp
        <?php  ?>
        <tr>
          <td>{{date('d-M-Y',strtotime($trans->date))}}</td>
          <td><a target="_blank" href="{{url('invoices')}}/{{$trans->id}}">{{$trans->id}}</a></td>
          <td>{{$trans->bill_number}}</td>
          <td>{{($trans->customer)?$trans->customer:""}}</td>
          <td>{{number_format($trans->total + $trans->discount)}}</td>
          <td>{{number_format($trans->discount)}}</td>
          <td>{{$trans->description}}</td>
          <td>{{number_format($balance)}}</td>
          <td>{{isset($trans->user)?$trans->user:''}}</td>
        </tr>
        @endforeach
      </tbody>
        <tfoot>
        <tr>
          <td colspan="3"><h4>BALANCE</h4></td>
          <td><h4>{{number_format(abs($exTotal))}}</h4></td>
          <td><h4>{{number_format(abs($totalDiscount))}}</h4></td>
          <td></td>
          <td><h4>{{number_format(abs($balance))}}</h4></td>
          <td></td>
        </tr>
        </tfoot>
    </table>
    <br>
    <h3>Sales Based on User</h3>
    <table class="table table-bordered">
      <thead>
        <tr>
          <td width="50px"><b>Users</b></td>
        @foreach($users as $name => $amount)
          <td>{{$name}}</td>
          @endforeach
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Sales excluding Discounts</td>
        @foreach ($users as $amount)
          <td>{{number_format($amount['sales'] + $amount['discount'])}}</td>
        @endforeach
      </tr>
      <tr>
        <td>Discounts</td>
      @foreach ($users as $amount)
        <td>{{number_format($amount['discount'])}}</td>
      @endforeach
    </tr>
    <tr>
      <td>Net Sale</td>
    @foreach ($users as $amount)
      <td>{{number_format($amount['sales'])}}</td>
    @endforeach
  </tr>
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
    var messageTopContent	 = 'Day Sale Report date: {{ date_format_app($from) }} to  {{ date_format_app($to) }}';
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
          title: "Day Sale Report",
          messageTop: messageTopContentName+pdfNewLine+messageTopContent,
          messageBottom: messageBottomContent,
          exportOptions: {columns: ':visible'}
        }, {
          extend: 'pdf',
          title: "Day Sale Report",
          messageTop: brandingPdf+pdfNewLine+pdfNewLine+messageTopContentName+pdfNewLine+messageTopContentPhone+pdfNewLine+messageTopContent,
          messageBottom: messageBottomContent,
          exportOptions: {columns: ':visible'}
        },{
          extend: 'print',
          title: "Day Sale Report",
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