@extends('layout')
@section('css')
    <link href="{{ asset('assets/css/bootstrap-datepicker.css') }}" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> Create Purchase </h1>
        <h3><a href="{{ url('purchases') }}">Back to All Purchase</a></h3>
    </div>
@endsection
@section('content')
    @include('error')
    <form id="product_add_form" action="{{ route('purchases.store') }}">
        <div class="row">
            <div class="col-md-12">
                <div id="log"></div>
                <div class="col-md-12">
                    <div class="content-panel">
                        <div class="col-md-12" style="background-color: cornsilk;">
                            <div class="col-md-3">
                                <div class="form-group" id="date_form_group">
                                    <label>Date:</label>
                                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                                    <small id="emailHelp" class="form-text text-muted">This is required</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="date_form_group">
                                    <label>Bill Number:</label>
                                    <input type="text" name="bill_number" class="form-control">
                                    <small id="emailHelp" class="form-text text-muted">Optional, Bill number</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="customer_form_group">
                                    <label>Supplier:</label>
                                    <select name="customer" class="form-control customer">
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    <small id="emailHelp" class="form-text text-muted">This is required</small>
                                    <br>
                                    <!-- <a id="emailHelp" data-toggle="modal" href="#addcustomermodal"  class="form-text text-muted">Add Customer/Supplier</a> -->
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Type:</label>
                                    <select id="s_type" class="form-control" placeholder="s_type">
                                        <option value="purchase">Purchase</option>
                                        <option value="refund">Return</option>
                                        <option value="damage">Damage Return (Not in Stocks)</option>
                                    </select>
                                    <a id="emailHelp" data-toggle="modal" href="#addproductgrpoup"
                                        class="form-text text-muted" accesskey="y">Add Product Group (ALT + Y)</a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="product_form_group">
                                    <label>Product:</label>
                                    <select id="product" class="form-control product" placeholder="product"></select>
                                    <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="warehouse_form_group">
                                    <label>Warehouse:</label>
                                    <select id="warehouse" class="form-control warehouse"></select>
                                    <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" id="sale_form_group">
                                    <label>Quantity:</label>
                                    <input type="text" id="quantity" class="form-control textbox" placeholder="Quantity"
                                        onkeydown="enter(event, this)">
                                    <small>Total Stock in Warehouse: <span id="display_limit"></span></small>
                                    <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group" id="qty_form_group">
                                    <label>Purchase:</label>
                                    <input type="text" class="form-control" placeholder="Price" id="saleprice" value="0"
                                        onkeydown="keyDown(event,this)">
                                    {{-- <small id="emailHelp" class="form-text text-muted">Press ENTER to ADD</small> --}}
                                </div>
                            </div>
                            @if (session()->get('settings.products.enable_advance_fields'))
                                <div class="col-md-1" style="display: none">
                                    <div class="form-group" id="qty_form_group">
                                        <label>Sale:</label>
                                        <input type="text" class="form-control" placeholder="Price" id="sprice" value="0">
                                        <small id="emailHelp" class="form-text text-muted">Press ENTER to ADD</small>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-1" style="display: none">
                                <div class="form-group" id="qty_form_group">
                                    <label>Minimum Sale price:</label>
                                    <input type="text" class="form-control" placeholder="Minimum Sale Price"
                                        id="minSalePrice" value="0">
                                    <small id="emailHelp" class="form-text text-muted">Press ENTER to ADD</small>
                                </div>
                            </div>

                        </div>
                        <div class="multiRowInput">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Product</th>
                                        <th>Warehouse</th>
                                        <th>Quantity</th>
                                        <th>Minimum Sale Price</th>
                                        @if (session()->get('settings.products.enable_advance_fields'))
                                            <th class="numeric">Sale Price</th>
                                        @endif
                                        <th class="numeric">Purchase Price</th>
                                        <th>Sub Total</th>
                                        <th>Option</th>
                                    </tr>
                                </thead>
                                <tbody id="appendMe">
                                    <tr class="parent_row"></tr>
                                </tbody>
                            </table>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </div>
                    <div class="content-panel">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <p><strong>Total Items: <span id="item_count">0</span></strong></p>
                                    <label>Notes:</label>
                                    <textarea name="description" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <h4>Mark As Paid and Add Transaction / Cash Purchase</h4>
                                    <input type="checkbox" name="mark_paid" data-toggle="switch">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label style="font-size: 16px">Sub Total:</label>&emsp;
                                    <span style="font-size: 16px" class="sub_total">0</span>
                                </div>
                                <div class="form-group">
                                    <label>Packaging & Shipping:</label>
                                    <input class="form-control " id="shipping" autocomplete="off" onkeyup="adjustTotal()"
                                        type="text" name="shipping">
                                </div>
                                <div class="form-group">
                                    <label>Discount:</label>
                                    <input class="form-control " id="discount" autocomplete="off" onkeyup="adjustTotal()"
                                        type="text" name="discount">
                                </div>
                                <div class="form-group">
                                    <label style="font-size: 20px">Total:</label>&emsp;
                                    <input type="hidden" name="total" value="0">
                                    <span style="font-size: 20px" id="total">0</span>
                                </div>
                                <div class="form-group">
                                    <label style="font-size: 16px">Previous Balance:</label>&emsp;
                                    <span style="font-size: 16px" id="previous_balance">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
                <center>{!! get_invoice_submit_buttons('Purchase', 'Add', url()->current()) !!}</center>
            </div>
        </div>
    </form>
    <input type="hidden" id="warehouse_limit">
@endsection
@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/js/jquery.numeric.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-switch.js') }}"></script>
    <!-- <script type="text/javascript" src="{{ url('productgroup.json') }}"></script> -->
    <script type="text/javascript" src="{{ url('warehouse.json') }}"></script>
    <script type="text/javascript">
        const show_purchase = "{{ is_allowed('product-show-purchase-price') }}";
        $(document).on("keyup", "input[name^=quantity],input[name^=sale_price]", function(e) {
            adjustTotal();
        });
        var products_json_d = [];
        var current_bill = [];

        function updateProducts() {
            $.getScript("{{ asset('products.json') }}", function() {
                var hrm =
                    '<label>Product:</label><select id="product" class="form-control product" placeholder="product"></select><br><a data-toggle="modal" href="#addproductmodal"  class="form-text text-muted"><small>Add Product</small></a><br><a href="javascript:void(0)" onclick="updateProducts()"><small>Update Products</small></a>';
                $("#product_form_group").html("");
                $("#product_form_group").append(hrm);
                choices('.product');
                $("#product").val("").trigger("change"); //select none
            });
        }
        $(document).ready(function() {
            updateProducts();
            $("[name=date]").select();
            // $(".customer").select2("open");
            $(".customer").val("{{ $request->customer }}").trigger("change");
            set_user_balance($('.customer').val(), "#previous_balance", true);
        });
        $(document).ready(function() {
            $("[data-toggle='switch']").wrap('<div class="switch" />').parent().bootstrapSwitch();
        });

        function enter(e, me) {
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9 || keyCode == 13) {
                e.preventDefault();
                if (me.id == 'saleprice') {
                    $("#sprice").focus();
                } else {
                    $("#saleprice").focus();
                }
            }
        }

        function choices(identifier) {
            // var warehouse_id = $("#warehouse_d").val();
            $(identifier).select2({
                placeholder: "Select a product",
                data: products_json_d,
                matcher: function(params, data) {
                    if ($.trim(params.term) === '') {
                        return data;
                    }
                    keywords = (params.term).split(" ");
                    for (var i = 0; i < keywords.length; i++) {
                        if ((((data.text).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1) && (((data
                                .barcode).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1))
                            return null;
                    }
                    return data;
                },
                minimumInputLength: 0,
            });
            $(identifier).on('select2:close', function(e) {
                $(identifier).blur();
                $.ajax({
                    url: "/product_purchase_json/",
                    data: {
                        'product_id': $(identifier).val(),
                        'customer_id': $('.customer').val()
                    }
                }).done(function(data) {
                    (show_purchase == 1) ? $("#saleprice").val(data): $("#saleprice").val(0);
                });
                $.ajax({
                    url: "/product_price_json/",
                    data: {
                        'product_id': $(identifier).val(),
                        'customer_id': 0
                    }
                }).done(function(data) {
                    $("#sprice").val(data);
                });
                $.ajax({
                    url: "/product_minimum_price/",
                    data: {
                        'product_id': $(identifier).val(),
                    }
                }).done(function(data) {
                    $("#minSalePrice").val(data);
                });

                // $("#warehouse").val(4); //selected by default, asked by shan
                // $("#warehouse").select2().trigger('change');
                $("#warehouse").select2('open').select2('close');
                // $("#warehouse").select2('open').select2('close')
            });
        }

        function warehouse(identifier) {
            // var warehouse_id = $("#warehouse_d").val();
            $(identifier).select2({
                placeholder: "Select a warehouse",
                data: warehouse_d,
                // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                minimumInputLength: 0,
            });
            $(identifier).on('select2:close', function(e) {
                load_start();
                $.ajax({
                    url: "/warehouse_product_json/",
                    data: {
                        'product_id': $("#product").val(),
                        'warehouse_id': $(identifier).val()
                    }
                }).done(function(data) {
                    load_end();
                    $("#warehouse_limit").val(data);
                    $("#display_limit").html(data);
                    $("#qty").val(1);
                }).error(function(d) {
                    load_end();
                    alertify.error('Some error occurred, try again').dismissOthers();
                    $(identifier).focus();
                });
                $("#quantity").select();
            });
        }
        choices('.product');
        warehouse('.warehouse');
        $(".customer").select2({
            placeholder: 'Select A Supplier',
            // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
        });
        $(".customer").on('select2:close', function(e) {
            $("#product").select2("open");
            $("#product").val("").trigger('change');
            set_user_balance($('.customer').val(), "#previous_balance", true);
        });
        $('#saleprice').numeric({
            negative: false,
            decimalPlaces: 2
        }, function() {
            alertify.error("No negative values").dismissOthers();
            this.value = "";
            this.focus();
        });
        $("#shipping").numeric({
            negative: false,
            decimalPlaces: 2
        }, function() {
            alertify.error("No negative values").dismissOthers();
            this.value = "";
            this.focus();
        });
        $("#discount").numeric({
            negative: true,
            decimalPlaces: 2
        }, function() {
            alertify.error("Non numeric values").dismissOthers();
            this.value = "";
            this.focus();
        });
        $("#quantity").numeric({
            decimalPlaces: 2,
            negative: false
        }, function() {
            alertify.error("Positive integers only").dismissOthers();
            this.value = "";
            this.focus();
        });

        function keyDown(e, me) {
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9 || keyCode == 13) {
                e.preventDefault();
                var product_id = $("#product").val();
                var type = $("#s_type").val();
                var warehouse_id = $("#warehouse").val();
                var qty = $('#quantity').val();
                if (type == "purchase") {
                    qty *= 1;
                } else {
                    qty *= -1;
                }
                var saleprice = parseFloat($('#saleprice').val()).toFixed(2);
                var s_price = parseFloat($('#sprice').val()).toFixed(2);
                var min_SalePrice = parseFloat($('#minSalePrice').val()).toFixed(2);
                if (showError($('.customer').val(), "customer", ".customer", "Please select Supplier") || showError(
                        product_id, "product", "#product", "Please select Product") || showError(warehouse_id, "warehouse",
                        "#warehouse", "Please select Warehouse") || showError(qty, "qty", "#quantity",
                        "Please enter quantity") || showError(saleprice, "sale", "#saleprice", "Please enter sale price")) {
                    return;
                }
                var current_stock = +(calculateConsumedQuantity(product_id, warehouse_id, qty));
                if (current_stock > +($("#warehouse_limit").val()) && 0 == 1) { //disable it
                    var remaining = +($("#warehouse_limit").val());
                    $("#qty_form_group").addClass('has-error');
                    alertify.error('Maximum Product Quantity in warehouse is: ' + remaining).dismissOthers();
                    //remove all entry
                    calculateConsumedQuantity(product_id, warehouse_id, qty * -1);
                    return;
                }
                if (qty == 0) {
                    alertify.error("Please Add Some Quantity").dismissOthers();
                    $("#quantity").select();
                    return;
                }
                var product_text = $('#product').select2('data');
                var warehouse_text = $("#warehouse").select2('data');
                $("#product").val("").trigger('change');
                // $("#warehouse").val("").trigger('change');
                $("#quantity").val("");
                $("#saleprice").val("");
                $("#sprice").val("");
                $("#min_SalePrice").val("");
                $("#warehouse_limit").val(0);
                $("#display_limit").html("");
                clearAllErrorClass();
                $("#product").select2('open');
                var v = '<td><input type="hidden" name="stype[]" value="' + type + '"><span>' + type +
                    '</span></td><td><input type="hidden" name="product[]" value="' + product_id + '"><span>' +
                    product_text[0].text + '</span></td><td><input type="hidden" name="warehouse[]" value="' +
                    warehouse_id + '"><span>' + warehouse_text[0].text +
                    '</span></td><td><input type="number" step="0.01" name="quantity[]" class="form-control" value="' +
                    qty +
                    '"></td><td><input type="number" step="0.01" min="0" name="minSalePrice[]" class="form-control" placeholder="Minimum Sale Price" value="' +
                    min_SalePrice +
                    '"></td>@if (session()->get('settings.products.enable_advance_fields'))<td><input type="number" step="0.01" min="0" name="s_price[]" class="form-control" placeholder="Sale Price" value="'+s_price+'"></td>@endif<td><input type="number" step="0.01" min="0" name="sale_price[]" class="form-control" placeholder="Purchase Price" value="' +
                    saleprice + '"></td><td><span class="sum">' + saleprice * qty +
                    '</span></td><td><a data-id="' + product_id + '" data-qty="' + qty + '" data-wh="' + warehouse_id +
                    '" style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td>';
                var xo = $("#appendMe").append("<tr class='parent_row'>" + v + "</tr>");
                adjustTotal();
            }
        }

        function adjustTotal() {
            updateItemCount();
            $("#appendMe input[name^=sale_price]").each(function(index, data) {
                // debugger
                var quantity = $("#appendMe input[name^=quantity]").eq(index).val();
                var sss = $(data).val() * quantity;
                $(data).parentsUntil("tbody").find(".sum").html(sss.toFixed(2));
            });
            var sum = 0;
            $(".sum").each(function() {
                sum += +($(this).html());
            });
            $(".sub_total").html(sum);
            var ship = +($("#shipping").val());
            var discount = +($("#discount").val());
            var total = ship + sum - discount;
            $("#total").html(total.toFixed(2));
            $("input[name=total]").val(total.toFixed(2));
        }

        function calculateConsumedQuantity(product_id, warehouse_id, quantity) {
            if (current_bill[product_id] == undefined) { //if new product
                current_bill[product_id] = [];
                current_bill[product_id][warehouse_id] = +(quantity);
                return +(quantity);
            }
            if (current_bill[product_id][warehouse_id] == undefined) { //if from new warehouse
                current_bill[product_id][warehouse_id] = +(quantity);
                return +(quantity);
            }
            //old is gold
            current_bill[product_id][warehouse_id] += +(quantity);
            return current_bill[product_id][warehouse_id];
        }

        function clean(e, ele) {
            calculateConsumedQuantity($(ele).data('id'), $(ele).data('wh'), -1 * $(ele).data('qty'));
            var row = $(ele).parent().parent();
            var boss = $("tbody#appendMe").children();
            if (boss.length < 2) {
                var xo = $("#appendMe").append("<tr class='parent_row'>" + $(".parent_row").html() + "</tr>");
                xo.children().last().children().find(".textbox").attr('onkeydown', 'keyDown(event, this)');
            }
            $(row).remove();
            adjustTotal();
            // debugger;
        }

        function isInt(n) {
            return (Math.floor(n) == n && $.isNumeric(n));
        }

        function showError(data, identifier, selector, message) {
            if ($.isNumeric(data)) {
                return false;
            }
            $("#" + identifier + "_form_group").addClass('has-error');
            $(selector).focus();
            alertify.error(message).dismissOthers();
            return true;
        }

        function clearAllErrorClass() {
            $("div.form-group").removeClass('has-error');
        }
        $(document).ready(function(e) {
            // $('input[name=date]').datepicker( "setDate", new Date());
        });
    </script>
    {!! hotkey_print_script() !!}
    @include("product_groups.widget",['purchase'=>true])
    @include("customers.modal",['purchase'=>true])
    @include("products.modal")
@endsection
