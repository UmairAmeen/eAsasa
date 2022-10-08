@extends('reporting')
@section('header')
<title>Forcasting</title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Artifical Intelligence Reporting</h3>
  <h4>Forcasting</h4>
  <form action="{{url()->current()}}" method="post">
    <div class="form-group">
      {{-- calendar month year --}}
      {{-- <div class="form-group">
        <label for="month" class="col-sm-2 control-label">Month</label>
        <div class="col-sm-10">
          <input type="date" class="form-control" id="month" name="month" format="mm/yyyy" value="{{ old('month') }}">
        </div>
      </div> --}}

  </form>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>
@endsection
@section('content')
<div class="row showback">
	<div class="col-md-12">
<br>
<h3>Product Sale / Order Prediction</h3>
<table class="table datatable">
  <thead>
    <th>Product id</th>
    <th>Product Name</th>
    <th>Product Brand</th>
    <th>Predicted Sale this Month</th>
    <th>Predicted Per Order Quantity this Month</th>
    <th>Current Sale this Month</th>
    <th>Target Meet %</th>
  </thead>
  <tbody>    
    @foreach($orders as $trans)
    <?php $prod = get_product($trans['product_id']); $cm = find_current_product_sale($trans['product_id'], $orders_now );  ?>
    <tr>
      <td>{{ $trans['product_id'] }}</td>
      <td>{{ $prod->name }}</td>
      <td>{{ $prod->brand }}</td>
      <td>{{ $trans['sm'] }}</td>
      <td>{{ number_format($trans['qty']) }}</td>
      <td>{{ $cm }}</td>
      <td>{{ number_format(($cm/$trans['sm'])*100,2) }}%</td>
    </tr> 
    @endforeach
  </tbody>
</table>    
	</div>
</div>
    <script type="text/javascript" src="{{asset('/assets/js/jquery.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/assets/js/datatable/dataTables.bootstrap.min.js')}}"></script>
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
    	var messageBottomContent = "\nReports by  eAsasa ( 0345 4777487)";
      $(".datatable").DataTable({
        "deferRender": true,
        dom: 'Bfrtip',
        "ordering": true,
        buttons: [
          'copy', {
              extend: 'excel',
              title: "Forcasting",
              messageTop: messageTopContentName+pdfNewLine+messageTopContent,
              messageBottom: messageBottomContent
            }, {
              extend: 'pdf',
              title: "Forcasting",
              messageTop: brandingPdf+pdfNewLine+pdfNewLine+messageTopContentName+pdfNewLine+messageTopContentPhone+pdfNewLine+messageTopContent,
              messageBottom: messageBottomContent
            }, {
              extend: 'print',
              title: "Forcasting",
              messageTop: brandingHtml+printNewLine+printNewLine+messageTopContentName+printNewLine+messageTopContentPhone+printNewLine+messageTopContent,
              messageBottom: messageBottomContent
            }
        ]
      });  
    });
</script>
@endsection