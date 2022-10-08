@extends('reporting')

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Accounts Receivable Aging Sheet Reporting</h3>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>

@endsection
@section('content')

<table class="table table-bordered datatable">
	<thead>
		<th>Customer Name</th>
		<th>Total A/R <br><small>Total Receivable from Customers</small></th>
		<th>0-30 Days <br><small>Receivable in last 30 Days</small></th>
		<th>30-60 Days <br><small>Receivable between 30 to 60 Days</small></th>
		<th>60-120 Days <br><small>Receivable between 60 to 120 Days</small></th>
        <th>120+ Days <br><small>Receivable for more than 120 Days</small></th>
	</thead>
	<tbody>
<?php    $total = $total_30 = $total_60 = $total_90 = $total_120 = 0; ?>
    @foreach($response as $key=> $trans)
    <?php
    $balance = $trans['total'];
    ?>
    @if($balance > 0)
    <?php 

    $total += $balance;
    if ($trans['30Days'] > 0) 
        $total_30 += $trans['30Days'];
    if ($trans['60Days'] > 0)  
        $total_60 += $trans['60Days']; 
    if ($trans['60Days+'] > 0) 
        $total_90 += $trans['60Days+'];
    if ($trans['120Days+'] > 0) 
        $total_120 += $trans['120Days+'];

     ?>
		<tr>
      <td>{{$key ." ". $trans['name']}}</td>
      <td><b><u>{{no_negative($balance) }}</u></b></td>
      <td>{{no_negative($trans['30Days']) }}</td>
      <td>{{no_negative($trans['60Days']) }}</td>
      <td>{{no_negative($trans['60Days+']) }}</td>
      <td>{{no_negative($trans['120Days+']) }}</td>
		</tr>
    @endif
    @endforeach
    <tfoot style="background-color: #00BCD4;font-weight: bold;color: white;font-size: 15px;">
      <tr>
        <td>Total</td>
        <td>{{ formating_price($total) }}</td>
        <td>{{ formating_price($total_30) }}</td>
        <td>{{ formating_price($total_60) }}</td>
        <td>{{ formating_price($total_90) }}</td>
        <td>{{ formating_price($total_120) }}</td>
      </tr>
    </tfoot>
	</tbody>
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

    $(document).ready(function(e){
    	var messageTopContent	 = 'Report date: {{date("d-M-Y")}}';
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
                title: "Accounts Receivable Aging",
                messageTop: messageTopContent,
                messageBottom: messageBottomContent
            },
            {
                extend: 'pdf',
                title: "Accounts Receivable Aging",
                messageTop: messageTopContent,
                messageBottom: messageBottomContent
            },{
                extend: 'print',
                title: "Accounts Receivable Aging",
                messageTop: messageTopContent,
                messageBottom: messageBottomContent
            },
            'colvis'
    ]});

    //   $(".customer_selector").select2({
    //     ajax: {
    //       url: "/pagination_customer_json/",
    //       dataType: 'json',
    //       delay: 50,
    //       data: function (params) {
    //         return {
    //           q: params.term, // search term
    //           page: params.page
    //         };
    //       },
    //       processResults: function (data, params) {
    //         // parse the results into the format expected by Select2
    //         // since we are using custom formatting functions we do not need to
    //         // alter the remote JSON data, except to indicate that infinite
    //         // scrolling can be used
    //         params.page = params.page || 1;

    //         return {
    //           results: data.items,
    //           pagination: {
    //             more: (params.page * 10) < data.total_count
    //           }
    //         };
    //       },
    //       // cache: true
    //     },
    //     // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
    //     minimumInputLength: 0,
    // });
  
});

</script>
@endsection