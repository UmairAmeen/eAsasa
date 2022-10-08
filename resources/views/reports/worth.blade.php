@extends('reporting')
@section('header')
<?php
// If arrives here, is a valid user.
// echo "<p>Welcome $user.</p>";
// echo "<p>Congratulation, you are into the system.</p>";
?>
<title>Stock Worth Report</title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Stock Worth Report</h3>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>
@endsection
@section('content')
<div class="row showback">
	<div class="col-md-12">
<br>
<h3>Stock Details</h3>
<table class="table datatable">
  <thead>
    <th>#id</th>
    <th>Product Name</th>
    <th>Product Brand</th>
    @if(strpos(session()->get('settings.products.optional_items'), 'size') !== false) 
      <th>Product Size</th>
    @endif
    <th>Total Available Stock</th>
    <th>Last Purchased Price</th>
    <th>Total Sale Price</th>
    <th>Total Purchase Price</th>
  </thead>
  <tbody>
    <?php $balance = 0 ?>
    @foreach($all as $trans)
    <?php 
    $ptotal = $trans->stock * $trans->purchase_price;
    $balance+= $ptotal; 
    $sTotal = $trans->stock * $trans->salePrice;
    $totalSaleAvailable+= $sTotal;
    ?>
    <tr>
      <td>{{$trans->id}}</td>
      <td>{{$trans->name}}</td>
      <td>{{$trans->brand}}</td>
      @if(strpos(session()->get('settings.products.optional_items'), 'size') !== false) 
        <td>{{$trans->size}}</td>
      @endif
      <td>{{$trans->stock}}</td>
      <td>{{$trans->purchase_price}}</td>
      <td>{{$trans->salePrice }}</td>
      <td>{{number_format($ptotal)}}</td>
    </tr> 
    @endforeach
  </tbody>
  <tfoot>    
    <tr>
      <td>Total</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td>{{number_format($totalSaleAvailable)}}</td>
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
    	var messageTopContent	 = 'Stock Worth Report';
    	var messageBottomContent = "\nReports by FireWorks Inventory";
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