@extends('layout')
@section('css')
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/searchbuilder/1.0.0/css/searchBuilder.dataTables.min.css">
@endsection
@section('header')
    @if (is_allowed('transaction-create'))
        <div class="row">
            <div class="col-md-4">
                <center>
                    <a id="transaction_out" onclick="showModal('out');return false;">
                        <div class="col-md-12 col-sm-12 box0">
                            <div class="box1">
                                <span class="fa fa-arrow-up"></span>
                                <h3>Cash Out</h3>
                                <p>Cash Pay to Owner</p>
                            </div>
                        </div>
                    </a>
                </center>
            </div>
            <div class="col-md-4">
                <center>
                    <a id="transaction_in" onclick="showModal('in');return false;">
                        <div class="col-md-12 col-sm-12 box0">
                            <div class="box1">
                                <span class="fa fa-arrow-down"></span>
                                <h3>Cash In</h3>
                                <p>Cash Borrow from Owner</p>
                            </div>
                        </div>
                    </a>
                </center>
            </div>
            <div class="col-md-4">
                <center>
                    <a href="{{url('reports/cash_in_hand')}}">
                        <div class="col-md-12 col-sm-12 box0">
                            <div class="box1">
                                <span class="fa fa-arrow-left"></span>
                                <h3>Back to</h3>
                                <p>Cash In Hand Report</p>
                            </div>
                        </div>
                    </a>
                </center>
            </div>
            <br>
            <br>
            @if (session()->get('settings.accounts.transaction_search_filter'))
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="from"><strong>Starting Date</strong></label>
                            <input value="{{ date('Y-m-d', strtotime('-6 months')) }}" type="date" class="form-control"
                                name="search_from" id="search_from" style="width: 300px;">
                        </div>
                        <div class="col-md-3">
                            <label for="to"><strong>Ending Date</strong></label>
                            <input value="{{ date('Y-m-d') }}" type="date" class="form-control" name="search_to"
                                id="search_to" style="width: 300px;">
                        </div>
                        <div class="col-md-3">
                            <label for=""></label>
                            <button class="btn btn-success form-control" onclick="search_transaction()"
                                style="width: 200px;height:60px">Search</button>
                        </div>
                    </div>
                </div>
            @endif
            <br><br>
        </div>
    @endif

    {{-- <div class="form-group col-md-4">
  <label>Search Transaction by Date</label>
  <input type="date" class="form-control" name="sort_by_date" placeholder="View Transaction of selected date" id="date_sort">
</div> --}}
@endsection
@section('content')
    <div class="col-md-12">
        <div class="row content-panel">
            <div class="table-responsive">
                <table class="table table-condensed table-striped admin_transaction_listing">
                    <thead>
                        
                        <tr>
                            <th>ID</th>
                            <th>TYPE</th>
                            <th>DATE</th>  
                            <th>PAYMENT TYPE</th>
                            <th>AMOUNT</th>
                            <th>DESCRIPTION</th>
                            <th>TRANSACTION ID</th>
                            <th>BANK</th>
                            <th>Release Date</th>
                            <th>Added By</th>
                            <th>Updated By</th>
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" id="addTransaction" class="modal fade"
        data-backdrop="static">
        <div class="modal-dialog" style="width: 80%">
            <div class="modal-content col-md-12">
                <div class="col-md-12">
                    <div class="pull-right"><button type="button" class="close" data-dismiss="modal"
                            aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
                    <div class="content-panel">
                        <form id="admin_transaction_form" action="{{ route('admin_transactions.store') }}">
                            <input type="hidden" name="type" value="">
                            <div class="multiRowInput">
                                <center>
                                    <h1 id="modal_title">Sale</h1>
                                </center>
                                <div id="log"></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date:</label>
                                        <input name="date" class="form-control date-picker"
                                            style="background: rgba(33, 37, 47, 0.85);color: #FFEB3B" type="text">
                                    </div>
                                </div>
                                
                                <table id="aaks" class="table table-bordered table-striped table-hover">
                                    <thead>
                                    </thead>
                                    <tbody id="appendMe"></tbody>
                                </table>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </div>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
                    Total Enteries: <span id="item_count">0</span><br>
                    Total Amount: <span id="item_amount">0</span>
                    <center>
                        <input type="submit" id="submit_btn" class="btn btn-primary btn-lg" value="Add Transaction" />
                    </center>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <table id="transaction-template" style="display: none;">
        <tr class="parent_row">
            <td>
                {!! Form::select('payment_type[]', App\Transaction::$types, null, ['class' => 'form-control']) !!}
            </td>
            <td colspan="2">
                <input type="text" name="amount[]" class="form-control" placeholder="Amount"
                    onkeyup="updateCountandTotal()">
            </td>
            <td>
                {!! Form::select('bank[]', App\BankAccount::GetBankDropDown(), null, ['class' => 'form-control']) !!}
            </td>
            <td>
                <input type="date" name="release_date[]" class="form-control" placeholder="Cheque Release Date (if any)">
            </td>
            <td>
                <input type="text" name="transacion_id[]" class="form-control qty0" placeholder="Transaction ID (if any)" max="100">
            </td>
            <td>
                <input type="text" name="description[]" class="form-control qty0" placeholder="Description"
                    onkeydown="keyDown(event, this)">
            </td>
            <td>
                <a style="cursor: pointer;" name="cln_btn" onclick="clean(event, this)"><span
                        class="fa fa-trash"></span>Clean</a>
            </td>
        </tr>
    </table>
@endsection
@section('scripts')
<script src="{{ asset('assets/js/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/moment/locale.min.js') }}"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js" type="text/javascript">
    </script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.0.0/js/dataTables.searchBuilder.min.js">
    </script>
    <script type="text/javascript">
        var count = 0;
        const js_date_format = "{{ $date_format }}";
        var productselectbox;

        var cheque_head = ' <tr>\
                                  <th>Bank</th>\
                                  <th colspan="2">Amount</th>\
                                  <th>Transaction Id</th>\
                                  <th style="display: none;">Payment Type</th>\
                                  <!-- <th>Release Date</th>\
                                  <th>Option</th>\
                                </tr>';
        var cheque_body = '<tr class="parent_row">' +
            '<td><input type="text" name="bank[]" class="form-control" placeholder="Bank (if any)"></td>' +
            '<td colspan="2"><input type="text" name="amount[]" class="form-control" placeholder="Amount" onkeyup="updateCountandTotal()"></td>' +
            '<td><input type="text" name="transacion_id[]" class="form-control qty0" placeholder="Transaction ID (if any)"></td>' +
            '<td>' +
            '<input type="date" name="release_date[]" class="form-control" placeholder="Cheque Release Date (if any)"></select>' +
            '</td>' +
            '<td style="display: none;">' +
            '<select name="payment_type[]" class="form-control">' +
            // '<option value="1">-</option>'+
            '<option value="cash">Cash</option>' +
            '<option value="cheque">Cheque</option>' +
            '<option value="transfer">Online Transfer</option>' +
            '</select>' +
            '</td>' +
            '<td><a style="cursor: pointer;" name="cln_btn" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td>' +
            '</tr>';
        var normal_head = ' <tr>\
                                  <th>Payment Type</th>\
                                  <th colspan="2">Amount</th>\
                                  <th>Bank</th>\
                                  <th>Release Date</th>\
                                  <th>Transaction Id</th>\
                                  <th>Description</th>\
                                  <th>Option</th>\
                                </tr>';
        var normal_body = $('#transaction-template').html().replace(/<\/?tbody>/g, '');
        var current_head = "";
        var current_body = "";

        function showModal(type) {
            $('#admin_transaction_form input[name=type]').val(type);
            $('#addTransaction').modal('show');
        }

        function updateCountandTotal() {
            var sum = 0;
            $(".parent_row input[name^=amount]").each(function(index, data) {
                if (parseFloat($(data).val())) {
                    sum += parseFloat($(data).val());
                }
            });
            $("#item_count").html($(".parent_row").length);
            $("#item_amount").html(sum.toFixed(2));
        }

        function clean(e, ele) {
            var box = current_body;
            var row = $(ele).closest('tr');
            var boss = $("tbody#appendMe").children();
            if (boss.length < 2) {
                var xo = $("#appendMe").append(box);
                xo.children().last().children().find("input").attr('onkeydown', 'keyDown(event, this)');
            }
            $(row).remove();
            if (hide_payment_type) {
                $('#aaks tr > *:nth-child(3)').hide();
                $('#aaks tr > *:nth-child(5)').hide();
                $('#aaks tr > *:nth-child(6)').hide();
                // $('#aaks tr > *:nth-child(6)').hide();        
            }
            if (select_cheque) {
                $("select[name^=payment_type]").val("cheque");
            }
            updateCountandTotal();
        }

        function keyDown(e, me) {
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9) {
                count += 1;
                e.preventDefault();
                var box = current_body;
                var xo = $("#appendMe").append(box);
                xo.children().last().children().find(".textbox").attr('onkeydown', 'keyDown(event, this)');
                // xo.children().last().children().first().find("select").focus();
                $(me).removeAttr('onkeydown');
                if (hide_payment_type) {
                    $('#aaks tr > *:nth-child(3)').hide();
                    $('#aaks tr > *:nth-child(5)').hide();
                    $('#aaks tr > *:nth-child(6)').hide();
                    // $('#aaks tr > *:nth-child(6)').hide();
                }
                if (select_cheque) {
                    $("select[name^=payment_type]").val("cheque");
                }
                // call custom function here
                updateCountandTotal();
            }
        }

        function keyFocus(e, me) {
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9) {
                // count += 1;
                e.preventDefault();
                $(me).closest('td').find('input').focus();
            }
        }

        function addproducts() {
            // $("#product_add_form").submit();
        }

        var hide_payment_type = select_cheque = false;

        $("#transaction_in").click(function(e) {
            $("#submit_btn").val('Add');
            $("#modal_title").html("Cash In");
            hide_payment_type = false;
            select_cheque = false;
            current_head = normal_head;
            current_body = normal_body;
            $("#aaks thead").html(current_head);
            $("#aaks tbody").html(current_body);
            $('#aaks tr > *:nth-child(1)').show();
            $('#aaks tr > *:nth-child(2)').show();
            $('#aaks tr > *:nth-child(4)').show();
            $('#aaks tr > *:nth-child(5)').show();
            $('#aaks tr > *:nth-child(6)').show();
            updateCountandTotal();
            // $("#supplier").hide();
        });
        $("#transaction_out").click(function(e) {
            $("#submit_btn").val('Add');
            $("#modal_title").html("Cash Out");
            hide_payment_type = false;
            select_cheque = false;
            current_head = normal_head;
            current_body = normal_body;
            $("#aaks thead").html(current_head);
            $("#aaks tbody").html(current_body);
            $('#aaks tr > *:nth-child(2)').show();
            updateCountandTotal();
            // $('#aaks tr > *:nth-child(6)').hide();
            // $("#supplier").show();
        });

        function reorder_cheque_col() {
            var tbl = jQuery('#aaks');
            $.each($("th[data-cheque]"), function(index, ele) {
                jQuery.moveColumn(tbl, $(ele).data("cheque") - 1);
            });
        }

        function reset_col() {
            var tbl = jQuery('#aaks');
            $.each($("th[data-order]"), function(index, ele) {
                jQuery.moveColumn(tbl, $(ele).data("order") - 1);
            });
        }

        function clearAll() {
            $("#appendMe").empty();
            var box = current_body;
            $("#appendMe").html(box);
            if (hide_payment_type) {
                $('#aaks tr > *:nth-child(3)').hide();
                $('#aaks tr > *:nth-child(5)').hide();
                $('#aaks tr > *:nth-child(6)').hide();
                // $('#aaks tr > *:nth-child(6)').hide();      
            }
            if (select_cheque) {
                $("select[name^=payment_type]").val("cheque");
            }
        }

        $('[role=dialog]').on('shown.bs.modal', function() {
            $("form").find("#log").removeClass();
            $("form").find("#log").html("");
            clearAll();
        });

        var table = false;
        $(document).ready(function() {


            $.fn.dataTable.moment(js_date_format);
            table = $('.admin_transaction_listing').DataTable({
                "processing": true,
                "serverSide": false,
                "searching": true,
                "ajax": {
                    url: "/admin_transaction_listing_datatable",
                    type: "GET",
                    data: function(d) {
                        d.searchFrom = $('#search_from').val();
                        d.searchTo = $('#search_to').val();
                    }
                },
                responsive: true,
                // "dom": 'Qti<"bottom"lrp>',
                "dom": 'QBlfrtip',
                "order": [
                    [0, 'desc']
                ],
                "columns": [{
                        "data": "id",
                        "orderable": true,
                        "searchable": true
                    },
                    {
                        "data": 'type',
                        "orderable": true,
                        "searchable": true
                    },
                    {
                        "data": "date"
                    },
                    {
                        "data": "payment_type"
                    },
                    {
                        "data": "amount"
                    },
                    {
                        "data": "description"
                    },
                    {
                        "data": "transaction_id"
                    },
                    {
                        "data": "bank"
                    },
                    {
                        "data": "release_date"
                    },
                    {
                        "data": "added_by",
                        defaultContent: "-",
                        "orderable": false,
                        "searchable": true
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
                ],
                "fnDrawCallback": function(oSettings) {
                    init();
                }
            });

            $('#date_sort', this).on('keyup change', function() {
                if (table.column(2).search() !== this.value) {
                    table.column(2).search(this.value).draw();
                }
            });

            function del_init() {
                $(".delete_the_transaction").click(function(e) {
                    e.preventDefault();
                    var ele = $(this);
                    alertify.confirm('Are you sure, you want to delete this product ?',
                        'Deleting this product will delete all related transactions, refunds, sale, purchase & inventory In/Out',
                        function() {
                            return ele.parent('form').submit();
                        },
                        function() {
                            return true;
                        });
                });
            }

            var date = new Date();
            var day = date.getDate();
            var monthIndex = date.getMonth() + 1;
            var year = date.getFullYear();
            $('.date-picker').val(day + "-" + monthIndex + "-" + year); // = new Date();
            $(".datatable").show();
            jQuery.moveColumn = function(table, from, to) {
                var rows = jQuery('tr', table);
                var cols;
                rows.each(function() {
                    // debugger;
                    cols = jQuery(this).children('th, td');
                    cols.eq(from).detach().insertBefore(cols.eq(to));
                });
            };
        });

        function search_transaction() {
            table.ajax.reload(null, false);
        }
    </script>
@endsection
