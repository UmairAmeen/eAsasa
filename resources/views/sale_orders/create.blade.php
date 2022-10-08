@extends('layout')
@section('css')
    <link href="{{ asset('assets/css/bootstrap-datepicker.css') }}" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> Create Sale </h1>
    </div>
@endsection

@section('content')
    @include('error')
    <form id="product_add_form" action="{{ route('sale_orders.store') }}">

        <div class="row">
            <div class="col-md-12">
                <div id="log"></div>
                <div id="error_log"></div>
                <div class="col-md-12">
                    <div class="content-panel">
                        <div class="col-md-12" style="    background-color: cornsilk;">
                            <div class="col-md-3">
                                <div class="form-group" id="date_form_group">
                                    <label>Date:</label>
                                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                                    <small id="emailHelp" class="form-text text-muted">This is required</small>
                                </div>
                            </div>
                            @if (env('DELIVERY_DATE'))
                                <div class="col-md-3">
                                    <div class="form-group" id="date_form_group">
                                        <label>Delivery Date:</label>
                                        <input type="date" name="delivery_date" class="form-control"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-3">
                                <div class="form-group" id="date_form_group">
                                    <label>Bill Number:</label>
                                    <input type="text" name="bill_number" class="form-control">
                                    <small id="emailHelp" class="form-text text-muted">Optional</small>
                                </div>
                            </div>

                            {{-- Specifically changed for Wood castle                                
                                <div class="col-md-3">
                                <div class="form-group" id="date_form_group">
                                    <label>Order Number:</label>    
                                    <input type="number" name="id" class="form-control"
                                        value="{{ $sale_order->invoice->id }}">
                                    <small id="emailHelp" class="form-text text-muted">Optional, Order number</small>
                                </div>
                            </div> --}}

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Order Status</label>
                                    <select onchange="postOrderOffOnPending(this)" class="form-control" name="status">
                                        <option value="0" selected="selected">PENDING</option>
                                        <option value="1">ACTIVE</option>
                                        <option value="3">QUOTATION</option>
                                        <option value="4">COMPLETED</option>
                                    </select>
                                </div>
                            </div>
                            {{-- Environment variable set to true for RAZA TRACTOR client.
                            This variable is only added to the .env file of relevant instance.
                            For all other instances it will be null to implement generic code.
                            note: "This change eliminates the need to add a particular variable
                            in all instances to cater this change of a particular client". --}}
                            @if (env('RAZA'))
                            <div class="col-md-3">
                                <div class="form-group" id="source_div">
                                    <label>Noted By:</label>
                                    <input type="text" name="source" id="source" class="form-control textbox"
                                        placeholder="Noted By">
                                </div>
                            </div>
                        @endif


                        </div>
                        <div class="col-md-12" style="background-color: cornsilk;">

                            <div class="col-md-3">
                                <div class="form-group" id="customer_form_group">
                                    <label>Customer:</label>
                                    <select name="customer" class="form-control customer" autofocus="autofocus"></select>
                                    <small id="emailHelp" class="form-text text-muted">This is required</small>
                                    <br>
                                    <a id="emailHelp" data-toggle="modal" href="#addcustomermodal"
                                        class="form-text text-muted">Add Customer</a>
                                </div>
                            </div>
                            <?php $salesPersonBool = !empty($salesPersons[0])?true:false;?>
                            @if(env('SALES_PERSON'))
                            <div class="col-md-3">
                                <label for="sales_person">Sales Person</label>
                                <input type="text" name="sales_person" id="sales_person" class="form-control textbox"
                                    placeholder="Enter sales person name">
                            </div>
                            @elseif($salesPersonBool)
                            <div class="col-md-3">
                                <label for="sales_person">Sales Person</label>
                                <select name="salesPerson" class="form-control salesPerson" autofocus="autofocus">
                                    @foreach ($salesPersons as $salesPerson)                                        
                                        <option value="{{ $salesPerson->id }}">{{ $salesPerson->name }}</option>
                                    @endforeach
                                </select>
                                <a id="emailHelp" data-toggle="modal" href="#addSalesPersonmodal"
                                class="form-text text-muted">Add Sales Person</a>
                            </div>

                            @endif
                            @if (env('RELATED_TO_SOURCE'))
                                <div class="col-md-3">
                                    <div class="form-group" id="source_div">
                                        <label>Source:</label>
                                        <input type="text" name="source" id="source" class="form-control textbox"
                                            placeholder="From where you heard about us">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="source_div">
                                        <label>Related To:</label>
                                        <input type="text" name="related_to" id="related_to" class="form-control textbox"
                                            placeholder="Related to">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-12" style="background-color: cornsilk;">
                                <div class="col-md-3">
                                    <div class="form-group" id="source_div">
                                        <input class="btn btn-success" type="text" readonly name="manualMode" id="manualMode"
                                            value="Enable Manual Mode">
                                    </div>
                                </div>
                            @if (session()->get('settings.sales.is_sale_invoice'))
                                <div class="col-md-3">
                                    <div class="form-group" id="tax_div">
                                        <input class="btn btn-success" type="text" readonly name="taxMode" id="taxMode"
                                            value="Enable Tax Mode">
                                    </div>
                                </div>
                            @endif
                        </div>
                        <hr>
                        <div class="col-md-12">
                            <div class="col-md-4">

                                <div class="form-group" id="product_form_group">
                                    <label>Product:</label>
                                    <select id="product" class="form-control product" placeholder="product">
                                        <option selected="" disabled="">Select Product</option>
                                    </select>
                                    <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" id="sale_form_group">
                                    <label>Quantity:</label>
                                    <input type="text" id="quantity" class="form-control textbox" placeholder="Quantity"
                                        onkeydown="enter(event, this)">
                                    <a id="emailHelp" data-toggle="modal" href="#addproductgrpoup"
                                        class="form-text text-muted" accesskey="y">Add Product Group (ALT + Y)</a>
                                    <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group" id="qty_form_group">
                                    <label>Sale Price:</label>
                                    <input type="text" @if(!is_allowed('allow-edit-sale-price')) readonly @endif class="form-control" placeholder="Sale Price" id="saleprice"
                                        value="0" onkeydown="keyDown(event,this)">
                                    <small id="emailHelp" class="form-text text-muted">Press ENTER to ADD</small>
                                </div>
                            </div>
                            <!-- <div class="col-md-1">
                                                 <div class="form-group">
                                                 <label></label>
                                                   <button onclick="add()" class="btn btn-lg btn-primary">Add</button>
                                                 </div>
                                               </div> -->
                        </div>

                        @php($custom_explode = explode(',', session()->get('settings.sales.custom_items')))
                        @if (count($custom_explode) > 0)
                            <div class="col-md-12">
                                @foreach ($custom_explode as $item)
                                    @if (!empty($item))
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="custom input">{{ ucwords(ltrim($item)) }}:</label>
                                                <input type="text" class="form-control" name="custom_field[]" id="">
                                                <input type="hidden" class="form-control" name="custom_labels[]" id=""
                                                    value="{{ $item }}">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif


                        <div class="multiRowInput">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <!-- <th>Warehouse</th> -->
                                        @if(env('UUID'))
                                            <th>Notes</th>
                                            <input type="hidden" name="uuid" id="uuid" value="1">
                                        @else
                                            <input type="hidden" name="uuid" id="uuid" value="0">
                                        @endif
                                        <th>Quantity</th>
                                        <th class="numeric">Sale Price</th>
                                        <th>Sub Total</th>
                                        <th>Option</th>
                                    </tr>
                                </thead>
                                <tbody id="appendMe">

                                    <tr class="parent_row">

                                    </tr>

                                </tbody>
                            </table>

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        </div>

                    </div>
                    <div class="content-panel">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <p>

                                        <strong>Total Items: <span id="item_count">0</span></strong>
                                    </p>
                                    <label>Notes:</label>
                                    <textarea name="description" class="form-control"></textarea>
                                </div>
                                {{-- <div class="form-group">
                                    <h4>Mark As Paid and Add Transaction &amp; POST Order</h4>
                                    <input type="checkbox" name="mark_paid" data-toggle="switch">
                                </div> --}}

                                
                                {{-- Environment variable set to true for RAZA TRACTOR client.
                                This variable is only added to the .env file of relevant instance.
                                For all other instances it will be null to implement generic code.
                                note: "This change eliminates the need to add a particular variable
                                in all instances to cater this change of a particular client". --}}
                                @if(!env('RAZA'))
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <h4 for="payment">Payment Type:</h4>
                                                <select class="form-control" name="payment_type" id="payment_type"
                                                    style="width: 50%">
                                                    <option value="cash">Cash</option>
                                                    <option value="cheque">Cheque</option>
                                                    <option value="transfer">Online transfer</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">

                                            <div class="form-group">
                                                <h4>Bank(optional):</h4>
                                                {!! Form::select('bank', App\BankAccount::GetBankDropDown(), null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <h4>Transaction ID(optional):</h4>
                                    <input class="form-control" type="text" name="transaction" style="width: 76%">
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



                                    <div class="input-group">
                                        <input class="form-control " min="0" max="{{ auth()->user()->allowed_discount }}" id="discount_percentage"
                                            autocomplete="off" onkeyup="adjustDiscount(this); adjustTotal()" type="number"
                                            placeholder="Enter Discount in Percentage" aria-describedby="basic-addon2">
                                        <span class="input-group-addon" id="basic-addon2">%</span>
                                    </div>
                                    <br>
                                    <div class="input-group" @if(!is_allowed('fixed-discount')) style="display: none" @endif>
                                        <span class="input-group-addon" id="basic-addon2">Fixed</span>

                                        <input class="form-control " id="discount" autocomplete="off"
                                            onkeyup="checkFixedDiscount(this);adjustTotal()" min="0" max="{{ auth::user()->allowed_discount_pkr }}" type="text" name="discount"
                                            placeholder="Discount in Fixed Price">
                                    </div>
                                    <br>

                                    <div class="input-group" id="taxPercentage" style="display: none">
                                        <span class="input-group-addon" id="basic-addon2">Tax Percentage</span>
                                        <input type="number" class="form-control" name="taxPercent" id="taxPercent"
                                            onkeyup="adjustTotal()" onchange="adjustTotal()"
                                            value="{{ App\Invoice::GetSaleTaxPercentage() * 100 }}">
                                    </div>
                                    <br>


                                    <div class="input-group" id="taxId" style="display: none">
                                        <span class="input-group-addon" id="basic-addon2">Tax</span>
                                        <input class="form-control" id="taxAmount" type="number" readonly
                                            name="taxAmount">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="font-size: 20px">Total:</label>&emsp;
                                    <input type="hidden" name="total1" id="total1" value="0">
                                    <span style="font-size: 20px" id="total">0</span>
                                </div>
                                <div class="form-group" id="manual_total_div" style="display: none">
                                    <label style="font-size: 20px">Manual Total:</label>&emsp;
                                    <input type="number" class="form-control" id="manual_total" name="manual_total"
                                        value="0">
                                </div>
                                <div class="form-group">
                                    <label style="font-size: 16px">Remaining Balance:</label>&emsp;
                                    <span style="font-size: 16px" id="previous_balance">0</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-8">
                                    <h4>POST Order</h4>
                                    <div id="switch-on"></div>
                                    <input type="checkbox" name="post_order" id="post_order" style="display: none"
                                        {{ session()->get('settings.sales.enable_post_order') ? 'checked' : '' }}>
                                </div>
                                <div class="col-md-4">
                                    <div id="post_order_display"
                                    @if(session()->get('settings.sales.enable_post_order'))
                                    style="display: block;" @else style="display: none;" @endif>
                                        <label>Payment Paid</label>
                                        <input type="text" onkeyup="currentOrderBalance()" name="payment" id="payment"  class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-md-4" style="margin-top: 10px">
                                    <div class="form-group">
                                        <label style="font-size: 15px">Current Order Balance: </label>
                                        <span style="font-size: 20px" name="current_order_balance" id="current_order_balance">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-1">
                             <div class="form-group">
                             <label></label>
                               <button onclick="add()" class="btn btn-lg btn-primary">Add</button>
                             </div>
                           </div> -->
                </div>



            </div>
            <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
                <center>
                    {!! get_invoice_submit_buttons('Sale Order', 'Add', url()->current()) !!}
                </center>
            </div>
        </div>
    </form>
    <!-- <input type="hidden" id="warehouse_limit"> -->
    <input type="hidden" id="allowed_discount" value="{{ auth()->user()->allowed_discount }}">
@endsection
@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/js/jquery.numeric.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-switch.js') }}"></script>
    <!-- <script type="text/javascript" src="{{ url('products.json') }}?v={{ versioning('products') }}"></script> -->
    <script type="text/javascript">
        var products_json_d = [];
        var current_bill = [];
        var toggleManual = $('#manualMode');

        function paymenyPaidToZero(){
            $('#payment').val(0);
        }
        function postOrderOffOnPending(e){
            var change_checkbox = $('#post_order');
            if(e.value == 3){
                change_checkbox.prop('checked',false).trigger('change');
                $('#switch-on').dxSwitch({value: false});
                $("#post_order_display").hide();
                paymenyPaidToZero();
                currentOrderBalance(e);
            }
            else{
                change_checkbox.prop('checked',true).trigger('change');
                $('#switch-on').dxSwitch({value: true});
                $("#post_order_display").show();
            }
        }
        toggleManual.click(function() {
            if (toggleManual.val() == "Enable Manual Mode") {
                toggleManual.val('Disable Manual Mode');
            } else {
                toggleManual.val('Enable Manual Mode');
            }
            $('#manual_total_div').toggle();
            toggleManual.toggleClass('btn-danger');
        });
        var sw = null;
        var new_switch = null;
        $(document).ready(function() {
            sw = $("[data-toggle='switch']").wrap('<div class="switch" />').parent().bootstrapSwitch();
            new_switch = $('#switch-on').dxSwitch({value:$("#post_order").is(':checked'), onValueChanged(data) {
                $("#post_order").prop('checked',data.value).trigger('change');
            }
        });
            updateProducts();
            $("[name=date]").select();
            choices('.product');
            // $(".customer").select2("open");
            // $('input[name=date]').datepicker( "setDate", new Date());
        });
        var toggleTax = $('#taxMode');
        var toggleTaxId = $('#taxId');
        var toggleTaxPercent = $('#taxPercentage');

        toggleTax.click(function() {
            if (toggleTax.val() == "Enable Tax Mode") {
                toggleTax.val('Disable Tax Mode');
                adjustTotal();
            } else {
                toggleTax.val('Enable Tax Mode');
                adjustTotal();
            }
            toggleTaxId.toggle();
            toggleTaxPercent.toggle();
            toggleTax.toggleClass('btn-danger');
        });
        $(document).on("keyup", "input[name^=quantity],input[name^=sale_price]", function(e) {
            adjustTotal();
        });

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
        $("[name=post_order]").change(function(e) {
            // alert("changed");
            $("#post_order_display").hide();
            paymenyPaidToZero();
            currentOrderBalance();
            if ($(this).prop("checked")) {
                $("#post_order_display").show();
            }
        });

        function enter(e, me) {
            var keyCode = e.keyCode || e.which;

            if (keyCode == 9 || keyCode == 13) {
                e.preventDefault();
                $("#saleprice").focus();
                $("#saleprice").select();
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
                    url: "/product_price_json/",
                    data: {
                        'product_id': $(identifier).val(),
                        'customer_id': $('.customer').val()
                    }
                }).done(function(data) {
                    $("#saleprice").val(data);
                });

                // $("#warehouse").val("");
                // $("#warehouse").trigger('change');
                $("#quantity").select();
            });
        }
        $(".salesPerson").select2();
        $(".customer").select2({
            placeholder: 'Select A customer',
            ajax: {
                url: "/pagination_customer_json",
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
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 10) < data.total_count
                        }
                    };
                },
                cache: true
            },
            matcher: function(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }

                keywords = (params.term).split(" ");

                for (var i = 0; i < keywords.length; i++) {
                    if (((data.text).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1)
                        return null;
                }
                return data;
            },
            // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
        });
        function triggerProductsDropdown(){
            $("#product").select2("open");
            $("#product").val("").trigger('change');
            set_user_balance($('.customer').val(), "#previous_balance");
        }
        $(".customer").on('select2:close', function(e) {
            if('<?php echo $salesPersonBool; ?>' == 1){$(".salesPerson").select2("open");}
            else{ triggerProductsDropdown();}
        });
        $(".salesPerson").on('select2:close', function(e) {
            triggerProductsDropdown();
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
                var uuid = '';
                if($("#uuid").val() == 1){
                    uuid = '<td><input type="text" name="note[]" class="form-control" value=""></td>';
                }
                // var warehouse_id = $("#warehouse").val();
                var qty = $('#quantity').val();
                var saleprice = $('#saleprice').val();
                if (showError($('.customer').val(), "customer", ".customer", "Please select Customer") || showError(
                        product_id, "product", "#product", "Please select Product") || showError(qty, "qty", "#quantity",
                        "Please enter quantity") || showError(saleprice, "sale", "#saleprice", "Please enter sale price")) {
                    return;
                }
                $.each(products_json_d, function(idss, objss) {
                    if (objss.id == product_id) {
                        if (parseFloat(saleprice) < parseFloat(objss.min_sale_price)) {
                            alertify.error('Minimum Product Price is ' + objss.min_sale_price).dismissOthers();
                            $("#saleprice").select();
                            quit;
                        }
                        return false;
                    }
                });
                var product_text = $('#product').select2('data');
                $("#product").val("").trigger('change');
                $("#quantity").val("");
                $("#saleprice").val("");
                is_read_only = "{{ !is_allowed('allow-edit-sale-price') }}" ? "readonly" : "";

                clearAllErrorClass();
                $("#product").select2("open");
                var v = '<td><input type="hidden" name="product[]" value="' + product_id + '"><span>' + product_text[0]
                    .text + '</span></td>' +
                    uuid +
                    '<td><input type="number" min="0" step="0.01" name="quantity[]" value="' +
                    qty +
                    '" required> <span> ' + product_text[0].unit +
                    ' </span></td><td><input type="number" min="0" step="0.01" name="sale_price[]" '+is_read_only+' class="form-control" placeholder="Sale Price" value="' +
                    saleprice + '" required></td><td><span class="sum">' + saleprice * qty +
                    '</span></td><td><a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td>';
                var xo = $("#appendMe").append("<tr class='parent_row'>" + v + "</tr>");
                adjustTotal();
            }
        }

        function adjustTotal() {
            updateItemCount();
            $("#appendMe input[name^=sale_price]").each(function(index, data) {
                var quantity = $("#appendMe input[name^=quantity]").eq(index).val();
                $(data).parentsUntil("tbody").find(".sum").html(($(data).val() * quantity).toFixed(2));
            });
            var sum = 0;
            $(".sum").each(function() {
                sum += +parseFloat($(this).html());
            });
            $(".sub_total").html(thousands_separators(sum.toFixed(2)));
            var ship = +($("#shipping").val());
            var discount = +($("#discount").val());
            var total = ship + sum - discount;
            var tax_mode = $('#taxMode').val();
            var tax_percent = $('#taxPercent').val();
            var tax_amount = 0;
            var tax_id = document.getElementById("taxId");
            if (tax_mode == "Disable Tax Mode") {
                tax_amount = total * (tax_percent / 100);
                $('#taxAmount').val(tax_amount);
                total = total + tax_amount;
            }
            total = Math.round(total);
            $('#total').html(thousands_separators(total.toFixed(2)));
            $('#current_order_balance').html(thousands_separators(total.toFixed(2)));
            $('#total1').val(total);
            // $("#total").html(total.toFixed(2));
            // $("input[name=total]").val(total.toFixed(2));
        }

        function calculateConsumedQuantity(product_id, warehouse_id, quantity) {
            //if new product
            if (current_bill[product_id] == undefined) {
                current_bill[product_id] = [];
                current_bill[product_id][warehouse_id] = +(quantity);
                return +(quantity);
            }
            //if from new warehouse
            if (current_bill[product_id][warehouse_id] == undefined) {
                current_bill[product_id][warehouse_id] = +(quantity);
                return +(quantity);
            }
            //old is gold
            current_bill[product_id][warehouse_id] += +(quantity);
            return current_bill[product_id][warehouse_id];
        }

        function clean(e, ele) {
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

        function adjustDiscount(e) {
            let allowed_discount = parseInt($("#allowed_discount").val());
            if ($(e).val() < 0) {
                $(e).val(0);
            }
            if ($(e).val() > allowed_discount) {
                $(e).val(allowed_discount);
            }
            var sum = 0;
            $(".sum").each(function() {
                sum += +($(this).html());
            });
            var percent = $(e).val() * sum / 100;
            percent = Math.round(percent, 2);
            $("input[name=discount]").val(percent);
        }
        function currentOrderBalance(){
            let total = parseFloat($('#total1').val());
            let amount_paid = parseInt($('#payment').val());
            let remaining_amount = total - ((amount_paid > 0)?amount_paid:0);
            $('#current_order_balance').html(remaining_amount);
        }
        function checkFixedDiscount(e){
          if(parseFloat($(e).val()) > parseFloat($(e).attr('max'))){
            $(e).val($(e).attr('max'));
          }
        }
    </script>
    {{-- <script type="text/javascript" src="{{ url('productgroup.json') }}"></script> --}}
    {!! hotkey_print_script() !!}
    @include("product_groups.widget")
    @include("customers.modal")
    @include("sales_person.modal")
    @include("products.modal")
@endsection
