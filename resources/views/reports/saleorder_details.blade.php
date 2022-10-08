@extends('reporting')
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchbuilder/1.0.0/css/searchBuilder.dataTables.min.css">
@endsection
@section('header')
<title>Sale Order Reporting</title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Sale Order Reporting</h3>
	<h4>Report {{ date_format_app($from) }} to  {{ date_format_app($to) }}</h4>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
  <br>
</div>

@endsection
@section('content')
<div class="row showback">

	<form method="GET" action="{{url('reports')}}/saleorders_details">

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


<h3>Sale Order Details</h3>
<table class="table datatable">
  <thead>
    <th>Order #</th>
    <th>Bill #</th>
    <th>Booking Date</th>
    <th>Customer</th>
    <th>Phone</th>
    <th>Address</th>
    <th>Product Name</th>
    <th>Description</th>
    <th>Related To</th>
    <th>Quantity</th>
    <th>Total Worth</th>
    <th>Advance</th>
    <th>Sale Person</th>
    <th>Supplier</th>
    <th>Source</th>
    <th>Status</th>
    <th>Delivery Date</th>
    <th>Balance</th>
  </thead>
  <tbody>

    <?php $balance = 0 ?>
    @foreach($sale_orders as $trans)
    <?php $balance += $trans->invoiceTotal; ?>
    <tr>
      <td><a target="_blank" href="{{url('invoices')}}/{{$trans->invoice_idd}}">{{$trans->invoice_idd}}</a></td>
      <td>{{ $trans->bill_number }}</td>
      <td>{{ date('d-M-Y',strtotime($trans->invoiceDate)) }}</td>
      <td>{{ $trans->cName }}</td>
      <td>{{ $trans->cPhone }}</td>
      <td>{{ $trans->cAddress }}</td>
      <td>{!! $trans->pName !!}</td>
      <td>{{ $trans->notes }}</td>
      <td>{{ $trans->invoiceRelatedTo }}</td>
      <?php
        $qty = explode(',',$trans->prod_quantity);
        $rounded = [];
        if($trans->unit != 3){
          foreach ($qty as $key => $value) {
            $rounded[$key] = number_format($value,0);
            $qty_sum += $rounded[$key];
				}
        $trans->prod_quantity = implode(', ',$rounded); 
        }
      ?>
      <td>{{ $trans->prod_quantity }}</td>
      <td>{{ $trans->total + 0 }}</td>
      <td>{{ $trans->advance + 0 }}</td>
      <td>{{ $trans->invoiceSales_person }}</td>
      <?php
        $supplier = get_supplier_of_product($trans->product_id)
      ?>
      <td>{{ $supplier->name?$supplier->name:'-' }}</td>
      <td>{{ $trans->source }}</td>
      <?php
	  	$status_array = array("PENDING", "ACTIVE", "null","QUOTATION","COMPLETED");
      ?>
      <td>{{ $status_array[$trans->status] }}</td>
      <td>{{ date('d-M-Y',strtotime($trans->delivery_date)) }}</td>
      <td>{{ $trans->balance }}</td>
    </tr>
    <?php
    $total_worth += $trans->total;
    $total_qty = $qty_sum;
    $total_advance += $trans->advance;
    $total_balance += $trans->balance;
    ?>
    @endforeach

  </tbody>
  <tfoot>  
        <tr>
            <td colspan="8"><h4> TOTAL </h4></td>
            <td><h4>{{number_format(abs($total_qty))}}</h4></td>
            <td><h4>{{number_format(abs($total_worth))}}</h4></td>
            <td><h4>{{number_format(abs($total_advance))}}</h4></td>
            <td colspan="5"></td>
            <td><h4>{{number_format(abs($total_balance))}}</h4></td>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.0.0/js/dataTables.searchBuilder.min.js"></script>
    <script type="text/javascript" src="{{asset('/assets/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">

    $(document).ready(function(e) {
      $.fn.dataTable.moment( 'DD-MMM-YYYY' );
      var brandingHtml = "<h1>{{getSetting('company')}}</h1>\
                                <address>\
                                <strong>{{getSetting('address')}}<br>\
                                <abbr>Phone:</abbr>{{getSetting('phone')}}\
                                </address>";
      var brandingPdf = "{{getSetting('company')}}\n\
      {{getSetting('address')}}\n\
      Phone: {{getSetting('phone')}}";
      var messageTopContentName = "";
      var messageTopContentPhone = "";
      var pdfNewLine = "\n";
      var printNewLine = "<br>";

    	var messageTopContent	 = 'Day Sale Report date: {{ date_format_app($from) }} to  {{ date_format_app($to) }}';
    	var messageBottomContent = "\nReports by eAsasa ( 0345 4777487)";
      $("tbody tr:odd").css("background-color", "#f2f2f2");
      $("thead").css("background-color", "#adadad");

          $(".datatable").DataTable({
            "deferRender": true,
            dom: 'QBfrtlip',
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
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                title: "Day Sale Report",
                messageTop: brandingPdf+pdfNewLine+pdfNewLine+messageTopContentName+pdfNewLine+messageTopContentPhone+pdfNewLine+messageTopContent,
                messageBottom: messageBottomContent,
                exportOptions: {
                    columns: ':visible'
                }
            },{
                extend: 'print',
                title: "Day Sale Report",
                messageTop: brandingHtml+printNewLine+printNewLine+messageTopContentName+printNewLine+messageTopContentPhone+printNewLine+messageTopContent,
                messageBottom: messageBottomContent,
                exportOptions: {
                    columns: ':visible'
                }
            },'colvis'
    ]});

  
});

</script>
@endsection