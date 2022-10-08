<style>
    .total_font_size{
        font-size: {!! session()->get('settings.misc.total_font_size') !!};
    }
    td{
        text-align: right;
    }
</style>
@php  
    $enable_amount_in_words = session()->get('settings.sales.show_amount_in_words')?:0;
    $custom_width = "90%";
    //if Amount In Words is enabled
    if($enable_amount_in_words == 1)
    {
        $custom_width = "40%";
    }
@endphp
<table class="table" border="1" cellpadding="6" width="97%">
    {{-- Environment variable set to true for RAZA TRACTOR client.
    This variable is only added to the .env file of relevant instance.
    For all other instances it will be null to implement generic code.
    note: "This change eliminates the need to add a particular variable
    in all instances to cater this change of a particular client". --}}
    @if(!env('RAZA'))
    <tr>
        <td width="{{ $custom_width }}">{{ $summary['totalQuantity'] }}</td>
        <td width="10%"><strong>کل تعداد</strong></td>
        @if($enable_amount_in_words == 1)
        <td width="{{ $custom_width }}">{{ ucwords($summary['amountInWords']) }}</td>
        <td width="10%"><strong>رقم لفظوں میں</strong></td>
        @endif
    </tr>
    <tr>
        <td colspan="4" width="90%">{{ $invoice->description }}</td>
        <td width="10%"><strong>نوٹ:</strong></td>
    </tr>
    @endif
    @if ($is_purchase != 1)
        <tr>

            {{-- Environment variable set to true for RAZA TRACTOR client.
            This variable is only added to the .env file of relevant instance.
            For all other instances it will be null to implement generic code.
            note: "This change eliminates the need to add a particular variable
            in all instances to cater this change of a particular client". --}}
            <td @if(!env('RAZA')) colspan="3" width="40%" @else width="100%" @endif >
                <table id="invoiceTotal" border="0">
                    @foreach ($summary_detail as $label => $value)
                        @if($value)
                        <tr class="header-height">
                            <td align="left"><strong class="total_font_size">{{ number_format($value) }}</strong></td> 
                            <td><strong class="total_font_size">{{ $label }}</strong></td>
                        </tr>
                        @endif
                    @endforeach
                </table>
            </td>
            {{-- Environment variable set to true for RAZA TRACTOR client.
            This variable is only added to the .env file of relevant instance.
            For all other instances it will be null to implement generic code.
            note: "This change eliminates the need to add a particular variable
            in all instances to cater this change of a particular client". --}}
            @if(!env('RAZA'))
                <td colspan="2" width="60%">
                    <table border="0">
                        <tr class="header-height">
                            <?php 
                                $lang_arr = ['cash' => 'کیش', 'cheque' => 'چیک', 'transfer' => 'ٹرانسفر']
                                ?>
                            <td>
                                <strong>{{ $lang_arr[$transaction->payment_type] }}</strong>ادائیگی کی قسم:
                            </td>
                        </tr>
                        @php
                            $bank_details = null;
                            if (!empty($transaction->bank_detail)) {
                                $bank_details = $transaction->bank_detail->name . ' - ' . $transaction->bank_detail->branch;
                            } else {
                                $bank_details = !empty($invoice->bank) ? $invoice->bank->name : null;
                            }
                        @endphp
                        @if ($bank_details)
                        <tr class="header-height">
                            <td>بینک کی تفصیلات :
                                <strong>{{ $bank_details }}</strong>
                            </td>
                        </tr>
                    @endif
                        <tr class="header-height">
                            <td>ٹرانزیکشن ID:
                                <strong>{{ !empty($transaction->transaction_id) ? $transaction->transaction_id : '' }}</strong>
                            </td>
                        </tr>
                    </table>
                </td>
            @endif
        </tr>
    @endif
</table>
@php
$terms_on_back = session()->get('settings.sales.terms_on_back')?:0;
@endphp
@if ($is_purchase != 1 && $terms_on_back == 0)
    {!! session()->get('settings.sales.invoice_terms') !!}
@endif