@extends('layout')
@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fas fa-bullhorn"></i> Promotion
        </h1>

    </div>
@endsection
@section('content')
<div class="row mt">
    <div class="col-md-12">
        <a data-toggle="modal" data-target="#send_sms">
            <div  class="col-md-3 box0">
            <div class="box1">
                <span class="fa fa-arrow-up"></span>
                <h3>Send SMS</h3>
            </div>
            <p>Send Promotion SMS</p>
            </div>
        </a>
        <a id="success_sms" onclick="datatable('success');">
            <div  class="col-md-3 box0">
            <div class="box1">
                <span class="fa fa-eye"></span>
                <h3>Success SMS</h3>
            </div>
            <p>Success SMS List</P>
            </div>
        </a>
        <a id="error_sms" onclick="datatable('error');">
            <div  class="col-md-3 box0">
            <div class="box1">
                <span class="fa fa-eye"></span>
                <h3>Error SMS</h3>
            </div>
            <p>Error SMS List</P>
            </div>
        </a>
    </div>

    <div class="col-md-12">
        <div class="col-md-12 p-x-2">
          @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
          @endif
          @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
          @endif
          @if(!$promotion_sms->isEmpty())
            <div class="content-panel">
              <div class="table-responsive">
                <table class="table table-bordered table-condensed table-hover table-striped promotion_sms_listing table-responsive">
                  <thead>
                    <tr>
                      <th class="numeric">ID</th>
                      <th class="numeric">Phone</th>
                      <th>Message</th>
                      <th>Status</th>
                      <th class="text-right"><i class=" fa fa-edit"></i> Options</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
            @else
              <h3 class="text-center alert alert-info">Empty!</h3>
            @endif
        </div>
    </div>
</div>
<!-- Send SMS Modal -->
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" data-backdrop="static" id="send_sms" class="modal fade">
        <div class="modal-dialog" style="width: 95%">
         <div class="modal-content col-md-12">
          <div class="col-md-12">
            <div class="">
                <form id="sms_send_form" action="promotion/send_sms" >
                    <div class="multiRowInput">
                        <div id="log"></div>
                        <div id="sms_log"></div>
                        <center><h1>Send SMS</h1></center>
                        <div class="col-md-12" id="appendMe">
                            <div class="parent_row">
                                <div class="row">
                                    <div class="{{$class}}">
                                        <div class="form-group" id="customer_file">
                                            <label>Attach Excel File (xls/xlsx)</label>
                                            <input type="file" name="importexcel" class="form-control" accept=".xlsx,.xls" onchange="fileSelect(this)">
                                            <input id="customer_numbers" type="hidden" name="customer_numbers[]" class="form-control" value="">
                                        </div>
                                        <div class="form-group" id="customer_select">
                                            <label>Customers</label>
                                            <select class="form-control" name="customer_id[]" onchange="customerSelect(this)" multiple="multiple" style="width: 100%">
                                                <option class="select_all" value="all">Select All</option>
                                                @foreach($customers as $customer)
                                                <option class="customer" value="{{$customer->id}}">{{$customer->name}} [<small>{{$customer->city}}</small>]</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Message</label>
                                            <textarea name="message" id="message" class="form-control input-sm" cols="30" rows="5" placeholder="Write Message" >{{$last_message->message}}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
            <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
                <center>
                    <button class="btn btn-primary btn-lg" id="submit_btn" onclick="sendSms()">Send SMS</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
                </center>
            </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">

var flag = true;
 $(document).ready(function(){
    customer_select2();
    datatable(); 
 });

function customerSelect(e){
    if(flag){
        $("#customer_file").find('#customer_numbers').val('');
        $('input[name=importexcel]').val('');
        var customers = $(e).val();
        var xxi = $.inArray('all', customers);
        if (xxi != -1) {
            $('#customer_select > select').find('.select_all').prop('selected', false);
            $('#customer_select > select').select2('destroy').find('.customer').prop('selected', 'selected').end();
            customer_select2();
        }
    }
    else{
        this.flag = true;
    }
    
}

function fileSelect(e){
    this.flag = false;
    $("#customer_select").find('select').val(null).trigger("change");
    $("#submit_btn").attr('disabled', true);
    var file = $(e).prop('files')[0];
    var fd=new FormData();
    fd.append('file',file);

    $.ajax({
        type: 'POST',
        url: "promotion/import_excel",
        processData: false,
        contentType: false,
        data:fd,
        success: function(resultData) {
            $("#customer_numbers").val(resultData);
            $("#submit_btn").attr('disabled', false);
        },
        error: function(error) {
            alertify.error("Something Wrong").dismissOthers();
            $("#submit_btn").attr('disabled', false);
        }
    });
}

function customer_select2(){
    $("#customer_select").find('select').select2({
        placeholder: "Select Customer",
        allowClear: true
    });
}

function sendSms(){
    $("#sms_send_form").submit();
}

function del_init() {
  $(".delete_promotion_sms").click(function(e){
    e.preventDefault();
    var ele = $(this);
    alertify.confirm('Are you sure, you want to delete this SMS ?', '', function(){ return ele.parent('form').submit(); } , function(){ return true;});
  });
}

function datatable(message = null) {
var url = "/promotion_listing_datatable"+"/"+message;
if(message)
{
    $('.promotion_sms_listing').DataTable().ajax.url( url ).load();
}

<?php $messageTop = "SMS List";
$exportingCol = "0,1,2,3" ?>
var table = false;
table = $('.promotion_sms_listing').DataTable({
  "ajax": url,
  serverSide:true,
  processing:true,
  dom: "Blfrtip",
  lengthMenu : [[10, 25, 50, -1], [10, 25, 50, "All"]],
  "ordering": true,
  stateSave: true,
    "stateLoadParams": function (settings, data) {
        data.search.search = "";
        data.length = 10;
        data.start = 0;
        data.order = [];
    },
  "columns": [
    { "data": "id" },
    { "data": "number" },
    { "data": "message" },
    { "data": "status", defaultContent:"-", orderable:false },
    {"data": "options", searchable:false, orderable:false},
  ],
  "fnDrawCallback": function (oSettings) {
    del_init();
  },
  buttons: [
    <?=get_print_template(".datatable", 'copy', 'SMS', $messageTop, $exportingCol)?>, <?=get_print_template(".datatable", 'excel', 'SMS', $messageTop, $exportingCol)?>, <?=get_print_template(".datatable", 'print', 'SMS', $messageTop, $exportingCol);?>,'colvis'
  ]
});
}

$('.modal').on('hidden.bs.modal', function () {
    $(this).find('#sms_send_form').trigger('reset');
    this.flag = false;
    $("#customer_select").find('select').val(null).trigger("change");
});
</script>
@endsection