@extends('reporting')
@section('header')
<title>Log Report</title>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/buttons.dataTables.min.css')}}">
<div class="col-md-12 text-center">
	<h3>Log Report</h3>
	<h4>Report {{ app_date_format($from) }}</h4>
	<a href="{{url('reports')}}" class="btn btn-primary noPrint">Back to All Reports</a>
</div>
@endsection
@section('content')
<div class="row showback">
 <form method="GET" action="{{url('reports')}}/log_report">
      <div class="form-group">
        <label>Date</label>
        <input type="date" class="form-control" name="from" value="{{$from}}">
      </div>     
      <div class="form-group">
        <button type="submit" class="btn btn-success">Search Data</button>
      </div>      
    </form>
	<div class="col-md-12">
    <br>
    <h3>Activity Log Details</h3>
    <table class="table">
      <thead>
        <th>#id</th>
        <th>Details</th>
        <th>Added On</th>
      </thead>
      <tbody>
        @if(count($data['customers']))
        <tr>
          <td colspan="3" style="background: #2196F3; color: #fff"><h4>Customers</h4></td>
        </tr>
        @endif
        @foreach($data['customers'] as $key => $entry)
        <tr>
        <td>{{$entry->id}}</td>
        <td>{{$entry->name}}</td>
        <td><a href="{{ url('customers') }}/{{$entry->id}}/edit" target="_blank">View Entry</a></td>
        </tr> 
        @endforeach
          @if(count($data['suppliers']))
        <tr>
          <td colspan="3" style="background: #2196F3; color: #fff"><h4>Suppliers</h4></td>
        </tr>
        @endif
        @foreach($data['suppliers'] as $key => $entry)
        <tr>
        <td>{{$entry->id}}</td>
        <td>{{$entry->name}}</td>
        <td><a href="{{ url('suppliers') }}/{{$entry->id}}/edit" target="_blank">View Entry</a></td>
        </tr> 
        @endforeach
          @if(count($data['products']))
        <tr>
          <td colspan="3" style="background: #2196F3; color: #fff"><h4>Products</h4></td>
        </tr>
        @endif
        @foreach($data['products'] as $key => $entry)
        <tr>
        <td>{{$entry->id}}</td>
        <td>{{$entry->name}} {{$entry->brand}} | Price: {{$entry->salePrice}}</td>
        <td><a href="{{ url('products') }}/{{$entry->id}}" target="_blank">View Entry</a></td>
        </tr> 
        @endforeach
          @if(count($data['transactions']))
        <tr>
          <td colspan="3" style="background: #2196F3; color: #fff"><h4>Transactions</h4></td>
        </tr>
        @endif
        @foreach($data['transactions'] as $key => $entry)
        <tr>
        <td>{{$entry->id}}</td>
        @if($entry->customer)
        <td style="background-color: aliceblue;">Customer: {{$entry->customer->name}} | Type: {{$entry->type}} | Amount: {{$entry->amount}} | Bank: {{$entry->bank}} | Release Date:{{$entry->release_date}}</td>
        @elseif ($entry->supplier)
        <td style="background-color: #f0fff1;">Supplier: {{$entry->supplier->name}} | Type: {{$entry->type}} | Amount: {{$entry->amount}} | Bank: {{$entry->bank}} | Release Date:{{$entry->release_date}}</td>
        @else
        <td>Type: {{$entry->type}} | Amount: {{$entry->amount}} | Bank: {{$entry->bank}} | Release Date:{{$entry->release_date}}</td>    
        @endif
        <td><a href="{{ url('transactions') }}/{{$entry->id}}" target="_blank">View Entry</a>
          @if($entry->invoice)
          |
          <a href="{{ url('invoices') }}/{{$entry->invoice_id}}" target="_blank">View Invoice</a>
          @endif
        </td>
        </tr> 
        @endforeach
          @if(count($data['warehouses']))
        <tr>
          <td colspan="3" style="background: #2196F3; color: #fff"><h4>Warehouse</h4></td>
        </tr>
        @endif
        @foreach($data['warehouses'] as $key => $entry)
        <tr>
        <td>{{$entry->id}}</td>
        <td>{{$entry->name}}</td>
            <td><a href="{{ url('warehouses') }}/{{$entry->id}}" target="_blank">View Entry</a></td>
        </tr> 
        @endforeach
          @if(count($data['sale_orders']))
        <tr>
          <td colspan="3" style="background: #2196F3; color: #fff"><h4>Sale Order</h4></td>
        </tr>
        @endif
        @foreach($data['sale_orders'] as $key => $entry)
        <tr>
        <td>{{$entry->id}}</td>
        <td>Invoice #{{$entry->id}} | Customer: {{$entry->customer->name}} | Total:  {{$entry->total}}</td>
            <td><a href="{{ url('invoices') }}/{{$entry->id}}" target="_blank">View Entry</a></td>
        </tr> 
        @endforeach
          @if(count($data['purchases']))
        <tr>
          <td colspan="3" style="background: #2196F3; color: #fff"><h4>Purchase</h4></td>
        </tr>
        @endif
        @foreach($data['purchases'] as $key => $entry)
        <tr>
        <td>{{$entry->id}}</td>
        <td>Invoice #{{$entry->id}} | Supplier: {{$entry->supplier->name}} | Total:  {{$entry->total}}</td>
            <td><a href="{{ url('invoices') }}/{{$entry->id}}" target="_blank">View Entry</a></td>
        </tr> 
        @endforeach
          @if(count($data['refunds']))
        <tr>
          <td colspan="3" style="background: #2196F3; color: #fff"><h4>Refund</h4></td>
        </tr>
        @endif
        @foreach($data['refunds'] as $key => $entry)
        <tr>
        <td>{{$entry->id}}</td>
        <td>Invoice #{{$entry->id}} | Customer: {{$entry->customer->name}} | Total:  {{$entry->total}}</td>
            <td><a href="{{ url('invoices') }}/{{$entry->id}}" target="_blank">View Entry</a></td>
        </tr> 
        @endforeach
          @if(count($data['sales']))
        <tr>
          <td colspan="3" style="background: #2196F3; color: #fff"><h4>Sales</h4></td>
        </tr>
        @endif
        @foreach($data['sales'] as $key => $entry)
        <tr>
        <td>{{$entry->id}}</td>
        <td>Invoice #{{$entry->id}} | Customer: {{$entry->customer->name}} | Total:  {{$entry->total}}</td>
        <td><a href="{{ url('invoice/show') }}/{{$entry->id}}" target="_blank">View Entry</a></td>
        </tr> 
        @endforeach
          @if(count($data['stocks']))
        <tr>
          <td colspan="3" style="background: #2196F3; color: #fff"><h4>Stocks</h4></td>
        </tr>
        @endif
        @foreach($data['stocks'] as $key => $entry)
        <tr>
        <td>{{$entry->id}}</td>
        <td>{{$entry->product->name}} | type: {{$entry->type}} | stock: {{$entry->quantity}}</td>
        <td><a href="{{ url('stock') }}" target="_blank">View Entry</a></td>
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
    var messageBottomContent = "\nReports by eAsasa ( 0345 4777487)";
    $(".datatable").DataTable({
      "deferRender": true,
      dom: 'Bfrtip',
      "ordering": true,
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
        }, {
          extend: 'print',
          title: "Receivable",
          messageTop: brandingHtml+printNewLine+printNewLine+messageTopContentName+printNewLine+messageTopContentPhone+printNewLine+messageTopContent,
          messageBottom: messageBottomContent
        }
      ]
    });
  });
</script>
@endsection