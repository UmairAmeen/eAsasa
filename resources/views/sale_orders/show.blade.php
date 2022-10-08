@extends('layout')
@section('header')
    <div class="page-header">
        <h1>Sale Order# {{ $sale_order->id }}</h1>
        <form action="{{ route('sale_orders.destroy', $sale_order->id) }}" method="POST" style="display: inline;"
            onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="btn-group pull-right" role="group" aria-label="...">
                <button type="submit" class="btn btn-danger">Delete <i class="glyphicon glyphicon-trash"></i></button>
                <a class="btn btn-default" href="{{ route('sale_orders.index') }}"><i
                        class="glyphicon glyphicon-backward"></i> Back</a>
            </div>
        </form>
    </div>
@endsection

@section('content')
    <div class="row showback">
        <div class="col-md-12 text-center">
            {{-- <div class="col-md-3">
                <h4>Sale Order#</h4>
                <h2>{{$sale_order->id}}</h2>
            </div> --}}
            <div class="col-md-2">
                <h4>Order No #</h4>
                <h2>{{ $sale_order->invoice_id }}</h2>
            </div>
            {{-- <div class="col-md-3">
                <h4>Bill #</h4>
                <h2>{{$sale_order->invoice->bill_number}}</h2>
            </div> --}}
            <div class="col-md-2">
                <h4>Customer</h4>
                <h2>{{ $sale_order->customer->name }}<br><small>{{ $sale_order->customer->city }}</small></h2>
                {{-- <h5>Balance: {{number_format(getCustomerBalance($sale_order->customer_id))}}</h5> --}}
            </div>
            <div class="col-md-2">
                <h4>Order Date</h4>
                <h2>{{ app_date_format($sale_order->date) }}</h2>
            </div>
            <div class="col-md-2 text-center">
                <h4>Sale Order Worth</h4>
                <h2>{{ number_format($worth, 2) }}</h2>
                {{-- <h5>Balance: {{number_format(getInvoiceBalance($sale_order->invoice_id))}}</h5> --}}
            </div>
            <div class="col-md-2 text-center">
                <h4>Shipping & Packaging</h4>
                <h2>{{ number_format($sale_order->invoice->shipping) }}</h2>
            </div>
            <div class="col-md-2 text-center">
                <h4>Balance</h4>
                <h2>{{ number_format(getInvoiceBalance($sale_order->invoice_id)) }}</h2>
            </div>

        </div>
        <br><br><br><br><br><br><br><br><br>
        <div class="col-md-12 text-center">
            <div class="col-md-3">
                <h4>POSTED</h4>
                <h2>{{ $sale_order->posted ? 'YES' : 'NO' }}</h2>
                @if (!$sale_order->posted)
                    <h6>
                        <form action="{{ url('confirmOrder') }}" method="POST">
                            <div id="log"></div>
                            <input type="hidden" name="id" value="{{ $sale_order->id }}">
                        @if(is_allowed('confirm-quotation'))
                            <button class="btn btn-link" type="submit">Confirm Order</button>
                        @endif
                        </form>
                    </h6>
                    <small>You cannot edit order once it's posted</small>
                @endif
            </div>


            {{-- <div class="col-md-3">
                <h4>Customer</h4>
                <h2>{{$sale_order->customer->name}}<br><small>{{$sale_order->customer->city}}</small></h2>
                {{-- <h5>Balance: {{number_format(getCustomerBalance($sale_order->customer_id))}}</h5> - -}}
            </div> --}}
            <div class="col-md-3">
                <h4>Sale Order Status</h4>
                <!-- <h2>{{ saleOrderStatus($sale_order->status) }}</h2> -->
                <form action="{{ url('update_saleorder_status') }}" method="POST">
                    <div id="log"></div>
                    <h2><select name="status" class="form-control">
                            <option value="0" {{ is_selected($sale_order->status, 0) }}>PENDING</option>
                            <option value="1" {{ is_selected($sale_order->status, 1) }}>ACTIVE</option>
                            <option value="3" {{ is_selected($sale_order->status, 3) }}>QUOTATION</option>
                            <option value="4" {{ is_selected($sale_order->status, 4) }}>COMPLETED</option>
                            @if(session()->get('settings.fbr.is_fbr_enable') == 1)
                            <option value="5" {{ is_selected($sale_order->status, 5) }}>FINISHED</option>
                            @endif
                        </select></h2>
                    <h6>
                        <input type="hidden" name="id" value="{{ $sale_order->id }}">
                        @if(is_allowed('update-status'))
                            <button class="btn btn-link" type="submit">Update Status</button>
                        @endif
                    </h6>
                </form>
            </div>
            <div class="col-md-3">
                <h4>Order Completion Date</h4>
                <form action="{{ url('update_saleorder_completion_date') }}" method="POST">
                    <div id="log"></div>
                    <h2>
                        <input type="date" @if($sale_order->status != 4) readonly @endif class="form-control" name="completion_date" id="completion_date"
                        value="{{ ($sale_order->status == 4) ? $sale_order->completion_date : 'Y-m-d' }}">
                    </h2>
                    <h6>
                        <input type="hidden" name="id" value="{{ $sale_order->id }}">
                        @if(is_allowed('update-status'))
                            <button class="btn btn-link" type="submit">Update Completion Date</button>
                        @endif
                    </h6>
                </form>
            </div>

            @if (env('DELIVERY_DATE') && $sale_order->delivery_date)
                <div class="col-md-3">
                    <h4>Delivery Date</h4>
                    <h2>{{ !empty($sale_order->delivery_date) ? app_date_format($sale_order->delivery_date) : 'N/A' }}
                    </h2>
                </div>
            @endif

            {{-- Environment variable set to true for RAZA TRACTOR client.
            This variable is only added to the .env file of relevant instance.
            For all other instances it will be null to implement generic code.
            note: "This change eliminates the need to add a particular variable
            in all instances to cater this change of a particular client". --}}
        </div>
        <br><br><br><br><br><br><br><br><br>
        <div class="col-md-12 text-center">
            @if (env('RAZA') && $sale_order->source)
            <div class="col-md-2">
                <label for="source">
                    <h4>Noted By:</h4>
                    <h2>{{ $sale_order->source }}</h2>
                </label>
            </div>
        @endif
            @if (env('SALES_PERSON'))
                <div class="col-md-2">
                    <label for="sales_person">
                        <h4><b>Sales Person:</b>&nbsp;&nbsp;{{ $sale_order->invoice->sales_person }}</h4>
                    </label>
                </div>
            @endif
            @if (env('RELATED_TO_SOURCE'))
                <div class="col-md-2">
                    <label for="related_to">
                        <h4><b>Related To:</b>&nbsp;&nbsp;{{ $sale_order->invoice->related_to }}</h4>
                    </label>
                </div>
                <div class="col-md-2 text-right">
                    <label for="source">
                        <h4><b>Source:</b>&nbsp;&nbsp;{{ $sale_order->source }}</h4>
                    </label>
                </div>
            @endif
        </div>
    </div>

    <div class="col-md-12">
        <div class="col-md-6">
            <a href="{{ route('sale_orders.edit', $sale_order->id) }}" class="btn btn-default">Edit Order</a>
            <a href="#" onclick="popitup('{{ url('smallInvoice') }}/{{ $sale_order->invoice_id }}','invoicePrint')"
                class="btn btn-primary">Print Minor Invoice</a>
            <a href="#" onclick="popitup('{{ url('invoices') }}/{{ $sale_order->invoice_id }}','invoicePrint')"
                class="btn btn-primary">Print Invoice</a>

            <a href="#"
                onclick="popitup('{{ url('showStock') }}?sale_order_id={{ $sale_order->id }}&customer_id={{ $sale_order->customer_id }}','invoicePay')"
                class="btn btn-success">Add Stock Delivery</a>

            <a href="#"
                onclick="popitup('{{ url('invoice_pay') }}?invoice_id={{ $sale_order->invoice_id }}&customer_id={{ $sale_order->customer_id }}','invoicePay')"
                class="btn btn-warning">Add Transaction</a>
            <a class="btn btn-success" target="_blank" href="{{ url('purchaseInvoice/'.$sale_order->invoice_id.'/1')}}"><i
                    class="glyphicon glyphicon-eye-open"></i> Print Purchase Order</a>
        </div>
        <div class="col-md-6">
            <h5>Order Details</h5>
            <table class="table showback">
                <thead>
                    @if (session()->get('settings.barcode.is_enable'))
                        <th>Product Barcode</th>
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
                    <th>Quantity</th>
                    <th>Sale Price</th>
                    @if (env('QTY_TO_DELIVERED'))
                        <th>Quantity Left to be delivered</th>
                    @else
                        <th>Total</th>
                    @endif
                </thead>
                <tbody>

                    @foreach ($sale_order->invoice->orders as $value)
                        <tr>
                            @if (session()->get('settings.barcode.is_enable'))
                                <td>{{ $value->product->barcode }}</td>
                            @endif
                            <td>{{ $value->product->name }}</td>
                            @if (strpos(session()->get('settings.products.optional_items'), 'size') !== false)
                                <td>{{ $value->product->size }}</td>
                            @endif
                            @if (strpos(session()->get('settings.products.optional_items'), 'pattern') !== false)
                                <td>{{ $value->product->pattern }}</td>
                            @endif
                            @if (strpos(session()->get('settings.products.optional_items'), 'color') !== false)
                                <td>{{ $value->product->color }}</td>
                            @endif

                            {{-- @if (!empty(session()->get('settings.products.optional_items')))
                    @php($fields = explode(",", session()->get('settings.products.optional_items')))
                    @foreach ($fields as $field) 
                            <td>{{(($field == 'category')?$order->product->$field->name:$order->product->$field);}}</td>
                    @endforeach
                @endif --}}
                            @if ($value->product->unit_id == 3)
                                <td>{{ $value->quantity }}</td>
                            @else
                                <td>{{ floatval($value->quantity) }}</td>
                            @endif
                            <td>{{ $value->salePrice }}</td>
                            @if (env('QTY_TO_DELIVERED'))
                                <td>{{ $value->quantity - getProductStockDeliveredInSaleOrder($value->product_id, $sale_order->id) }}
                                </td>
                            @else
                                <td>{{ $value->salePrice * floatval($value->quantity) }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h5>Transaction Details</h5>
            <table class="table showback">
                <thead>
                    <th>Transaction #</th>
                    <th>Date</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Options</th>
                </thead>
                <tbody>

                    @foreach ($sale_order->invoice->transactions as $value)
                        <tr>
                            <td>{{ $value->id }}</td>
                            <td>{{ app_date_format($value->date) }}</td>
                            <td>{{ $value->type == 'out' ? 'credit' : 'debit' }}</td>
                            {{-- <td>{{$value->payment_type}}({{($value->type == "out")?"credit":"debit"}})</td> --}}
                            <td>{{ $value->amount }}</td>
                            <td>
                                <ul class="list-inline">
                                    <li><a href="{{ route('transactions.show', $value->id) }}" class="btn btn-success"
                                            target="blank"><i class="glyphicon glyphicon-print"></i> Print</a></li>
                                    <li>
                                        @if ($value->type != 'out')
                                            <form action="{{ route('transactions.destroy', $value->id) }}" method="POST"
                                                onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false }">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="custom_redirect"
                                                    value="{{ $sale_order->id }}">
                                                <input type="hidden" name="_token" value="'.csrf_token() .'">
                                                <button type="submit" class="btn btn-danger"><i
                                                        class="glyphicon glyphicon-trash"></i> Delete</button>
                                            </form>
                                        @endif
                                    </li>
                                </ul>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <h5>Delivery Details</h5>
            <table class="table showback">
                <thead>
                    <th>Date</th>
                    <th>Product Name</th>
                    <th>Warehouse</th>
                    <th>Quantity</th>
                </thead>
                <tbody>

                    @foreach ($sale_order->stock as $value)
                        <tr>
                            <td>{{ app_date_format($value->date) }}</td>
                            <td>{{ $value->product->name }}</td>
                            <td>{{ $value->warehouse->name }}</td>
                            <td>{{ $value->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script type="text/javascript">
        function popitup(url, windowName) {
            newwindow = window.open(url, windowName, 'height=800,width=1050');
            if (window.focus) {
                newwindow.focus()
            }
            return false;
        }
    </script>

@endsection
