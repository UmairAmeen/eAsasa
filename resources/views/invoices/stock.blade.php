@extends('layout')
@section('header')
    <div class="row">
        <div class="col-md-12">
            <center>Name: {{ $customer->name }}<br>
                SaleOrder # {{ $sale_order->id }}
            </center>
            <form action="{{ route('stock.store') }}" method="POST">
                <input type="hidden" name="stock_deliver" id="stock_deliver" value="{{ true }}">
                <div id="log"></div>
                <div class="form-group">
                    <label>Date</label>
                    <input class="form-control" type="date" value="{{ date('Y-m-d') }}" name="date" required="required">
                </div>
                <div class="form-group">
                    <label id="warehouse_label">Warehouse:</label>
                    <select id="warehouse_d" name="warehouse_id" class="form-control">
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Product</label>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>All Stock Delivery against of Sale Order #{{ $sale_order->id }}</h4>
                            <table class="table">
                                <thead>
                                    <td><input type="checkbox" id="checkk" name="checkall" class="form-control"
                                            onchange="checkAll(this)" class="form-control"></td>
                                    <th>Product Name</th>
                                    <th>Quantity to be Delivered</th>
                                    <th>Quantity</th>
                                </thead>
                                <tbody>
                                    @php($count = 0)
                                    @foreach ($products as $key => $value)
                                        @php($quantity = getQuantityFromOrder($value->id, $sale_order->invoice->id) - getProductStockDeliveredInSaleOrder($value->id, $sale_order->id))
                                        @if ($quantity == 0)
                                            @continue
                                        @else
                                            @php($count++)
                                            <input type="hidden" name="product_id[]" value="{{ $value->id }}">
                                            <tr>
                                                <td><input type="checkbox" onchange="checkOne(this)" class="form-control"
                                                        value="{{ $value->id }}" name="check[]"></td>
                                                <td><input type="text" class="form-control" readonly
                                                        value="{{ $value->name }}"></td>
                                                <td><input type="text" class="form-control" id="quantity_remaining"
                                                        name="quantity_remaining[]" readonly value="{{ $quantity }}">
                                                </td>
                                                <td><input type="number" onkeyup="checkQuantity(this)"
                                                        class="form-control" required name="quantity[]"
                                                        value="{{ 0 }}" min="0" max="{{ $quantity }}">
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <small></small>
                </div>
                <input type="hidden" name="is_purchase" value="0">
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                <input type="hidden" name="sale_orders_id" id="sale_orders_id" value="{{ $sale_order->id }}">
                @if ($count > 0)
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit" onclick="setTimeout(closeIt,1000)"
                            id="submit">Add</button>
                    </div>
                @endif
            </form>
        </div>
    </div>
    <hr>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h4>All Stock Delivery against of Sale Order #{{ $sale_order->id }}</h4>
            <table class="table">
                <thead>
                    <th>Id</th>
                    <th>Date</th>
                    <th>Product Name</th>
                    <th>Warehouse Name</th>
                    <th>Quantity</th>
                </thead>
                <tbody>
                    @foreach ($sale_order->stock as $stock)
                        <tr>
                            <td>{{ $stock->id }}</td>
                            <td>{{ app_date_format($stock->date) }}</td>
                            <td>{{ $stock->product->name }}</td>
                            <td>{{ $stock->warehouse->name }}</td>
                            <td>{{ $stock->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function checkQuantity(ele) {
            console.log($(ele).val());
            if ($(ele).val() != "") {
                if ($(ele).val() > $(ele).prop('max')) {
                    $(ele).val($(ele).prop('max'));
                    return true;
                }
                if ($(ele).val() <= $(ele).prop('min')) {
                    $(ele).val($(ele).prop('min'));
                }
            }
        }
    </script>
    <script>
        function closeIt() {
            // window.close();
        }
    </script>
    <script>
        function checkAll(ele) {
            var checkboxes = document.getElementsByName('check[]');
            if ($(ele).is(":checked")) {
                for (let index = 0; index < checkboxes.length; index++) {
                    checkboxes[index].checked = true;
                }
            } else {
                for (let index = 0; index < checkboxes.length; index++) {
                    checkboxes[index].checked = false;
                }
            }
        }

        function checkOne(ele) {
            if ($(ele).is(":checked")) {
                console.log('check');
                $(ele).checked = false;
            } else {
                $(ele).checked = true;
                let allcheck = document.getElementsByName('checkall');
                allcheck[0].checked = false;
            }
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            choices('.product');
        });



        function choices(identifier) {
            var warehouse_id = $("#warehouse_d").val();
            $(identifier).select2({
                ajax: {
                    url: "/pagination_product_json/",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
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
            }).focus();
            $(identifier).on('select2:close', function(e) {
                if ($(identifier).val() > 0) {
                    $.ajax({
                        url: "/warehouse_product_json/",
                        data: {
                            'warehouse_id': $("#warehouse_d").val(),
                            'product_id': $(identifier).val()
                        }
                    }).done(function(data) {
                        $(identifier).parent().find('small').html('Stock: ' + data);

                    }).error(function(d) {
                        alertify.error('Some error occurred, try again').dismissOthers();

                        $(identifier).parent().find('small').html('Unable to get stock, try again');

                    });

                    $(identifier).blur();
                    $("#qty" + count).focus();
                }
                //some ajax call to update <br><small id="stock_info'+count+'"></small>

            });

        }
    </script>
@endsection
