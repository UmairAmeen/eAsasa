@extends('layout')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css"
          rel="stylesheet">
    <style>
        .description {
            text-align: left;
        }
    </style>
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> Delivery Challan / Create </h1>
    </div>
@endsection
@section('content')
    @include('error')
    @if (Session::has('message'))
        <div class="alert alert-success"> {{ Session::get('message') }} </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('deliverychallans.store') }}" method="POST">
                <div id="log"></div>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="col-md-12" style="background-color: cornsilk;">
                    <div class="col-md-4">
                        <div class="form-group" id="date_form_group">
                            <label>Date:</label>
                            <input type="date" name="date" class="form-control" value="{{date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" id="warehouse_form_group">
                            <label>Warehouse:</label>
                            {{Form::select('warehouse_id', $warehouses, NULL, ['class' => 'form-control', 'id' => 'warehouse_id'])}}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" id="date_form_group">
                            <label> Rep By:</label>
                            <input type="text" name="rep_by" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" id="date_form_group">
                            <label> Name:</label>
                            {!! Form::select('customer_id', ["" => " - Select Customer -"], "", ['class' => 'form-control', 'id' => "customer_id"]) !!}
                            {{--
                            <select id="customer_id" class="form-control" name="customer_id">
                                <option value=""> - Select Customer -</option>
                                @foreach($customers as $customer)
                                    <option value="{{$customer->id}}">{{$customer->name}} {{$customer->phone}}</option>
                                @endforeach
                            </select>
                            --}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" id="date_form_group">
                            <label> Order No:</label>
                            {!! Form::select('order_no', ["" => " - Select Order -"], "", ['class' => 'form-control multipleSelect2', 'id' => "order_no"/*, 'multiple' => 'multiple'*/]) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" id="date_form_group">
                            <label> Address:</label>
                            <input type="text" name="address" class="form-control" id="address"
                                   placeholder="Type a delivery address here if it is different from Customer's address (OPTIONAL)">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            Following fields will not be updated after creating delivery form because of security concerns:
                            <br>Warehouse, Customer, Order No and Quantity.
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-striped" style="margin-top:20px">
                            <thead>
                            {{--<th width="5%"><input type="checkbox" id="selectAllOrders" class="order"></th>--}}
                            {{--<th> Sr No</th>--}}
                            <th  width="35%" class="description"> Description</th>
                            <th width="15%"> Qty</th>
                            <th  width="15%"> Status</th>
                            <th  width="35%"> Remarks</th>
                            </thead>
                            <tbody id="order_detail_resp"></tbody>
                        </table>
                    </div>
                    <div class="well well-sm">
                        <button type="submit" class="btn btn-primary">Create</button>
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
{{--<script type="text/javascript" src="{{url('customer_orders.json')}}"></script>--}}
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

<script>
    const sale_orders = JSON.parse("{!! addcslashes(json_encode($sale_orders), '"') !!}");
    let customer_string = "{!! addcslashes(json_encode($customers), '"') !!}";
    customer_string = customer_string.replaceAll('\\','\\\\');
    const customers = JSON.parse(customer_string);
    orders = {};
    $(document).ready(function () {
        loadCustomers(customers);
        loadOrders(sale_orders);
        $('#customer_id, #order_no').select2();
    });
    $("#customer_id").change(function() {
        const customer_id = $("#customer_id").val();
        if(customer_id == "") {
            $('#address').val("");
            loadOrders(sale_orders);
            return false;
        }
        const selected_customer = customers.find(customer => customer.id == customer_id);
        $('#address').val(selected_customer.address != undefined ? selected_customer.address : '');
        orders = sale_orders.filter(function(order) { return order.customer_id == customer_id; });
        let reloadOrders = false;
        let selected = $("#order_no").val();
        if(selected == "" || selected == null) {
            reloadOrders = true;
            selected = "";
        }else {
            invoice_id = selected;
            isSelected = orders.find(o => o.invoice_id == invoice_id);
            console.log('isSelected', isSelected);
            if(isSelected == undefined) {
                reloadOrders = true;
            }
        }
        console.log('reloadOrders', reloadOrders);
        if(reloadOrders) {
            loadOrders(orders);
            if(selected == "" || isSelected == undefined) {
                selected = orders[0] == undefined ? "" : orders[0]['invoice_id'];
            }
            $("#order_no").val(selected).trigger('change');
        }        
    });
    $("#order_no").on("change", function () {
        const order_no = $(this).val();
        if(order_no == "") {
            loadOrders(orders);
            $("#order_detail_resp").html("");
            return false;
        }
        const invoice_id = order_no;
        let order = sale_orders.find(o => o.invoice_id == invoice_id);
        // console.log($("#customer_id").val(), order.customer_id);
        if($("#customer_id").val() != order.customer_id) {
            $("#customer_id").val(order.customer_id).trigger('change');
        }
        $.ajax({
            url: "/customer_orders_dropdown_detail/",
            cache: false,
            async: false,
            dataType: "html",
            data: {'invoices': invoice_id}
        }).done(function (data) {
            $("#order_detail_resp").html(data);
        }).error(function (d) {
            alert(d);
        });
    });
    $(document).on('click', '#selectAllOrders', function () {
        $('.order').prop('checked', $(this).prop('checked'));
    });

    function loadCustomers(customers) {
        $.each(customers, function(index, user) {
            let option = new Option(user.name + ' | ' + user.phone, user.id);
            $('#customer_id').append(option);
        });
    }

    function loadOrders(orders) {
        $('#order_no option:not(:first)').remove();
        $.each(orders, function(index, order) {
            let option = new Option(order.invoice_id, order.invoice_id);
            $('#order_no').append(option);
        });
    }
    </script>
@endsection
