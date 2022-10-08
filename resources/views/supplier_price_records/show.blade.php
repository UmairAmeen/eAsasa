@extends('layout')
@section('header')
<div class="page-header">
        <h1>Supplier Price Records / Show #{{$supplier->id}}</h1>
        
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="showback">
                Name: {{$supplier->name}}<br>
                Phone: {{$supplier->phone}}<br>
                Address: {{$supplier->address}}<br>
                Comments: {{$supplier->description}}
            </div>
        </div>
        <div class="col-md-12">
            <form action="{{url('store_product_pricing')}}" class="showback" method="POST">
                <div id="log"></div>
                <input type="hidden" name="supplier_id" value="{{$supplier->id}}">
            <table id="sp_price_rec" class="table">
                <thead>
                    <th>Date</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Sale Price</th>
                    <th>Option</th>
                </thead>
                <tbody>
            @foreach($supplier_price_record as $value)
                    <tr>
                        <td>
                            <input type="date" name="date[]" class="form-control" value="{{$value->date}}"><span style="display: none">{{date('d M Y', strtotime($value->date))}}</span>
                            <input type="hidden" name="old_date[]" class="form-control" value="{{$value->date}}">
                        </td>
                        <td>{{getProductOptionalFields($value->product)}}<input type="hidden" name="product[]" value="{{$value->product_id}}"></td>
                        <td><input type="text" name="price[]" class="form-control" value="{{$value->price + 0}}">
                            <span style="display: none">{{$value->price}}</span>
                            <input type="hidden" name="old_price[]" value="{{$value->price + 0}}"></td>
                         <td>
                            <input type="text" name="sale_price[]" class="form-control" value="{{$value->product->salePrice + 0}}">
                            <input type="hidden" name="old_sale_price[]" value="{{$value->product->salePrice + 0}}">
                        </td>
                        <td>
                            <button type="button" onclick="setCurrentDate(this)" class="btn btn-default">Today Date</button>
                <a href="{{url('products')}}/{{$value->product_id}}/edit" class="btn btn-warning">Edit Product</a>

                            <!-- <form action="{{ route('supplier_price_records.destroy', $value->id) }}" method="POST" style="display: inline;" >
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="btn-group pull-right" role="group" aria-label="...">
                <button type="button" class="btn btn-danger" onclick="if(confirm('Delete? Are you sure?')) { return true } else {return false };">Delete <i class="glyphicon glyphicon-trash"></i></button>
            </div>
        </form> -->

                            <a href="{{url('supplier_price_records')}}/destoring/{{$value->id}}" class="btn btn-danger">Remove</a></td>
                    </tr>
            @endforeach
                </tbody>
            </table>
            <center>
                <button type="submit" class="btn btn-xl btn-success">Update Pricing</button>
            </center>
            </form>
                
                

            <a class="btn btn-link" href="{{ route('supplier_price_records.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>

        </div>
    </div>

@endsection
@section('scripts')

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
<script type="text/javascript">
      var brandingHtml = "";
      var brandingPdf = "";
      var messageTopContentName = "{{(isset($supplier))?$supplier->name:""}}";
      var messageTopContentPhone = "{{(isset($supplier))?$supplier->phone:""}}";
      var pdfNewLine = "\n";
      var printNewLine = "<br>";
        var messageTopContent    = '';
        var messageBottomContent = "Print Date: {{date('d M Y')}} \n  eAsasa ( 0345 4777487)";
    function setCurrentDate(ele)
    {
        $(ele).parentsUntil("tbody").find("input[type=date]").val("{{date('Y-m-d')}}");
        return false;
    }
    
    $('[data-command="removeitem"]').on('click', function (ele) {
        // body...
        ele.preventDefault();
        // console.log("Xk");
        // console.log(this);
        $.ajax({
          url: $(this).attr('href'),
          // data: {},
          method: "DELETE",
          dataType: "json"
        }).done(function(e) {
          
        }).error(function(e){
        });
    });
    $(document).ready(function(){
        $("#sp_price_rec").DataTable({
            "deferRender": true,
            dom: 'Blfrtip', 
            order: [[ 1, "asc" ]],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            stateSave: true,
            "stateLoadParams": function (settings, data) {
                data.search.search = "";
                data.length = 10;
                data.start = 0;
                data.order = [[ 1, "asc" ]];
            },
            
    buttons: [
        'copy', {
                extend: 'excel',
                title: "Supplier Pricing List {{$supplier->name}}",
                messageTop: messageTopContentName+pdfNewLine+messageTopContent,
                messageBottom: messageBottomContent,
                exportOptions: {
                    columns: [0,1,2,3],
                    // modifier: { order: [[ 1, "asc" ]] }
                }
            },
            {
                extend: 'pdf',
                title: "Supplier Pricing List {{$supplier->name}}",
                messageTop: brandingPdf+pdfNewLine+pdfNewLine+messageTopContentName+pdfNewLine+messageTopContentPhone+pdfNewLine+messageTopContent,
                messageBottom: messageBottomContent,
                exportOptions: {
                    columns: [0,1,2,3]
                }
            },{
                extend: 'print',
                title: "Supplier Pricing List {{$supplier->name}}",
                messageTop: brandingHtml+printNewLine+printNewLine+messageTopContentName+printNewLine+messageTopContentPhone+printNewLine+messageTopContent,
                messageBottom: messageBottomContent,
                exportOptions: {
                    columns: [0,1,2,3],
                    // modifier: { order: [[ 1, "asc" ]] }
                }
            }
    ]});
    });
</script>
@endsection