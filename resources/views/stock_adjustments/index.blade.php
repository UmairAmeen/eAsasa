@extends('layout')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/choices.min.css') }}">
    <link href="{{ asset('assets/css/bootstrap-datepicker.css') }}" rel="stylesheet">
@endsection
@section('header')
    <div class="row">
        @if (is_allowed('inventory-out'))
            <div class="col-md-4">
                <center>
                    <a href="#addproduct" id="add_sale" data-toggle="modal">
                        <div class="col-md-6 col-sm-6 box0">
                            <div class="box1">
                                <span class="fa fa-arrow-up"></span>
                                <h3>Inventory Out</h3>
                            </div>
                            <p>Out Product Inventory</p>
                        </div>
                    </a>
                </center>
            </div>
        @endif
        @if (is_allowed('inventory-in'))
            <div class="col-md-4">
                <center>
                    <a href="#addproduct" id="add_purchase" data-toggle="modal">
                        <div class="col-md-6 col-sm-6 box0">
                            <div class="box1">
                                <span class="fa fa-arrow-down"></span>
                                <h3>Inventory In</h3>
                            </div>
                            <p>Add Product Inventory</p>
                        </div>
                    </a>
                </center>
            </div>
        @endif
        @if (is_allowed('warehouse-transfer'))
            <div class="col-md-4">
                <center>
                    <a href="#addproduct" id="add_transfer" data-toggle="modal">
                        <div class="col-md-6 col-sm-6 box0">
                            <div class="box1">
                                <span class="fa fa-exchange"></span>
                                <h3>Warehouse Transfer</h3>
                            </div>
                            <p>Transfer Stock From One Warehouse to Another</p>
                        </div>
                    </a>
                </center>
            </div>
        @endif
    </div>
    <form action="/bulk_stock" method="POST">
        <div class="page-header clearfix">
            <h3>
                <i class="glyphicon glyphicon-align-justify"></i> Stocks Transactions
                <a href="/clearcache" class="btn btn-xs btn-default">Refresh Data</a>
            </h3>
            <div id="log"></div>
            <input type="hidden" value="delete" name="operation">
        </div>
    @endsection
    @section('content')
        <div class="content-panel">
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    <div class="table-responsive">
                        <table class="table table-condensed table-striped stocks_listing">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Date</th>
                                    @if (session()->get('settings.barcode.is_enable'))
                                        <th>Barcode</th>
                                    @endif
                                    <th>Product Name</th>
                                    @if (strpos(session()->get('settings.products.optional_items'), 'size') !== false)
                                        <th>Size</th>
                                    @endif
                                    @if (strpos(session()->get('settings.products.optional_items'), 'pattern') !== false)
                                        <th>Pattern</th>
                                    @endif
                                    @if (strpos(session()->get('settings.products.optional_items'), 'color') !== false)
                                        <th>Color</th>
                                    @endif
                                    <th>Brand</th>
                                    <th>Notes</th>
                                    <th>Batch</th>
                                    <th>Type</th>
                                    <th>Warehouse</th>
                                    <th>Related Supplier</th>
                                    <th>Related Customer</th>
                                    <th>Quantity</th>
                                    <th>Related Invoice</th>
                                    <th>Update At</th>
                                    <th>Added By</th>
                                    <th>Updated By</th>
                                    <th class="text-right">OPTIONS</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    </form>
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" id="addproduct" class="modal fade"
        data-backdrop="static">
        <div class="modal-dialog" style="width: 80%">
            <div class="modal-content col-md-12">
                <div class="col-md-12">
                    <div class="pull-right"><button type="button" class="close" data-dismiss="modal"
                            aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
                    <div class="content-panel">
                        <form id="product_add_form" action="{{ route('stock.store') }}">
                            <div class="multiRowInput">
                                <center>
                                    <h1 id="modal_title">Sale</h1>
                                </center>
                                <div id="log"></div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date:</label>
                                        <input name="date" class="form-control date-picker"
                                            style="background: rgba(33, 37, 47, 0.85);color: #FFEB3B" type="text">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="supplier">
                                        <label>Supplier:</label>
                                        <select name="supplier_id" class="form-control"
                                            style="background: rgba(33, 37, 47, 0.85);color: #FFEB3B" autofocus
                                            onchange="showpurchaseprice(this)">
                                            <option value="" disabled selected style="display: none;">No Supplier</option>
                                            @foreach ($supplier as $val)
                                                <option value="{{ $val->id }}">{{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" id="to_warehouse">
                                        <label>To Warehouse (Destination): <i class="fa fa-arrow-left"></i></label>
                                        <select name="to_warehouse" class="form-control"
                                            style="background: rgba(33, 37, 47, 0.85);color: #FFEB3B" autofocus>
                                            @foreach ($warehouse as $val)
                                                <option value="{{ $val->id }}">{{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="is_transfer" name="is_transfer" value="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label id="warehouse_label">Warehouse:</label>
                                        <select id="warehouse_d" name="warehouse_id" class="form-control"
                                            style="background: rgba(33, 37, 47, 0.85);color: #FFEB3B" autofocus>
                                            @foreach ($warehouse as $val)
                                                <option value="{{ $val->id }}">{{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th id="purchase_price">Purchase Price</th>
                                            <th class="numeric">Quantity</th>
                                            <th class="numeric">Notes</th>
                                            <th>Option</th>
                                        </tr>
                                    </thead>
                                    <tbody id="appendMe">
                                        <tr class="parent_row">
                                            <td>
                                                <select name="product_id[]" class="form-control product"
                                                    placeholder="Product Name" onkeydown="keyFocus(event, this)"></select>
                                            </td>
                                            <td>
                                                <input type="text" name="quantity[]" class="form-control qty0"
                                                    placeholder="Quantity">
                                            </td>
                                            <td>
                                                <input type="text" name="notes[]" class="form-control"
                                                    placeholder="notes" onkeydown="keyDown(event, this)">
                                            </td>
                                            <td><a style="cursor: pointer;" name="cln_btn"
                                                    onclick="clean(event, this)"><span
                                                        class="fa fa-trash"></span>Clean</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <input type="hidden" id="is_purchase" name="is_purchase" value="0">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
                    <center>
                        <button id="submit_btn" class="btn btn-primary btn-lg" onclick="addproducts()">Add Sale</button>
                    </center>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript" src="{{ asset('/assets/js/datatable/buttons.colVis.min.js') }}"></script>
    <!-- <script type="text/javascript" src="{{ url('products.json') }}?v={{ versioning('products') }}"></script> -->
    <script type="text/javascript" src="{{ asset('assets/js/choices.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('products.json') }}?v={{ versioning('products') }}"></script>
    <script type="text/javascript">
        var count = 0;
        var productselectbox;
        var allow_purchase_price = false;
        const is_allowed_pp = "{{ $is_allowed_pp }}";
        console.log(is_allowed_pp);

        function showAllPurchasePrice() {
            $("form#product_add_form").find('th:nth-child(2)').show();
            $("form#product_add_form").find('td:nth-child(2)').show();
            allow_purchase_price = true;
        }

        function hideAllPurchasePrice() {
            if ($("#is_purchase").val() != "1") {
                return;
            }
            $("form#product_add_form").find('th:nth-child(2)').hide();
            $("form#product_add_form").find('td:nth-child(2)').hide();
            allow_purchase_price = false;
        }

        function showpurchaseprice(select) {
            if (is_allowed_pp) {
                if ($(select).val() > 0 && ($("#is_purchase").val() == "1")) {
                    showAllPurchasePrice();
                } else {
                    hideAllPurchasePrice();
                }
            } else {
                hideAllPurchasePrice();
            }
        }

        function clean(e, ele) {
            var purchase_price = "";
            if ($("#is_purchase").val() == "1" && is_allowed_pp) {
                purchase_price = '<td><input type="text" class="form-control" id="price' + count +
                    '" placeholder="Purchase_Price" name="purchase_price[]" value="0"></td>';
            }
            var box = '<tr class="parent_row">\
              <td>\
                <select name="product_id[]" class="form-control product' + count + '" placeholder="Product Name"></select>\
                <br><small id="stock_info' + count + '"></small></td>' + purchase_price +
                '<td><input type="text" name="quantity[]" id="qty' + count +
                '" class="form-control" placeholder="Quantity" onkeydown="keyDown(event, this)"></td>\
              <td><a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td></tr>';
            var row = $(ele).closest('tr');
            var boss = $("tbody#appendMe").children();
            if (boss.length < 2) {
                var xo = $("#appendMe").append(box);
                xo.children().last().children().find("input").attr('onkeydown', 'keyDown(event, this)');
            }
            $(row).remove();
            choices('.product' + count);
            // debugger;
        }

        function keyDown(e, me) {
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9) {
                count += 1;
                e.preventDefault();
                var purchase_price = "";
                if ($("#is_purchase").val() == "1") {
                    purchase_price = '<td ' + ((allow_purchase_price) ? "" : "style='display:none'") +
                        '><input type="text" class="form-control" id="price' + count +
                        '" placeholder="Purchase_Price" name="purchase_price[]" value="0"></td>';
                }
                var box = '<tr class="parent_row"><td><select name="product_id[]" class="form-control product' + count +
                    '" placeholder="Product Name"></select><br><small id="stock_info' + count + '"></small></td>' +
                    purchase_price + '<td><input type="text" name="quantity[]" id="qty' + count +
                    '" class="form-control" placeholder="Quantity" onkeydown="keyDown(event, this)"></td><td><a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td></tr>';
                var xo = $("#appendMe").append(box);
                xo.children().last().children().find(".textbox").attr('onkeydown', 'keyDown(event, this)');
                // xo.children().last().children().first().find("select").focus();
                $(me).removeAttr('onkeydown');
                choices('.product' + count);
                // call custom function here
            }
        }

        function keyFocus(e, me) {
            var keyCode = e.keyCode || e.which;
            // console.log(keyCode);
            if (keyCode == 9) {
                // count += 1;
                e.preventDefault();
                $(me).closest('td').find('input').focus();
            }
        }

        function addproducts() {
            if ($("#is_transfer").val() > 0) {
                if ($("select[name=to_warehouse]").val() === $("select[name=warehouse_id]").val()) {
                    alertify.error("You cannot transfer to same warehouse");
                    return false;
                }
                alertify.warning("Please wait this may take longer than usual");
                // return false;
            }
            $("#product_add_form").submit();
        }
        $("#add_sale").click(function(e) {
            $("#submit_btn").html('Add Inventory Out');
            $("#is_purchase").val(0);
            $("#is_transfer").val(0);
            $("#modal_title").html("Inventory Out");
            $("#supplier").hide();
            $("#to_warehouse").hide();
            $("#purchase_price").hide();
            $("#warehouse_label").html("Warehouse:");
        });
        $("#add_purchase").click(function(e) {
            $("#submit_btn").html('Add Inventory In');
            $("#is_purchase").val(1);
            $("#is_transfer").val(0);
            $("#modal_title").html("Inventory In");
            $("#supplier").hide();
            $("#purchase_price").show();
            $("#to_warehouse").hide();
            $("#warehouse_label").html("Warehouse:");
            $("select[name=supplier_id]").val(0);
            hideAllPurchasePrice();
        });
        $("#add_transfer").click(function(e) {
            $("#submit_btn").html('Transfer');
            $("#is_purchase").val(0);
            $("#is_transfer").val(1);
            $("#modal_title").html("Inventory Transfer");
            $("#supplier").hide();
            $("#purchase_price").hide();
            $("#to_warehouse").show();
            $("#warehouse_label").html("From Warehouse (Origin):");
        });

        function choices(identifier) {
            var warehouse_id = $("#warehouse_d").val();
            $(identifier).select2({
                // ajax: {
                //   url: "/pagination_product_json/",
                //   dataType: 'json',
                //   delay: 250,
                //   data: function (params) {
                //     return {
                //       q: params.term, // search term
                //       page: params.page
                //     };
                //   },
                //   processResults: function (data, params) {
                //     // parse the results into the format expected by Select2
                //     // since we are using custom formatting functions we do not need to
                //     // alter the remote JSON data, except to indicate that infinite
                //     // scrolling can be used
                //     params.page = params.page || 1;

                //     return {
                //       results: data.items,
                //       pagination: {
                //         more: (params.page * 10) < data.total_count
                //       }
                //     };
                //   },
                //   // cache: true
                // },
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
                    if ($("#price" + count).size() > 0) {
                        $("#price" + count).focus();
                    } else {
                        $("#qty" + count).focus();
                    }
                }
                //some ajax call to update <br><small id="stock_info'+count+'"></small>
            });
        }

        function clearAll() {
            $("#appendMe").empty();
            var purchase_price = "";
            if ($("#is_purchase").val() == "1") {
                purchase_price =
                    '<td><input type="text" class="form-control" id="price0" placeholder="Purchase_Price" name="purchase_price[]" value="0"></td>';
            }
            $("#appendMe").html(
                '<tr class="parent_row"><td><select style="width:90% !important; padding:8px !important"  name="product_id[]" class="form-control product" placeholder="Product Name"></select><br><small id="stock_info"></small></td>' +
                purchase_price +
                '<td><input type="text" id="qty0" name="quantity[]" class="form-control" placeholder="Quantity"></td><td><input type="text" id="notes0" name="notes[]" class="form-control" placeholder="Notes" onkeydown="keyDown(event, this)"></td><td><a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td></tr>'
            );
            choices('.product');
        }

        $('.stocks_listing').dataTable({
            "ajax": "/stock_manage_listing_datatable",
            dom: 'Blfrtip',
            columnDefs: [{
                targets: [1, -2, -3, -4, -7, -8, -9, -10],
                visible: false
            }, ],
            buttons: ['colvis'],
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "processing": true,
            "serverSide": true,
            "order": [
                [1, 'desc']
            ],
            stateSave: true,
            "stateLoadParams": function (settings, data) {
                data.search.search = "";
                data.length = 10;
                data.start = 0;
                data.order = [[1, 'desc']];
            },
            "columns": [{
                    "data": "id_display",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": 'stocklog.id'
                },
                {
                    "data": "stocklog.date"
                },
                @if (session()->get('settings.barcode.is_enable'))
                    { "data": "product.barcode" },
                @endif {
                    "data": "product.name"
                },
                @if (strpos(session()->get('settings.products.optional_items'), 'size') !== false)
                    { "data": "product.size" },
                @endif
                @if (strpos(session()->get('settings.products.optional_items'), 'pattern') !== false)
                    { "data": "product.pattern" },
                @endif
                @if (strpos(session()->get('settings.products.optional_items'), 'color') !== false)
                    { "data": "product.color" },
                @endif {
                    "data": "product.brand"
                },
                {
                    "data": "notes"
                },
                {
                    "data": "stocklog.batch_id"
                },
                {
                    "data": "stocklog.type"
                },
                {
                    "data": "warehouse.name"
                },
                {
                    "data": "supplier.name"
                },
                {
                    "data": "customer.name"
                },
                {
                    "data": "stocklog.quantity"
                },
                {
                    "data": "stocklog.sale_id"
                },
                {
                    "data": "stocklog.updated_at"
                },
                {
                    "data": "added_by",
                    defaultContent: "-",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "updated_by",
                    defaultContent: "-",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "options",
                    "orderable": false,
                    "searchable": false
                }
            ]
        });

        $(document).ready(function() {
            var date = new Date();
            var day = date.getDate();
            var monthIndex = date.getMonth() + 1;
            var year = date.getFullYear();
            $('.date-picker').val(day + "-" + monthIndex + "-" + year); // = new Date();
            $('#addproduct').on('shown.bs.modal', function() {
                clearAll();
                hideAllPurchasePrice();
            })
            $.fn.dataTable.ext.errMode = 'none';
            @if ($request->search)
                $("input[type=search]").val("{{ $request->search }}").trigger("keyup");
            @endif
        });
    </script>
@endsection
