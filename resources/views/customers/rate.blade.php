@extends('layout')
@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="glyphicon glyphicon-user"></i> Customer: {{$customer->name}}
            <a class="btn btn-success pull-right" href="#addproduct" data-toggle="modal"><i class="glyphicon glyphicon-plus"></i> Add New Product</a>
        </h1>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
                <div class="content-panel">

            @if($customer->rate->count())
                <table class="table table-condensed table-striped datatable">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Sale Price</th>
                            
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($customer->rate as $rate)
                            <tr>
                                <td>{{$rate->product->name. "-". $rate->product->brand}}</td>
                                <td>{{$rate->salePrice}}</td>
                                
                                <td class="text-right">
                                    
                                    <form action="/rates/{{$rate->id}}" method="POST" style="display: inline;" >
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return (confirm('Delete? Are you sure?'))"><i class="glyphicon glyphicon-trash"></i> Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif
            </div>
        </div>
    </div>

<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" id="addproduct" class="modal fade">
        <div class="modal-dialog" style="width: 80%">
         <div class="modal-content col-md-12">
          <div class="col-md-12">
            <div class="content-panel">
               <form id="product_add_form" action="/saverates/{{ $customer->id }}" >
              <div class="multiRowInput">
              <center><h1 id="modal_title">Product Prices</h1></center>
              <div id="log"></div>

              <table class="table table-bordered table-striped table-hover">

              <thead>
                <tr>
                  <th>Product</th>
                  <th class="numeric">Sale Price</th>
                  <th>Option</th>
                </tr>
              </thead>
              <tbody id="appendMe">
             
                <tr class="parent_row">
                   
                </tr>

                </tbody>
                </table>
                <input type="hidden" id="is_purchase" name="is_purchase" value="0">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
              </div>
                </form>
            </div>          
          </div>
          <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
          <center>
            <button id="btn_title" class="btn btn-primary btn-lg" onclick="addproducts()">Add Pricing</button>
            </center>
        </div>
        </div>
      </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript" src="{{asset('assets/js/choices.min.js')}}"></script>

<script type="text/javascript">
var count = 0;
var productselectbox;
function clean(e, ele)
{
    var box = '<tr class="parent_row"><td><select name="product_id[]" class="form-control product'+count+'" placeholder="Product Name"></select></td><td><input type="text" name="quantity[]" id="qty'+count+'" class="form-control" placeholder="Quantity" onkeydown="keyDown(event, this)"></td><td><a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td></tr>';
  var row = $(ele).closest('tr');
  var boss = $("tbody#appendMe").children();
  if (boss.length < 2)
  {
        var xo = $("#appendMe").append(box);
        xo.children().last().children().find("input").attr('onkeydown','keyDown(event, this)');
  }
  $(row).remove();
  choices('.product'+count);
  // debugger;
}

function keyDown(e, me)
{
  var keyCode = e.keyCode || e.which; 

  if (keyCode == 9) { 
    count += 1;
    e.preventDefault(); 
    var box = '<tr class="parent_row"> <td><select name="product_id[]" class="form-control product'+count+'" placeholder="Product Name" onkeydown="keyFocus(event, this)"></select></td><td><input type="text" id="qty'+count+'" name="sale_price[]" class="form-control qty'+count+'" placeholder="Custom Rate" onkeydown="keyDown(event, this)"></td><td><a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td></tr>';
    var xo = $("#appendMe").append(box);
    xo.children().last().children().find(".textbox").attr('onkeydown','keyDown(event, this)');
    // xo.children().last().children().first().find("select").focus();
    $(me).removeAttr('onkeydown');
    choices('.product'+count);
    // call custom function here
  } 
}
function keyFocus(e, me)
{
  var keyCode = e.keyCode || e.which; 
  console.log(keyCode);
  if (keyCode == 9) { 
    // count += 1;
    e.preventDefault(); 
    $(me).closest('td').find('input').focus();
  } 
}

function addproducts(){
    $("#product_add_form").submit();
}



function choices(identifier){
  // var warehouse_id = $("#warehouse_d").val();
      $(identifier).select2({
        ajax: {
          url: "/pagination_product_json/",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              page: params.page
            };
          },
          processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
              results: data.items,
              pagination: {
                more: (params.page * 10) < data.total_count
              }
            };
          },
          cache: true
        },
        // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 0,
    }).focus();
      $(identifier).on('select2:close', function (e) {
       $(identifier).blur();
        $.ajax({
          url: "/product_price_json/",
          data: {product_id: $(identifier).val()}}).done(function(data){
            $("#qty"+count).val(data);
        });
      $("#qty"+count).focus();
});

  }

  function clearAll()
  {
    $("#appendMe").empty();
    $("#appendMe").html('<tr class="parent_row"> <td><select name="product_id[]" class="form-control product" placeholder="Product Name" onkeydown="keyFocus(event, this)"></select></td><td><input type="text" id="qty0" name="sale_price[]" class="form-control qty0" placeholder="Custom Rate" onkeydown="keyDown(event, this)"></td><td><a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td></tr>');
    choices('.product');
  }



  $(document).ready(function() {

  var date = new Date();
  var day = date.getDate();
  var monthIndex = date.getMonth() + 1;
  var year = date.getFullYear();

  $('.date-picker').val(day+"-"+monthIndex+"-"+year);// = new Date();
  $(".datatable").show();
  $('#addproduct').on('shown.bs.modal', function() {
        clearAll();
    })
});
</script>
@endsection