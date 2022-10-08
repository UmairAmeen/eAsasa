@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fas fa-receipt"></i> Direct Sales Invoices
             @if (is_allowed('sale-create'))
            <a accesskey="a" class="btn btn-success pull-right" href="{{ url('pos/direct') }}"><i class="glyphicon glyphicon-plus"></i> POS <br><small>ALT + A</small></a>
            <a accesskey="n" class="btn btn-success pull-right" href="{{ route('sales.create') }}"><i class="glyphicon glyphicon-plus"></i> Create <br><small>ALT + N</small></a>
            <a accesskey="q" class="btn btn-success pull-right" href="{{ route('sales.create') }}?customer=459"><i class="glyphicon glyphicon-plus"></i> Create Cash Sale <br><small>ALT + Q</small></a>
             @endif
        </h1>

    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
                <div class="content-panel">
                    <!-- <div> -->
                    <div class="table-responsive">

            @if($sales->count())
                <table class="table table-condensed table-striped stocks_listing display  nowrap">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>Bill #</th>
                            <th>Customer</th>
                            <th>Shipping Cost</th>
                            <th>Grand Total</th>
                            <th>Added By</th>
                            <th>Updated By</th>
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                       
                    </tbody>
                </table>
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif
        </div>
</div>
        </div>
    </div>

@endsection
@section('scripts')
<script type="text/javascript">
    $('.stocks_listing').dataTable( {
  "ajax": "/sales_datatable",
  "processing": true,
  "serverSide": true,
    "order": [[0,'desc']],
   "columns": [
            { "data": "id", "orderable": true, "searchable": true},
            {"data":'date'},
            {"data":"bill_number","defaultContent":"-"},
            {"data":"customer.name"},
            {"data":"shipping", defaultContent:"0", "searchable": false},
            {"data":"total", "searchable": false},
            { "data": "added_by", defaultContent:"-", "orderable": false, "searchable": false },
            { "data": "updated_by", defaultContent:"-", "orderable": false, "searchable": false },
            { "data": "options", "orderable": false, "searchable": false, 'exportable': false, 'printable':false }

        ]
        // "fnDrawCallback": function (oSettings) {
        //   del_init()
        // }
  } );
    $(".stopMe").click(function(e){
        e.preventDefault();
        alertify.warning('Currently this feature is closed');
    });
</script>
@endsection