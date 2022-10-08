@extends('layout')

@section('header')
<div class="page-header clearfix">
    <h3>
        <i class="fa fa-cc"></i> Cheque Manager
    </h3>
    <div id="log"></div>
    <input type="hidden" value="delete" name="operation">
</div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
         <div class="col-md-6">
    <center>
        <a id="cheque_receive" onclick="showModal('in');return false;">
          <div  class="col-md-6 col-sm-6 box0">
            <div class="box1">
              <span class="fa fa-arrow-down"></span>
                <h3>Cheque Received</h3>
            </div>
            <p>Amount In, This will not add affect ledger</p>
          </div>
          </a></center>
    </div>

    <div class="col-md-6">
    <center>
        <a id="cheque_forward" onclick="showModal('out');return false;">
          <div  class="col-md-6 col-sm-6 box0">
            <div class="box1">
              <span class="fa fa-arrow-up"></span>
                <h3>Cheque Forward</h3>
            </div>
            <p>Amount Out, This will not add affect ledger</p>
          </div>
          </a></center>
    </div>

        </div>
    </div>
<div class="row">
        <div class="col-md-12 content-panel">
                <table class="table table-condensed table-striped cheaque_listing">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Bank</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Cheque ID</th>
                            <th>Cheque Release Date</th>
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>
                </table>

        </div>
    </div>
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" id="addTransaction" class="modal fade"  data-backdrop="static"> 
        <div class="modal-dialog" style="width: 80%">
         <div class="modal-content col-md-12">
          <div class="col-md-12">
             <div class="pull-right"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
            <div class="content-panel">
               <form id="product_add_form" action="{{ route('cheque_managers.store') }}">
                <input type="hidden" name="type" value="">
              <div class="multiRowInput">
              <center><h1 id="modal_title">Sale</h1></center>
              <div id="log"></div>
              <div class="col-md-4">
                <div class="form-group">
                    <label>Date:</label>
                    <input name="date" class="form-control date-picker" style="background: rgba(33, 37, 47, 0.85);color: #FFEB3B" type="text">
                </div>
            </div>
<!--             <div class="col-md-4">
            <div class="form-group" id="supplier">
                <label>Supplier:</label>
                <select  name="supplier_id" class="form-control supplier" style="background: rgba(33, 37, 47, 0.85);color: #FFEB3B" autofocus>
                    <option value="" disabled selected style="display: none;">No Supplier</option>

                    
                </select>
            </div>
            </div> -->
              <table id="aaks" class="table table-bordered table-striped table-hover">

              <thead>
                
              </thead>
              <tbody id="appendMe">
             
                
                </tbody>
                </table>
                <input type="hidden" id="is_purchase" name="is_purchase" value="0">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
              </div>
            </div>          
          </div>
          <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
          <center>
            <input type="submit" id="submit_btn" class="btn btn-primary btn-lg" value="Add Transaction"/>
            </center>
                </form>
        </div>
        </div>
      </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript" src="{{asset('assets/js/choices.min.js')}}"></script>
<script type="text/javascript" src="{{url('supplier.json')}}"></script>
<script type="text/javascript" src="{{url('customer.json')}}"></script>
<script type="text/javascript">
var count = 0;
var productselectbox;

  var current_head = ' <tr>\
                  <th>Bank</th>\
                  <th>Amount</th>\
                  <th>Transaction Id</th>\
                  <th>Release Date</th>\
                  <!-- <th>Supplier</th> -->\
                  <th>Customer</th>\
                  <th>Option</th>\
                </tr>';
  var current_body = '<tr class="parent_row">'+
              '<td><input type="text" name="bank[]" class="form-control i_am_first" placeholder="Bank (if any)" autofocus></td>'+
              '<td><input type="text" name="amount[]" class="form-control" placeholder="Amount"></td>'+
              '<td><input type="text" name="transacion_id[]" class="form-control qty0" placeholder="Transaction ID (if any)"></td>'+
              '<td>'+
                '<input type="date" name="release_date[]" class="form-control" placeholder="Cheque Release Date (if any)"></select>'+
              '</td>'+
              '<td class="this_is_last" onkeydown="keyDown(event, this)">'+
                '<select name="customer[]" class="form-control customer" >'+
                  '<option value="0" selected>Select customer</option>'+
                '</select>'+
              '</td>'+
               
              
              '<td><a style="cursor: pointer;" name="cln_btn" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td>'+
            '</tr>';

  

function showModal(type) {
  $('#product_add_form input[name=type]').val(type);
  choices('.customer','customer_json_d');
  // choices('.supplier','supplier_json_d');
  $('#addTransaction').modal('show');
}
function clean(e, ele)
{
  var box = current_body;
  var row = $(ele).closest('tr');
  
  var boss = $("tbody#appendMe").children();
  if (boss.length < 2)
  {
        var xo = $("#appendMe").append(box);
        // xo.children().last().children().find("td").attr('onkeydown','keyDown(event, this)');
  }
  $(row).remove();
  choices('.customer','customer_json_d');
  choices('.supplier','supplier_json_d');
if (hide_payment_type)
        {
          $('#aaks tr > *:nth-child(2)').hide();        
        }
        if (select_cheque)
        {
          $("select[name^=payment_type]").val("cheque");
        }
        $("#appendMe").find('.this_is_last').removeAttr('onkeydown');
        $("#appendMe").find('.this_is_last').last().attr('onkeydown','keyDown(event, this)');
  //  var date = new Date();
  // var day = date.getDate();
  // var monthIndex = date.getMonth() + 1;
  // var year = date.getFullYear();

  // $('.date-picker').val(day+"-"+monthIndex+"-"+year);
  // choices('.product'+count);
  // debugger;
}
function appendrow()
{
    count += 1;
    // e.preventDefault(); 
    var box = current_body;
    var xo = $("#appendMe").append(box);
    // xo.children().last().children().find("select").attr('onkeydown','keyDown(event, this)');
    // $(me).removeAttr('onkeydown');
    // choices('.product'+count);
    choices('.customer','customer_json_d');
    $("#appendMe").find('.this_is_last').removeAttr('onkeydown');
    $("#appendMe").find('.this_is_last').last().attr('onkeydown','keyDown(event, this)');
    // debugger;
    $("#appendMe").find('tr').last().find('input').first().focus();
}
function keyDown(e, me)
{
  var keyCode = e.keyCode || e.which; 

  console.log(keyCode);

  if (keyCode == 9) { 
    e.preventDefault();
    appendrow();
    $(me).removeAttr('onkeydown');
    return false;
  } 
}
function keyFocus(e, me)
{
  var keyCode = e.keyCode || e.which; 
  
  if (keyCode == 9) { 
    // count += 1;
    e.preventDefault(); 
    $(me).closest('td').find('input').focus();
  } 
}


var hide_payment_type = select_cheque = false;

$("#cheque_receive").click(function (e){
    $("#submit_btn").val('Add');
    $("#is_purchase").val(0);
    $("#modal_title").html("Cheque Received");
    $("#aaks thead").html(current_head);
    $("#aaks tbody").html(current_body);

    // $("#supplier").hide();
});
$("#cheque_forward").click(function (e){
    $("#submit_btn").val('Add');
    $("#is_purchase").val(1);
    $("#modal_title").html("Cheque Forward");
    $("#aaks thead").html(current_head);
    $("#aaks tbody").html(current_body);
    // $("#supplier").show();
});

function choices(identifier,json_url){
  // var warehouse_id = $("#warehouse_d").val();
      $(identifier).select2({
        // ajax: {
        //   url: json_url,
        //   dataType: 'json',
        //   delay: 250,
        //   data: function (params) {
        //     return {
        //       q: params.term, // search term
        //       page: params.page
        //     };
        //   },
        //   processResults: function (data, params) {
        //     // parse the results into the format expected by Select2
        //     // since we are using custom formatting functions we do not need to
        //     // alter the remote JSON data, except to indicate that infinite
        //     // scrolling can be used
        //     params.page = params.page || 1;

        //     return {
        //       results: data.items,
        //       pagination: {
        //         more: (params.page * 10) < data.total_count
        //       }
        //     };
        //   },
        //   cache: true
        // },
        data: eval(json_url),
        // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 0,
    });
      $(identifier).on('select2:close', function (e) {
        // debugger;
       $(identifier).blur();
      // $("#qty"+count).focus();
    // appendrow();
    $(this).focus();
});

  }

  function clearAll()
  {
    $("#appendMe").empty();
     var box = current_body;
    $("#appendMe").html(box);
        choices('.customer','customer_json_d');
  }

   $('[role=dialog]').on('shown.bs.modal', function() {
    $("form").find("#log").removeClass();
    $("form").find("#log").html("");
        clearAll();
    });



  $(document).ready(function() {

    $('.cheaque_listing').dataTable( {
  "ajax": "/cheque_managers_listing_datatable",
  "processing": true,
  "serverSide": true,
  "order": [[0,'desc']],
   "columns": [
            { "data": "id", "orderable": true, "searchable": true},
            {"data":"date"},
            {"data":'type'},
            { "data": "bank" },
            { "data": "customer.name", "defaultContent":"-" },
            { "data": "amount" },
            { "data": "transaction_id" },
            { "data": "release_date" },
            { "data": "options", "orderable": false, "searchable": false }

        ],
        "fnDrawCallback": function (oSettings) {
          init();
        }
  });

  var date = new Date();
  var day = date.getDate();
  var monthIndex = date.getMonth() + 1;
  var year = date.getFullYear();

  $('.date-picker').val(day+"-"+monthIndex+"-"+year);// = new Date();
  $(".datatable").show();
});

</script>
@endsection