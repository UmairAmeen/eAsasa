@extends('reporting')
@section('header')
<title> Revenue Report </title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3> Revenue Report </h3>
	<h4>Report {{ date_format_app($from) }} to  {{ date_format_app($to) }}</h4>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
  <br>
</div>
@endsection
@section('content')
<div class="row showback">
	<form method="GET" action="{{url('reports')}}/revenue_report">
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
<h3> Revenue Details </h3>
<table class="table datatable">
  <thead>
    <th> SR# </th>
    <th>Doc#</th>
    <th> Date </th>
    <th> Type </th>
    <th> Debit [In] </th>
  <th> Credit [Out] </th>
  <th>Mode</th>
    <th> Description </th>
  </thead>
  <tbody>

   <?php $count = 1;  $profitloss_calculation = 0; ?>
    @foreach($total_invoices as $invo)
    <?php
      $in_amount = '';
      $out_amount = '';
      //debit of suppliers will not be added
      if(($invo->type=='sale' || $invo->type=='in') && !$invo->supplier_id)
      {$in_amount = $invo->amount; $in_total += $invo->amount;  }
      //credit of customers will not be added
      if(($invo->type=='purchase' || $invo->type=='out' || $invo->type=='expense') && !$invo->customer_id )
      { $out_amount = $invo->amount; $out_total += $invo->amount;  }
    ?>
    <tr>
      <td>{{ $count }}</td>
      <td><a href="{{url('transactions/'.$invo->id)}}" target="_blank">{{$invo->id}}</a></td>
      <td>{{ date_format_app($invo->date) }}</td>
      <td> {{($invo->type=="out")?"credit":(($invo->type=="expense")?"expense":"debit")}}  </td>
      <td>{{ number_format($in_amount)}}</td>
      <td>{{ number_format($out_amount)}} </td>
      <td>{{$invo->payment_type}}</td>
      <td> {{$invo->description}}  </td>
    </tr>
   <?php $count++; ?>
    @endforeach
  </tbody>
    <?php $profitloss_calculation = $in_total-$out_total; ?>
    <tfoot>  
        <tr>
            <td colspan="4"><h4> TOTAL </h4></td>
            <td><h4>{{number_format(abs($in_total))}}</h4></td>
            <td><h4>{{number_format(abs($out_total))}}</h4></td>
            <td></td>
            <td>  
               <h4 class="{{($profitloss_calculation<0)?'red':'green'}}"> {{ amount_cdr($profitloss_calculation,true) }} </h4>
            </td>
        </tr>
    </tfoot>
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
    	var messageTopContent	 = 'Revenue Report date: {{ date_format_app($from) }} to  {{ date_format_app($to) }}';
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
              title: "Revenue Report",
              messageTop: messageTopContentName+pdfNewLine+messageTopContent,
              messageBottom: messageBottomContent,
              exportOptions: { columns: ':visible' }
            }, {
              extend: 'pdf',
              title: "Revenue Report",
              messageTop: brandingPdf+pdfNewLine+pdfNewLine+messageTopContentName+pdfNewLine+messageTopContentPhone+pdfNewLine+messageTopContent,
              messageBottom: messageBottomContent,
              exportOptions: { columns: ':visible' }
            }, {
              extend: 'print',
              title: "Revenue Report",
              messageTop: brandingHtml+printNewLine+printNewLine+messageTopContentName+printNewLine+messageTopContentPhone+printNewLine+messageTopContent,
              messageBottom: messageBottomContent,
              exportOptions: { columns: ':visible' }
            },
            'colvis'
        ]
    });
  });
</script>
@endsection