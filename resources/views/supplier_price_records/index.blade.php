@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fa fa-file-invoice-dollar"></i> Supplier Price Records
           <!--  <a class="btn btn-success pull-right" href="{{ route('supplier_price_records.create') }}"><i class="glyphicon glyphicon-plus"></i> Create</a> -->
        </h1>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 showback">
            @if($supplier_price_records->count())
                <table class="table table-condensed table-striped supplier_databale">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Last Price Updated</th>
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($supplier_price_records as $supplier_price_record)
                            <tr>
                                <td>{{$supplier_price_record->supplier_id}}</td>
                                <td>{{$supplier_price_record->supplier->name}}</td>
                                <td>{{date_format_app($supplier_price_record->max_dt)}}</td>
                                
                                <td class="text-right">
                                    <a class="btn btn-xs btn-primary" href="{{ route('supplier_price_records.show', $supplier_price_record->supplier_id) }}"><i class="glyphicon glyphicon-eye-open"></i> View Pricing</a>
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

@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function(e){
     var     table=   $('.supplier_databale').DataTable( {
  responsive: true,
  dom: "Blfrtip",
    lengthMenu : [[10, 25, 50, -1], [10, 25, 50, "All"]],
  "order": [[1,'asc']],
        buttons: [
                <?=get_print_template(".datatable", 'copy', 'Suppliers', $messageTop, $exportingCol)?>, <?=get_print_template(".datatable", 'excel', 'Suppliers', $messageTop, $exportingCol)?>, <?=get_print_template(".datatable", 'print', 'Suppliers', $messageTop, $exportingCol);?>
                ],
      
  });
    });
</script>
@endsection