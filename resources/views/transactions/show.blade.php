<style>
    .td-center { text-align: center; }
    .tr-height { line-height: 28px; }
    .td-border { border-bottom: 1px solid black; }
    .receipt-font { font-size: 1.3em; /*text-align: justify; text-justify: inter-word;*/ }
</style>
<table width="100%" border="0">
    <tr>
        <td width="15%">Order Status: </td>
        <td width="20%">{!! saleOrderStatusHtml($transaction->invoice->sale_order->status) !!}</td>
        <td width="35%" colspan="2"></td>
        <td>Date:</td>
        <td>{{ date(session()->get('settings.misc.date_format'), strtotime($transaction->date)) }}</td>
    </tr>
    <tr>
        <td width="15%">Order   Source: </td>
        <td width="20%">{{ $transaction->invoice->sale_order->source }}</td>
        <td width="35%" colspan="2"></td>
        <td>Sr No:</td>
        <td>{{str_pad($transaction->id, 6, '0', STR_PAD_LEFT)}}</td>
    </tr>
    <tr>
        <td colspan="4"></td>
        <td>Order No:</td>
        <td>{{str_pad($transaction->invoice_id, 6, '0', STR_PAD_LEFT)}}</td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td colspan="6">
            @php($f = new NumberFormatter('en', NumberFormatter::SPELLOUT))
            {{-- @php($balance = $transaction->customer ? getCustomerBalance($transaction->invoice_id) : ($transaction->supplier ? getSupplierBalance($transaction->supplier_id) : 0)) --}}
            @php($balance = $transaction->customer ? getInvoiceBalance($transaction->invoice_id) : ($transaction->supplier ? getSupplierBalance($transaction->supplier_id) : 0))
            <table border="0" width="100%" cellpadding="0" cellspacing="0" class="receipt-font">
                <tr class="tr-height">
                    <td>
                        <table border="0" width="96%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="40%">Received with thanks from </td>
                                <td width="60%" class="td-border"><b>{{ $transaction->customer->name }}</b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="tr-height">
                    <td>
                        <table border="0" width="96%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="18%">Payment Type</td>
                                <td width="20%" class="td-border">{{ ucfirst($transaction->payment_type) }}</td>
                                <td width="12%" class="td-center">Bank</td>
                                <td class="td-border" colspan="3" width="50%"> {{ $transaction->bank_detail->name . ' - ' . $transaction->bank_detail->branch }}</td>
                            </tr>
                        </table>
                    </td>
                    {{--
                    <td>Transaction ID</td> 
                    <td width="15%" class="td-border" >{{ $transaction->transaction_id }}</td>
                    --}}
                </tr>
                @if($transaction->description)
                <tr class="tr-height">
                    <td>
                        <table border="0" width="96%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="18%">Description</td>
                                <td width="82%" class="td-border">{{ ucfirst($transaction->description) }}</td>
                            </tr>
                        </table>
                    </td>
                    {{--
                    <td>Transaction ID</td> 
                    <td width="15%" class="td-border" >{{ $transaction->transaction_id }}</td>
                    --}}
                </tr>
                @endif
                <tr class="tr-height">
                    <td>
                        <table border="0" width="96%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="10%">Amount</td>
                                <td width="16%" class="td-border">{{ number_format($transaction->amount) }}{{ $transaction->amount > 0 ? " /=" : "" }}</td>
                                <td width="23%" class="td-center">Amount in words </td>
                                <td width="51%" class="td-border">{{ucfirst($f->format($transaction->amount))}}{{ $transaction->amount > 0 ? " only" : "" }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="tr-height">
                    <table border="0" width="96%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="10%">Balance</td>
                            <td width="16%" class="td-border">{{ formating_price($balance) }}{{ $balance > 0 ? " /=" : "" }}</td>
                            <td width="23%" class="td-center">Balance in words </td>
                            <td width="51%" class="td-border">{{ucfirst($f->format($balance))}}{{ $balance > 0 ? " only" : "" }}</td>
                        </tr>
                    </table>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
</table>
