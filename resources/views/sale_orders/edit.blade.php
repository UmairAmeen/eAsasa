@extends('layout')
@section('css')
    <link href="{{ asset('assets/css/bootstrap-datepicker.css') }}" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-edit"></i> Update Sale Order</h1>
    </div>
@endsection

@section('content')
    @include('error')
    <form id="product_add_form" action="{{ route('sale_orders.update', $sale_order->id) }}">
        <div class="row">
            <div class="col-md-12">
                <div id="log"></div>
                <div class="col-md-12">
                    <div class="content-panel">
                        <div class="col-md-12" style="background-color: cornsilk;">
                            <div class="col-md-3">
                                <div class="form-group" id="date_form_group">
                                    <label>Date:</label>
                                    <input type="date" name="date" class="form-control" value="{{ $sale_order->date }}">
                                    <small id="emailHelp" class="form-text text-muted">This is required</small>
                                </div>
                            </div>
                            @if (env('DELIVERY_DATE'))
                                <div class="col-md-3">
                                    <div class="form-group" id="date_form_group">
                                        <label>Delivery Date:</label>
                                        <input type="date" name="delivery_date" class="form-control"
                                            @if ($sale_order->delivery_date)value="{{ date('Y-m-d', strtotime($sale_order->delivery_date)) }}"@endif>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-3">
                                <div class="form-group" id="date_form_group">
                                    <label>Order Number:</label>
                                    <input type="number" name="id" class="form-control" disabled readonly
                                        value="{{ $sale_order->invoice->id }}">
                                    <small id="emailHelp" class="form-text text-muted">Order number not editable</small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" id="date_form_group">
                                    <label>Bill Number:</label>
                                    <input type="text" name="bill_number" class="form-control"
                                        value="{{ $sale_order->invoice->bill_number }}">
                                    <small id="emailHelp" class="form-text text-muted">Optional, Bill number</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Order Status</label>
                                    <select class="form-control" name="status">
                                        <option value="0" {{ is_selected($sale_order->status, 0) }}> PENDING</option>
                                        <option value="1" {{ is_selected($sale_order->status, 1) }}>ACTIVE</option>
                                        <option value="3" {{ is_selected($sale_order->status, 3) }}>QUOTATION</option>
                                        <option value="4" {{ is_selected($sale_order->status, 4) }}>COMPLETED</option>
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
                                            value="{{ $sale_order->source }}">
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-12" style="background-color: cornsilk;">
                            <div class="col-md-3">
                                <div class="form-group" id="customer_form_group">
                                    <label>Customer:</label>
                                    <select name="customer" class="form-control customer" autofocus="autofocus">
                                        <option value="{{ $sale_order->customer_id }}">
                                            {{ $sale_order->customer->name }} |
                                            {{ $sale_order->customer->phone }}</option>
                                    </select>
                                    <small id="emailHelp" class="form-text text-muted">This is required</small>
                                </div>
                            </div>
                            @if(env('SALES_PERSON'))
                                <div class="col-md-3">
                                    <label for="sales_person">Sales Person</label>
                                    <input type="text" name="sales_person" id="sales_person" class="form-control textbox"
                                        value="{{ $sale_order->invoice->sales_person }}">
                                </div>
                            @elseif(!array($salesPersons))
                                <div class="col-md-3">
                                    <label for="sales_person">Sales Person</label>
                                    <select name="salesPerson" class="form-control salesPerson" autofocus="autofocus">
                                        @foreach ($salesPersons as $salesPerson)
                                            <option @if($sale_order->sales_people_id == $salesPerson->id) selected @endif value="{{ $salesPerson->id }}">{{ $salesPerson->name }}</option>
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
                                            value="{{ $sale_order->source }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="source_div">
                                        <label>Related To:</label>
                                        <input type="text" name="related_to" id="related_to" class="form-control textbox"
                                            value="{{ $sale_order->invoice->related_to }}">
                                    </div>
                                </div>
                            @endif

                        </div>
                        <div class="col-md-12" style="background-color: cornsilk;">
                                <div class="col-md-3">
                                    <div class="form-group" id="source_div">
                                        <input @if ($sale_order->invoice->is_manual == 1)class="btn btn-danger"@else class="btn btn-success "@endif type="text" readonly name="manualMode" id="manualMode"
                                            value="{{ $sale_order->invoice->is_manual == 1 ? 'Disable Manual Mode' : 'Enable Manual Mode' }}">
                                    </div>
                                </div>
                            @if (session()->get('settings.sales.is_sale_invoice'))
                                <div class="col-md-3">
                                    <div class="form-group" id="tax_div">
                                        <input type="text" readonly name="taxMode" id="taxMode"
                                            value="{{ $sale_order->invoice->is_tax == 1 ? 'Disable Tax Mode' : 'Enable Tax Mode' }}"
                                            @if ($sale_order->invoice->is_tax == 1)class="btn btn-danger"@else class="btn btn-success "@endif>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <hr>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group" id="product_form_group">
                                    <label>Product:</label>
                                    <select id="product" class="form-control product" placeholder="product"></select>
                                    <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" id="qty_form_group">
                                    <label>Quantity:</label>
                                    <input type="text" id="quantity" class="form-control textbox" placeholder="Quantity"
                                        onkeydown="enter(event, this)">
                                    <a id="emailHelp" data-toggle="modal" href="#addproductgrpoup"
                                        class="form-text text-muted" accesskey="y">Add Product Group (ALT + Y)</a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" id="sale_form_group">
                                    <label>Sale Price:</label>
                                    <input type="text" @if(!is_allowed('allow-edit-sale-price')) readonly @endif class="form-control" placeholder="Sale Price" id="saleprice"
                                        value="0" onkeydown="keyDown(event,this)">
                                    <small id="emailHelp" class="form-text text-muted">Press ENTER to ADD</small>
                                    <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
                                </div>
                            </div>
                            <!-- <div class="col-md-1">
                                                     <div class="form-group">
                                                     <label></label>
                                                       <button onclick="add()" class="btn btn-lg btn-primary">Add</button>
                                                     </div>
                                                   </div> -->
                        </div>
                        @php
                            $custom_explode = explode(',', session()->get('settings.sales.custom_items'));
                            $decoded = json_decode($sale_order->custom_inputs);
                            $merged_array = [];
                            foreach ($custom_explode as $key => $value) {
                                if (!empty($value)) {
                                    $merged_array[$value] = '';
                                }
                            }
                            foreach ($decoded as $key => $value) {
                                if ($value) {
                                    $merged_array[$key] = $value;
                                }
                            }
                        @endphp
                        @if (count($merged_array) > 0)
                            <div class="col-md-12">
                                @foreach ($merged_array as $key => $value)
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="custom input">{{ ucwords(ltrim($key)) }}:</label>
                                            <input type="text" class="form-control" name="custom_field[]"
                                                value="{{ $value ? ucwords(ltrim($value)) : '' }}" id="">
                                            <input type="hidden" class="form-control" name="custom_labels[]" id=""
                                                value="{{ $key }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            {{-- @elseif(count($custom_explode) > 0)
                        <div class="col-md-12">
                          @foreach ($custom_explode as $item)
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="custom input">{{ ucwords(ltrim($item)) }}:</label>
                                        <input type="text" class="form-control" name="custom_field[]" id="">
                                        <input type="hidden" class="form-control" name="custom_labels[]" id=""
                                            value="{{ $item }}">
                                    </div>
                                </div>
                            @endforeach
                        </div> --}}
                        @endif
                        <div class="multiRowInput">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="20%">Product</th>
                                        @if(env('UUID'))
                                            <th width="30%">Notes</th>
                                            <input type="hidden" name="uuid" id="uuid" value="1">
                                        @else
                                            <input type="hidden" name="uuid" id="uuid" value="0">
                                        @endif
                                        <!-- <th>Warehouse</th> -->
                                        <th width="20%">Quantity</th>
                                        <th width="10%" class="numeric">Sale Price</th>
                                        <th width="10%">Sub Total</th>
                                        <th width="10%">Option</th>
                                    </tr>
                                </thead>
                                <tbody id="appendMe">


                                    @foreach ($sale_order->invoice->orders as $order)
                                        <tr class="parent_row">
                                            <td><input type="hidden" name="product[]"
                                                    value="{{ $order->product_id }}">{{ $order->product->name . '-' . $order->product->brand }}
                                            </td>
                                        @if(env('UUID'))

                                            <td><input type="text" name="note[]" class="form-control"
                                                    value="{{ $order->note }}"></td>
                                        @endif
                                            @if ($order->product->unit_id == 3)
                                                <td><input type="text" class="" name="quantity[]"
                                                        onchange="adjustTotal()" value="{{ $order->quantity }}">
                                                @else
                                                <td><input type="text" class="" name="quantity[]"
                                                        onchange="adjustTotal()"
                                                        value="{{ floatval($order->quantity) }}">
                                            @endif

                                            <span>{{ $order->product->unit->name }}</span>
                                            </td>

                                            <td><input type="number" @if(!is_allowed('allow-edit-sale-price')) readonly @endif class="salePriceModify form-control"
                                                    name="sale_price[]" onchange="minimum_sale_price(this)"
                                                    value="{{ $order->salePrice }}">
                                                <small class="orignal"
                                                    style="display: none">{{ $order->product->salePrice }}</small>
                                            </td>
                                            {{-- <td><small name="sale_price_orignal[]" readonly value="{{ $order->product->salePrice }}"></small>
                                                </td> --}}

                                            <td class="sum">
                                                {{ $order->quantity * $order->salePrice }}
                                            </td>

                                            <td><a style="cursor: pointer;" onclick="clean(event, this)"><span
                                                        class="fa fa-trash"></span>Clean</a></td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="PUT">

                        </div>
                    </div>
                    <div class="content-panel">
                        <div class="row">
                            <div class="col-md-8">
                                <p>
                                    <strong>Total Items:
                                        <span id="item_count">{{ count($sale_order->orders) }}</span>
                                    </strong>
                                </p>
                                <div class="form-group">
                                    <label>Notes:</label>
                                    <textarea name="description"
                                        class="form-control">{{ $sale_order->invoice->description }}</textarea>
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
                                                <h4 for="payment">Payment Type: </h4>
                                                {!! Form::select('payment_type', ['' => 'Select', 'cash' => 'Cash', 'cheque' => 'Cheque', 'transfer' => 'Transfer'], !empty($transaction->payment_type) ? $transaction->payment_type : '', ['class' => 'form-control', 'id' => 'payment_type', 'style' => 'width:50%']) !!}
                                            </div>
                                        </div>
                                        <div class="col-xs-6">

                                            <div class="form-group">
                                                <h4>Bank(optional):</h4>
                                                {!! Form::select('bank', App\BankAccount::GetBankDropDown(), !empty($transaction->bank) ? $transaction->bank : '', ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <h4>Transaction ID(optional):</h4>
                                    <input class="form-control" type="text" name="transaction_id"
                                        value="{{ !empty($transaction->transaction_id) ? $transaction->transaction_id : '' }}"
                                        style="width: 76%">
                                </div>


                                {{-- <div class="form-group">
                                    <h4>POST Order</h4>
                                    <input type="checkbox" name="post_order" data-toggle="switch" checked>
                                </div>
                                <div class="form-group" id="post_order_display" style="display: block;">
                                    <label>Payment Paid</label>
                                    <input type="text" name="payment" class="form-control" value="">
                                </div> --}}
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label style="font-size: 16px">Sub Total:</label>&emsp;
                                    <span style="font-size: 16px" class="sub_total">0</span>
                                </div>
                                <div class="form-group">
                                    <label>Packaging & Shipping:</label>
                                    <input class="form-control " id="shipping" autocomplete="off" onkeyup="adjustTotal()"
                                        type="text" name="shipping" value="{{ $sale_order->invoice->shipping }}">
                                </div>
                                <div class="form-group">
                                    <label>Discount:</label>
                                    <input class="form-control " id="discount" autocomplete="off" onkeyup="checkFixedDiscount(this);adjustTotal()"
                                        type="text" name="discount" min="0" max="{{ auth::user()->allowed_discount_pkr }}" value="{{ $sale_order->invoice->discount }}">
                                </div>
                                <div class="input-group" id="taxId">
                                    <span class="input-group-addon" id="basic-addon2">Tax</span>
                                    <input class="form-control" id="taxAmount" type="number" readonly
                                        value="{{ $sale_order->invoice->tax }}" name="taxAmount">
                                </div>
                                <br>
                                @if (session()->get('settings.sales.is_sale_invoice'))
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon2">Tax Percentage</span>
                                        <input type="number" class="form-control" name="taxPercent" id="taxPercent"
                                            value="{{ $sale_order->invoice->tax_percentage * 100 }}" onkeyup="adjustTotal()"
                                            onchange="adjustTotal()">
                                        <input type="hidden" name="taxPercent1" id="taxPercent1"
                                            value="{{ $sale_order->invoice->tax_percentage * 100 > 0 ? $sale_order->invoice->tax_percentage * 100 : App\Invoice::GetSaleTaxPercentage() * 100 }}">
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label style="font-size: 20px">Total:</label>&emsp;
                                    <span style="font-size: 20px" id="total">0</span>
                                    <input type="hidden" name="total" id="total_input" value="">
                                </div>
                                @if ($sale_order->invoice->is_manual == true)
                                    <div class="form-group" id="manual_total_div">
                                        <label style="font-size: 20px">Manual Total:</label>&emsp;
                                        <input type="number" class="form-control" id="manual_total" name="manual_total"
                                            value="{{ getManualAmount($sale_order->invoice->id) }}">
                                    </div>
                                @else
                                    <div class="form-group" id="manual_total_div" style="display: none">
                                        <label style="font-size: 20px">Manual Total:</label>&emsp;
                                        <input type="number" class="form-control" id="manual_total" name="manual_total"
                                            value="">
                                    </div>
                                @endif
                                {{-- @if (checkPayment($sale_order->invoice->id) > 0) --}}
                                    <div class="form-group" id="amount_paid">
                                        <label style="font-size: 20px">Advance Amount Paid:</label>&emsp;
                                        <input type="number" class="form-control" id="amount_paid" name="amount_paid"
                                            value="{{ (checkPayment($sale_order->invoice->id) > 0 ) ? checkPayment($sale_order->invoice->id) : 0 }}">
                                    </div>
                                {{-- @endif --}}

                                <div class="form-group">
                                    <label style="font-size: 16px">Previous Balance:</label>&emsp;
                                    <span
                                        style="font-size: 16px">{{ number_format(getInvoiceBalance($sale_order->invoice_id)) }}</span>
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
                    {!! get_invoice_submit_buttons('Sale Order', 'Update') !!}
                </center>
            </div>
        </div>
    </form>
    <!-- <input type="hidden" id="warehouse_limit"> -->
@endsection
@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/js/jquery.numeric.js') }}"></script>
    <!-- <script type="text/javascript" src="{{ url('products.json') }}?v={{ versioning('products') }}"></script> -->
    <script type="text/javascript">
        var products_json_d = [];
        var current_bill = [];
        var toggleTax = $('#taxMode');
        var toggleManual = $('#manualMode');
        var toggleTaxId = $('#taxId');
        toggleTax.click(function() {
            if (toggleTax.val() == "Enable Tax Mode") {
                toggleTax.val('Disable Tax Mode');
                $('#taxPercent').val($('#taxPercent1').val());
                adjustTotal();
            } else {
                toggleTax.val('Enable Tax Mode');
                $('#taxPercent').val(0);
                adjustTotal();
            }
            // toggleTaxId.toggle();
            if (toggleTax.toggleClass('btn-danger')) {
                toggleTax.toggleClass('btn-success');
            }
        });
        toggleManual.click(function() {
            if (toggleManual.val() == "Enable Manual Mode") {
                toggleManual.val('Disable Manual Mode');
            } else {
                toggleManual.val('Enable Manual Mode');
                if (toggleManual.toggleClass('btn-danger')) {
                    toggleManual.toggleClass('btn-success');
                }
                var manual = $(".salePriceModify");
                var original = document.querySelectorAll('small[class="orignal"]');
                for (let index = 0; index < original.length; index++) {
                    manual[index].value = original[index].innerHTML;
                    console.log(index);
                }
                adjustTotal();
            }
            $('#manual_total_div').toggle();
        });

        function adjustTotal() {
            updateItemCount();
            $("#appendMe input[name^=sale_price]").each(function(index, data) {
                // debugger
                var quantity = $("#appendMe input[name^=quantity]").eq(index).val();
                $(data).parentsUntil("tbody").find(".sum").html(($(data).val() * quantity).toFixed(2));
            });
            var sum = 0;
            $(".sum").each(function() {
                sum += +parseFloat($(this).html());
            });
            $(".sub_total").html(sum.toFixed(2));
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
            } else {
                $('#taxAmount').val(0);
            }
            $("#total").html(total.toFixed(2));
            $("input[name=total]").val(total.toFixed(2));
        }

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
            choices('.product');
            set_user_balance($('.customer').val(), "#previous_balance");
            adjustTotal();
        });

        function enter(e, me) {
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9 || keyCode == 13) {
                e.preventDefault();
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
                // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
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
                $("#quantity").focus();
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
                cache: true
            },
            // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
        });
        $(".customer").on('select2:close', function(e) {
        });

        $(".customer").on('select2:close', function(e) {
            $(".salesPerson").focus();
        });
        $(".salesPerson").on('select2:close', function(e) {
            $("#product").focus();
            $("#product").val("").trigger('change');
            set_user_balance($('.customer').val(), "#previous_balance");
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
                // var warehouse_id = $("#warehouse").val();
                var qty = parseFloat($('#quantity').val());
                var uuid = '';
                if($("#uuid").val() == 1){
                    uuid = '<td><input type="text" name="note[]" class="form-control" value=""></td>';
                }
                var saleprice = parseFloat($('#saleprice').val());
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
                // var current_stock  = +(calculateConsumedQuantity(product_id, warehouse_id, qty));
                // if (current_stock > +( $("#warehouse_limit").val()))
                // {
                //   var remaining = +($("#warehouse_limit").val());
                //   $("#qty_form_group").addClass('has-error');
                //   alertify.error('Maximum Product Quantity in warehouse is: '+ remaining).dismissOthers();
                //   //remove all entry
                //   calculateConsumedQuantity(product_id, warehouse_id, qty*-1);
                //   return;
                // }
                var product_text = $('#product').select2('data');
                // var warehouse_text = $("#warehouse").select2('data');
                $("#product").val("").trigger('change');
                // $("#warehouse").val("").trigger('change');
                $("#quantity").val("");
                $("#saleprice").val("");
                is_read_only = "{{ !is_allowed('allow-edit-sale-price') }}" ? "readonly" : "";
                // $("#warehouse_limit").val(0);
                clearAllErrorClass();
                $("#product").focus();
                var v = '<td><input type="hidden" name="product[]" value="' + product_id + '"><span>' + product_text[0]
                    .text + '</span></td>' +
                    uuid +
                    '<td><input type="text" onchange="adjustTotal()" name="quantity[]" class="form-control" value="' + qty +
                    '"><span>' + product_text[0].unit + '</span></td>' +
                    '<td><input type="text" onchange="adjustTotal()" name="sale_price[]" class="form-control" '+is_read_only+' placeholder="Sale Price" value="' +
                    saleprice + '"></td><td><span class="sum">' + saleprice * qty +
                    '</span></td><td><a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td>';
                var xo = $("#appendMe").append("<tr class='parent_row'>" + v + "</tr>");
                adjustTotal();
            }
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

        function minimum_sale_price(e) {
            var prod_id = $(e).parent().parent().children(":first").children().val();
            var prod_price = $(e).val();
            $.each(products_json_d, function(idss, objss) {
                if (objss.id == prod_id) {
                    if (objss.min_sale_price) {
                        if (parseFloat(prod_price) < parseFloat(objss.min_sale_price)) {
                            alertify.error('Minimum Product Price is ' + objss.min_sale_price).dismissOthers();
                            $(e).val(parseFloat(objss.min_sale_price));
                            // $("#saleprice").select();
                            // quit;
                            return false;
                        }
                    }
                }
            });
            adjustTotal();
        }
        function checkFixedDiscount(e){
          if(parseFloat($(e).val()) > parseFloat($(e).attr('max'))){
            $(e).val($(e).attr('max'));
          }
        }
    </script>
    {!! hotkey_print_script() !!}
    @include("product_groups.widget")
    @include("products.modal")
@endsection
