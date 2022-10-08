@extends('layout')
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchbuilder/1.0.0/css/searchBuilder.dataTables.min.css">
@endsection

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fa fa-clipboard"></i> Delivery Challans
            <a accesskey="n" class="btn btn-success pull-right" href="{{ route('deliverychallans.create') }}">
                <i class="glyphicon glyphicon-plus"></i> Add Delivery Challan <br><small>ALT + N</small>
            </a>
        </h1>
    </div>
@endsection
@section('content')
     <div class="content-panel">
    <div class="row">
        <div class="col-md-12">
            @if($deliverychallans->count())
                <table class="table table-condensed table-striped filtered_datatable">
                    <thead>
                        <tr>
                            <th width="8%"> ID </th>
                            <th width="8%"> Date </th>
                            <th width="8%"> Order No </th>
                            <th width="8%"> Bill No </th>
                            <th width="8%"> Rep By </th>
                            <th width="12%"> Name </th>
                            <th width="10%"> Phone</th>
                            <th width="30%"> Address </th>
                            <th width="20%"> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliverychallans as $dc)
                            <tr>
                                <td>{{$dc->id}}</td>
                                <td>{{app_date_format($dc->date)}}</td>
                                <td>{{$dc->order_no}}</td>
                                <td>{{$dc->delivery_invoice->bill_number}}</td>
                                <td>{{$dc->rep_by}}</td>
                                <td>{{$dc->name}}</td>
                                <td>{{$dc->phone}}</td>
                                <td>{{ $dc->address ? $dc->address : $dc->customer_address }}</td>
                                <td>
                                    <ul class="list-inline">
                                        <li><a class="btn btn-xs btn-info" href="{{ route('deliverychallans.edit', $dc->d_id) }}">Update</a></li>
                                        <li><a class="btn btn-xs btn-warning" target="_blank" href="{{ route('deliverychallans.show', $dc->d_id) }}"> Print </a></li>
                                        <li><a class="btn btn-xs btn-danger" onclick="return confirm('Delete? Are you sure? It will EFFECT the INENTORY')" href="{{ route('deliverychallans.del', $dc->d_id) }}"> Delete </a></li>
                                    </ul>
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
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.0.0/js/dataTables.searchBuilder.min.js"></script>
    <script>
        $(".filtered_datatable").DataTable({"deferRender": true, responsive: true, "order": [[ 0, "desc" ]], "dom": 'Qlfrtip',});
    </script>
@endsection