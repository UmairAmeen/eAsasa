@extends('reporting')
@section('header')
<title>Receivable</title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Receivable</h3>
	<h4>Report All Time</h4>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>
@endsection
@section('content')
<div class="row showback">
	<div class="col-md-12">
    <br>
    <h3>Receivable Details</h3>
    <table class="table datatable">
      <thead>
        <th>#id</th>
        <th>Reg #</th>
        <th>Customer Name</th>
        <th>Last Paid On</th>
        <th>Balance</th>
      </thead>
      <tbody>
        <?php $balance = 0 ?>
        @foreach($blnc as $trans)
        <?php $balance+= $trans['balance']; ?>
        <tr>
          <td>{{$trans['customer']->id}}</td>
          <td>{{$trans['customer']->registeration_number}}</td>
          <td>{{$trans['customer']->name}}</td>
          <td>{{($trans['last_paid']>0)?app_date_format($trans['last_paid']):"N/A"}}</td>
          <td><a href="{{url('reports/balance_sheet')}}?from=&to=&customer_id={{$trans['customer']->id}}" target="_blank">{{number_format($trans['balance'])}}</a></td>
        </tr> 
        @endforeach
      </tbody>
      <tfoot>        
        <tr>
          <td>Total</td>
          <td></td>
          <td></td>
          <td></td>
          <td>{{number_format($balance)}}</td>
        </tr>
      </tfoot>
    </table>    
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
  $(document).ready(function(e) {
    const brandingHtml = "<h1>" + company + "</h1><address><strong>" + address + "<br><abbr>Phone:</abbr>" + phone + "</address>";
    const brandingPdf = company + "\n" + address + "\nPhone: " + phone;
    var messageTopContentName = "";
    var messageTopContentPhone = "";
    var pdfNewLine = "\n";
    var printNewLine = "<br>";
    var messageTopContent	 = 'Report date: All Time';
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
          title: "Receivable",
          messageTop: messageTopContentName+pdfNewLine+messageTopContent,
          messageBottom: messageBottomContent
        }, {
          extend: 'pdf',
          title: "Receivable",
          messageTop: brandingPdf+pdfNewLine+pdfNewLine+messageTopContentName+pdfNewLine+messageTopContentPhone+pdfNewLine+messageTopContent,
          messageBottom: messageBottomContent
        },{
          extend: 'print',
          title: "Receivable",
          messageTop: brandingHtml+printNewLine+printNewLine+messageTopContentName+printNewLine+messageTopContentPhone+printNewLine+messageTopContent,
          messageBottom: messageBottomContent
        },
            'colvis'
      ]
    });
  });
</script>
@endsection