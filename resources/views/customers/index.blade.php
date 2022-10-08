@extends('layout')
@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="glyphicon glyphicon-user"></i> Customers
             @if(is_allowed('customer-create'))
            <a accesskey="n" class="btn btn-success pull-right" href="{{ route('customers.create') }}"><i class="glyphicon glyphicon-plus"></i> Create <br><small>ALT + N</small></a>
             @endif
        </h1>

    </div>
@endsection

@section('content')
<div class="row mt">

    <div class="col-md-12">

    
      @if(is_allowed('customer-import-export'))
      <a href="{{url('customers.xlsx')}}">
        <div  class="col-md-2 col-sm-2 box0">
        <div class="box1">
          <span class="fa fa-arrow-down"></span>
          <h3>Download Excel</h3>
          <span>Customer Data</span>
        </div>
      </div></a>

        <a data-toggle="modal" data-target="#uploadExcel">
        <div  class="col-md-2 col-sm-2 box0">
        <div class="box1">
          <span class="fa fa-arrow-up"></span>
          <h3>Import Excel</h3>
        </div>
      </div></a>

      @endif

    </div>
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
    <div class="row">
        <div class="col-md-12">
                <div class="content-panel">
                    <p>
                        <center>
                            <input type="text" class="form-control" id="search_city" name="City" placeholder="Filter Table by City Name"> 
                        </center>
                    </p>
                <table class="table table-condensed table-striped customer_databale">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>REG #</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>City</th>
                            <th>Address</th>
                            <th>Type</th>
                            <th>Notifications</th>
                            <th>Notify Days</th>
                            <th>Last Contact on</th>
                            <th>Balance</th>
                            <th>Notes</th>
                            <th>Added By</th>
                            <th>Updated By</th>
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                 
                    </tbody>
                </table>

            </div>
        </div>
    </div>
        <div aria-hidden="true" aria-labelledby="Upload Excel" role="dialog" data-backdrop="static" id="uploadExcel" class="modal fade">
        <div class="modal-dialog" style="width: 90%">
         <div class="modal-content col-md-12">
               <form id="uploadExcelFile" action="{{ url('uploadCustomerExcel') }}" method="POST" enctype="multipart/form-data" class="no-ajax">
          <div class="col-md-12">
            <div class="content-panel">
              <div id="log"></div>
              <center><h1>Please Attach Excel File (xls/xlsx)</h1></center>
                  <div class="form-group">
                    <input type="file" name="importexcel" class="form-control" accept=".xlsx,.xls">
                  </div>
            </div>          
          </div>
          <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
          <center>
            <button class="btn btn-primary btn-lg" type="submit">Upload</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
            </center>
        </div>
            </form>
        </div>
      </div>
    </div>

@endsection
@section('scripts')
<?php $messageTop = "Customer List";
$exportingCol = "0,1,2,3,4,8,9" ?>
    <script type="text/javascript">
        var table = false;
          $(document).ready(function() {


         $( '#search_city', this ).on( 'keyup change', function () {
            if ( table.column(4).search() !== this.value ) {
                table
                    .column(4)
                    .search( this.value )
                    .draw();
            }
        } );

  table=   $('.customer_databale').DataTable( {
  "ajax": "/customer_listing_datatable",
  "processing": true,
  "serverSide": true,
  responsive: true,
  dom: "Blfrtip",
    lengthMenu : [[10, 25, 50, -1], [10, 25, 50, "All"]],
  "order": [[0,'desc']],
   "columns": [
            { "data": "id", "orderable": true, "searchable": true},
            { "data":"registeration_number", "defaultContent":"-"},
            {"data":'name'},
            {"data":"phone"},
            {"data":"city", "defaultContent":"-"},
            {"data":"address", "defaultContent":"-"},
            { "data": "type" },
            { "data": "payment_notify" },
            { "data": "after_last_payment" },
            { "data": "last_contact_on" },
            { "data": "balance", "searchable":false, "orderable":false},
             { "data": "notes", "searchable":true, "orderable":false, "defaultContent":"-"},
            { "data": "added_by", defaultContent:"-", "orderable": false, "searchable": false },
            { "data": "updated_by", defaultContent:"-", "orderable": false, "searchable": false },
            { "data": "options", "orderable": false, "searchable": false }

        ],
        buttons: [
                <?=get_print_template(".datatable", 'copy', 'Customers', $messageTop, $exportingCol)?>, <?=get_print_template(".datatable", 'excel', 'Customers', $messageTop, $exportingCol)?>, <?=get_print_template(".datatable", 'print', 'Customers', $messageTop, $exportingCol);?>
                ],
        "fnDrawCallback": function (oSettings) {
          // init();
        //   del_init()
        // <th>ID</th>
        //                     <th>TYPE</th>
        //                     <th>PAYMENT TYPE</th>
        //                     <th>AMOUNT</th>
        //                     <th>INVOICE ID/ TRANSACTION ID</th>
        //                     <th>CUSTOMER ID/ SUPPLIER ID</th>
        //                     <th class="text-right">OPTIONS</th>
        }
  });


});
    </script>
@endsection