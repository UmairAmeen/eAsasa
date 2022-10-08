@extends('reporting')
@section('header')
<?php
// If arrives here, is a valid user.
// echo "<p>Welcome $user.</p>";
// echo "<p>Congratulation, you are into the system.</p>";
?>
<title>Shipping Amount Report</title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Shipping Report</h3>
    <form action="{{url()->current()}}">
        {{-- add to and from date with submit button --}}
        <div class="col-md-4">
            <div class="form-group">
                <label for="from">From</label>
                <input type="date" class="form-control" id="from" name="from" value="{{$from}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="to">To</label>
                <input type="date" class="form-control" id="to" name="to" value="{{$to}}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="submit"></label>
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
        </div>
    </form>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>
@endsection
@section('content')
<div class="row showback">
	<div class="col-md-12">
<br>
<h3>Shipping Details</h3>
<table class="table datatable">
  <thead>
    <th>Invoice #</th>
    <th>Bill #</th>
    <th>Invoice Date</th>
    <th>Total Shipping</th>
  </thead>
  <tbody>
    <?php $balance = 0 ?>
    @foreach($total_sale_tax as $trans)
    <?php 
    
    $balance+= $trans->shipping; 
   
    ?>
    <tr>
      <td>{{$trans->id}}</td>
      <td>{{$trans->bill_number}}</td>
      <td>{{date_format_app($trans->date)}}</td>
      <td>{{number_format($trans->shipping)}}</td>
      
    </tr> 
    @endforeach
  </tbody>
  <tfoot>    
    <tr>
      <td>Total ({{count($total_sale_tax)}})</td>
      <td></td>
      <td>{{number_format($balance)}}</td>
    </tr>
  </tfoot>
</table>
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
    	var messageTopContent	 = 'Shipping Report';
    	var messageBottomContent = "\nReports by eAsasa (0345 4777 487)";
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
            title: "Shipping",
            messageTop: messageTopContentName+pdfNewLine+messageTopContent,
            messageBottom: messageBottomContent
          }, {
            extend: 'pdf',
            title: "Shipping",
            messageTop: brandingPdf+pdfNewLine+pdfNewLine+messageTopContentName+pdfNewLine+messageTopContentPhone+pdfNewLine+messageTopContent,
            messageBottom: messageBottomContent
          },{
            extend: 'print',
            title: "Shipping",
            messageTop: brandingHtml+printNewLine+printNewLine+messageTopContentName+printNewLine+messageTopContentPhone+printNewLine+messageTopContent,
            messageBottom: messageBottomContent
          },
          'colvis'
        ]
      });
    });
</script>
@endsection