@extends('layout')

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/choices.min.css')}}">
<!-- <link href="{{asset('assets/css/bootstrap-datepicker.css')}}" rel="stylesheet"> -->
@endsection

@section('header')
<div class="row">
    <div class="col-md-4">
    <center>
        <a href="#addproduct" id="add_sale" data-toggle="modal">
          <div  class="col-md-6 col-sm-6 box0">
            <div class="box1">
              <span class="fa fa-plus"></span>
                <h3>Add Purchase Price</h3>
            </div>
            <p>Add New Purchase Price of Product</p>
          </div>
          </a></center>
    </div>
    
</div>

    <div class="page-header clearfix">

        <h3>
            <i class="fa fa-file-invoice-dollar"></i> Price Record Manager
            <!-- <a href="/clearcache" class="btn btn-xs btn-default">Refresh Data</a> -->
        </h3>
        <div id="log"></div>
        <input type="hidden" value="delete" name="operation">
    </div>
@endsection

@section('content')
      <div class="content-panel">
    <div class="row">

        <div class="col-md-12">
            
        <!-- <button type="submit" class="btn btn-danger">Delete</button> -->
                <table class="table table-condensed table-striped stocks_listing" style="width: 100%!important;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th class="text-right">OPTIONS</th>
                        </tr>
                      </thead>
                      <tbody>
                
                      </tbody>
                  </table>
            

        </div>
        </div>
    </div>
                
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" id="addproduct" class="modal fade" data-backdrop="static">
        <div class="modal-dialog" style="width: 80%">
         <div class="modal-content col-md-12">

          <div class="col-md-12">
          <div class="pull-right"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
            <div class="content-panel">
               <form id="product_add_form" action="{{ route('price_record.store') }}" >
              <div class="multiRowInput">
              <center><h1 id="modal_title">Purchase Price Update</h1></center>
              <div id="log"></div>
              
            <div class="col-md-4">
            
            </div>
            
            
              <table class="table table-bordered table-striped table-hover">

              <thead>
                <tr>
                  <th>Date</th>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Option</th>
                </tr>
              </thead>
              <tbody id="appendMe">
             
                <tr class="parent_row">
                   
                </tr>

                </tbody>
                </table>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
              </div>
            </div>          
          </div>
          <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
          <center>
            <button type="submit_btn" class="btn btn-primary btn-lg" >Add Price Record</button>
                </form>
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
function box(count) {return  '<tr class="parent_row">\
<td><input type="date" name="date[]" id="date'+count+'" class="form-control" placeholder="Purchase Price Date" value="{{date("Y-m-d")}}" required></td>\
<td><select name="product_id[]" class="form-control product'+count+'" placeholder="Product Name" required></select></td>\
<td><input type="text" name="price[]" id="price'+count+'" class="form-control" placeholder="Purchase Price" onkeydown="keyDown(event, this)" required></td>\
<td><a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td>\
</tr>';
}


function clean(e, ele)
{
  // var box = '<tr class="parent_row"><td><select name="product_id[]" class="form-control product'+count+'" placeholder="Product Name"></select><br><small id="stock_info'+count+'"></small></td><td><input type="text" name="quantity[]" id="qty'+count+'" class="form-control" placeholder="Quantity" onkeydown="keyDown(event, this)"></td><td><a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td></tr>';
  var row = $(ele).closest('tr');
  var boss = $("tbody#appendMe").children();
  if (boss.length < 2)
  {
        var xo = $("#appendMe").append(box(count));
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
    
    var xo = $("#appendMe").append(box(count));
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
  // console.log(keyCode);
  if (keyCode == 9) { 
    // count += 1;
    e.preventDefault(); 
    $(me).closest('td').find('input').focus();
  } 
}





function choices(identifier){
  var warehouse_id = $("#warehouse_d").val();
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
          // cache: true
        },
        // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 0,
    });
      $(identifier).on('select2:close', function (e) {
        $(identifier).closest("input[name^=price]").focus();
        //some ajax call to update <br><small id="stock_info'+count+'"></small>
         
});

  }

  function clearAll()
  {
    $("#appendMe").empty();
    $("#appendMe").html(box(0));
    choices('.product0');
  }

$('.stocks_listing').dataTable( {
  "ajax": "/price_record_dt",
  "processing": true,
  "serverSide": true,
  "order": [[1,'desc']],
   "columns": [
            { "data": "id", "orderable": false, "searchable": false},
            {"data":'date'},
            {"data":"product.name"},
            {"data":"price"},
            { "data": "options", "orderable": false, "searchable": false }

        ]
        // "fnDrawCallback": function (oSettings) {
        //   del_init()
        // }
  } );

  $(document).ready(function() {

  var date = new Date();
  var day = date.getDate();
  var monthIndex = date.getMonth() + 1;
  var year = date.getFullYear();

  // $('.date-picker').val(day+"-"+monthIndex+"-"+year);// = new Date();
  $('#addproduct').on('shown.bs.modal', function() {
        clearAll();
    })
  $.fn.dataTable.ext.errMode = 'none';
});
</script>
@endsection