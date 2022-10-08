@extends('reporting')
@section('header')
<?php
// If arrives here, is a valid user.
// echo "<p>Welcome $user.</p>";
// echo "<p>Congratulation, you are into the system.</p>";
?>
<title>Profit/Loss Report</title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Product Bundles Based Profit/Loss Report</h3>
  <h4>Report {{($from)?(date(session('settings.misc.date_format','d-M-Y'), strtotime($to))." To "):"Uptil"}} {{($to) ? (date(session('settings.misc.date_format','d-M-Y'), strtotime($to))) : date(session('settings.misc.date_format','d-M-Y'))}}</h4>
  <form method="GET" action="{{url()->current()}}">
    <div class="form-group">
      <label>From Date</label>
      <input type="date" class="form-control" name="from" value="{{$request->from}}">
    </div>
      <div class="form-group">
        <label>To Date</label>
        <input type="date" class="form-control" name="to" value="{{$request->to}}" required>
      </div>
      <input type="submit" name="Generate Report">
    </form>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>
@endsection
@section('content')
<div class="row showback">
	<div class="col-md-12">
  @php($balance = $invoice_total = $invoice_quantity = 0)
<br>
<h3>Profit/Loss Report Estimated Details - Product</h3>
<span>This report is calculated based on profit/loss margin between product purchase (batch / Purchase vise) and total sales Shorted By Product Bundles </span>

<table class="table datatable" style="display: none">
  <thead>
      <tr>
          <th>ID</th>
          <th>Group Name</th>
          <th>Details</th>
      </tr>
  </thead>
  <tbody>
      @foreach($productGroup as $product_group)
          <tr>
              <td>{{$product_group['id']}}</td>
              <td>{{$product_group['name']}}</td>
              <td>    
              <table class="table">
                <thead>
                  <th>Product Name</th>
                  <th>Total Sold Quantity</th>
                  <th>Average Sale Price</th>
                  <th>Average Purchase Price</th>
                  {{-- <th>Purchase History</th> --}}
                  <th>Profit/Loss Percentage</th>
                  <th>Total Profit/Loss based on Sale <-> Purchase Qty</th>
                </thead>
                <tbody>
                  @foreach($product_group['details'] as $key => $trans)
                    @php
                      $total = $trans['profit'];// - ($trans['quantity'] * $trans['purchase']);
                      $invoice_quantity += $trans['sale_quantity'];
                      $invoice_total += $trans['total_sale'];
                      $balance += $total;
                    @endphp
                  <tr>
                    <td>{{$trans['name']}}</td>
                    <td>{{number_format($trans['sale_quantity'])}}</td>
                    <td>{{ number_format($trans['sale'],2)}}</td>
                    <td>{{number_format($trans['purchase'],2)}}</td>
                    @php $profit_loss = ($trans['profit']/$trans['total_purchase'])*100; @endphp
                    <td style="color:{{($profit_loss>0)?"green":"red"}}">{{ number_format($profit_loss)."%"}}</td>
                    <td>{{number_format($trans['profit']) }}</td>
                  </tr>
                  @endforeach
                </tbody>   
              </table>
              </td> 
          </tr>
      @endforeach
  </tbody>
  <tfoot>    
    <tr>
      <td></td>
      <td></td>
      <td style="display: flex; justify-content: space-evenly;">
        <p>Total Sold QTY: <b>{{number_format($invoice_quantity)}}</b></p>
        <p>Sales: <b>{{number_format($invoice_total)}}</b></p>
        <p>Profit: <b>{{number_format($balance)}}</b></p>
      </td>
    </tr>
  </tfoot>
</table>

<center id="ctfooter">
  <table border="1" width="300px" style="font-size: 18px">
    <thead><tr><th colspan="2">Sales Summary</th></tr></thead>
    <tbody>
      <tr>
        <td style="text-align: left">Total Sale</td>
        <td style="text-align: right">{{ number_format($invoice_total) }}</td>
      </tr>
      <tr>
        <td style="text-align: left">Discount</td>
        <td style="text-align: right">{{ number_format($totalDiscount) }}</td>
      </tr>
      <tr>
        <td style="text-align: left">Net Sale</td>
        <td style="text-align: right">{{ number_format($invoice_total - $totalDiscount) }}</td>
      </tr>
    </tbody>
  </table>
  <table border="1" width="300px" style="font-size: 18px">
    <thead><tr><th colspan="2">Report Summary</th></tr></thead>
    <tbody>
      <tr>
        <td style="text-align: left">Profit/Loss</td>
        <td style="text-align: right">{{ number_format($balance) }}</td>
      </tr>
      <tr>
        @php($expense  = getExpense($from,$to))
        <td style="text-align: left">Expenses</td>
        <td style="text-align: right">{{ number_format($expense) }}</td>
      </tr>
      <tr>
        <td style="text-align: left">Net Profit/Loss</td>
        <td style="text-align: right">{{ number_format($balance - $expense) }}</td>
      </tr>
      <tr>
        <td style="text-align: left">Discount</td>
        <td style="text-align: right">{{ number_format($totalDiscount) }}</td>
      </tr>
      <tr>
        <td style="text-align: left">Total</td>
        <td style="text-align: right">{{ number_format(($balance - $totalDiscount - $expense)) }}</td>
      </tr>
@php($profit_percent = ($invoice_total == 0) ? 0 : (($balance - $totalDiscount - $expense) / $invoice_total) * 100)
      <tr>
          <td style="text-align: left">Result</td>
          <td><strong>
              {{ number_format(abs($profit_percent),2) }}% {{ (($balance - $expense - $totalDiscount) > 0) ? " PROFIT" : " LOSS" }}
            </strong></td>
        </tr>
    </tbody>
  </table>
</center>
<br><br>
<div id="message_show"><h4>Please Wait While we generate report</h4></div>    
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
    	var messageTopContent	 = 'Profit/Loss Report';
    	var messageBottomContent = $("#ctfooter").html()+"\nReports by eAsasa ( 0345 4777487)";
      $(".datatable").DataTable({
        "deferRender": true,
        dom: 'Blfrtip',
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
            title: "Profit/Loss",
            messageTop: messageTopContentName+pdfNewLine+messageTopContent,
            messageBottom: messageBottomContent
          }, {
            extend: 'pdf',
            title: "Profit/Loss",
            messageTop: brandingPdf+pdfNewLine+pdfNewLine+messageTopContentName+pdfNewLine+messageTopContentPhone+pdfNewLine+messageTopContent,
            messageBottom: messageBottomContent
          },{
            extend: 'print',
            title: "Profit/Loss",
            messageTop: brandingHtml+printNewLine+printNewLine+messageTopContentName+printNewLine+messageTopContentPhone+printNewLine+messageTopContent,
            messageBottom: messageBottomContent
          },
          'colvis'
        ]
      });
    });
</script>
@endsection