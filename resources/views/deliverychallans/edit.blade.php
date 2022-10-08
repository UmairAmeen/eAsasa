@extends('layout')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css"
          rel="stylesheet">
    <style>
        .description {text-align: left;}
    </style>
@endsection
@section('header')
    <div class="page-header"><h1><i class="glyphicon glyphicon-plus"></i> Delivery Challan / Update </h1></div>
@endsection
@section('content')
    @include('error')
    @if (Session::has('message'))
        <div class="alert alert-success"> {{ Session::get('message') }} </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('deliverychallans.update', $challan->id) }}" method="POST" class="no-ajax">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="col-md-12" style="background-color: cornsilk;">
                    <div class="col-md-4">
                        <div class="form-group" id="date_form_group">
                            <label>Date:</label>
                            <input type="date" name="date" class="form-control" value="{{date('Y-m-d', strtotime($challan->date))}}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" id="warehouse_form_group">
                            <label>Warehouse:</label>
                            {{Form::select('warehouse_id', $warehouses, $challan->warehouse_id, ['class' => 'form-control', 'disabled', 'id' => 'warehouse_id'])}}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" id="date_form_group">
                            <label> Rep By:</label>
                            <input type="text" name="rep_by" class="form-control" value="{{$challan->rep_by}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" id="date_form_group">
                            <label> Name:</label>
                            {!! Form::select('customer_id', [$customer->customer_id => $customer->name . ' | ' . $customer->phone], $challan->customer_id, ['class' => 'form-control', 'id' => "customer_id", 'disabled' ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" id="date_form_group">
                            <label> Order No:</label>
                            {!! Form::select('order_no', [$challan->order_no => $challan->order_no], $challan->order_no, ['class' => 'form-control multipleSelect2', 'id' => "order_no", 'disabled']) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" id="date_form_group">
                            <label> Address:</label>
                            <input type="text" name="address" class="form-control" id="address" value="{{$challan->address}}"
                                   placeholder="Type a delivery address here if it is different from Customer's address (OPTIONAL)">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            Following fields are not editable because of security concerns:
                            <br> Warehouse, Customer, Order No and Quantity.
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-striped" style="margin-top:20px">
                            <thead>
                            {{--<th width="10%"><input type="checkbox" id="selectAllOrders" class="order"></th>--}}
                            {{--<th> Sr No</th>--}}
                            <th  width="40%" class="description"> Description</th>
                            <th width="10%"> Qty</th>
                            <th  width="15%"> Status</th>
                            <th  width="25%"> Remakrs</th>
                            </thead>
                            <tbody id="order_detail_resp"></tbody>
                        </table>
                    </div>
                    <div class="well well-sm">
                        <button type="submit" class="btn btn-success">Update</button>
                        <a class="btn btn-link pull-right" href="{{ route('deliverychallans.index') }}">
                            <i class="glyphicon glyphicon-backward"></i> Back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript" src="{{asset('assets/js/jquery.numeric.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-switch.js')}}"></script>

<script>
    const challan_details = JSON.parse("{!! addcslashes(json_encode($challan->o_details), '"') !!}");
    const invoice_id = $('#order_no').val();
    // console.log('invoice_id: ', invoice_id, challan_details);
    $(document).ready(function(){
        $.ajax({
            url: "/customer_orders_dropdown_detail/",
            cache: false,
            async: false,
            dataType: "html",
            data: {'invoices': invoice_id, 'is_quantity_fixed': true}
        }).done(function (data) {
            $("#order_detail_resp").html(data);
            $.each(challan_details, function(index,detail) {
                $("#order_detail_resp").find('#quantity_'+detail.product_id).val(detail.quantity);
                $("#order_detail_resp").find('#status_'+detail.product_id).val(detail.status);
                $("#order_detail_resp").find('#remark_'+detail.product_id).val(detail.remarks);
                console.log(detail);
            })
        }).error(function (d) {
            alert(d);
        });
    })
</script>
@endsection
