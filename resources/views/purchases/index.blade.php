@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fa fa-credit-card"></i> Purchases
            @if (is_allowed('purchase-create'))
            <a accesskey="a" class="btn btn-success pull-right" href="{{ url('pos/purchase') }}"><i class="glyphicon glyphicon-plus"></i> POS <br><small>ALT + A</small></a>
            <a class="btn btn-success pull-right" accesskey="n" href="{{ route('purchases.create') }}"><i class="glyphicon glyphicon-plus"></i> Add a Purchase <br><small>ALT + N</small></a>
            @endif
        </h1>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
        <div class="content-panel">
            <div class="table-responsive">

                <table class="table table-condensed table-striped datatable_p table-responsive">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Bill #</th>
                            <th>Supplier Name</th>
                            <!-- <th>Product</th> -->
                            <!-- <th>Stock Purchased</th> -->
                            <!-- <th>Purchase Price pcs (Total Price)</th> -->
                            <!-- <th>From Supplier</th> -->
                            <th>Total</th>
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
    </div>

@endsection
@section('scripts')
<script type="text/javascript">
    $(".datatable_p").DataTable({
        "ajax": "/datatable_purchases",
        "dom":"Blfrtip",
        "processing": true,
        "serverSide": true,
        "order": [[0,'desc']],
        "columns": [
            {"data": "id", "orderable": true, "searchable": true},
            {"data": "date"},
            {"data": "bill_number", "defaultContent":"-"},
            {"data": 'supplier.name', 'defaultContent':"-"},
            {"data": "total", "searchable":false },
            {"data": "added_by", defaultContent:"-", "orderable": false, "searchable": false },
            {"data": "updated_by", defaultContent:"-", "orderable": false, "searchable": false },
            {"data": "options", "orderable": false, "searchable": false }
        ]
    });
</script>
@endsection