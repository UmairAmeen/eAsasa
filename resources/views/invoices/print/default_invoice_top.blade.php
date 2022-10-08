<style>
    .header-height {
        line-height: 20px;
    }
    .client_font_size{
        font-size: {!! session()->get('settings.misc.client_font_size') !!};
    }

</style>
<br>
@if ($is_purchase == 1)
<h3>Purchase Order</h3>
@endif
<table border="0" width="100%" cellpadding="1" cellspacing="1">
    <tr class="header-height">
        <td width="14%"><p class="client_font_size">Client Name:</p></td>
        <td width="25%"><u class="client_font_size">{{ $user->name }}</u></td>
        @if ($is_purchase != 1)
            <td width="10%"><p class="client_font_size">Mobile:</p></td>
            <td width="26%"><u class="client_font_size">{{ $user->phone }}</u></td>
        @else
            <td width="10%"></td>
            <td width="26%"><u></u></td>
        @endif
        <td width="13%"><p class="client_font_size">Order No:</P></td>
        <td width="17%"><u class="client_font_size">{{ str_pad($id, 6, '0', STR_PAD_LEFT) }}</u></td>
    </tr>
    <tr class="header-height">
        @if ($is_purchase != 1)
            <td><p class="client_font_size">Address:</P></td>
            <td colspan="3" rowspan="2"><u class="client_font_size">{{ $user->address }}</u></td>
        @else
            <td></td>
            <td colspan="3" rowspan="2"></td>
        @endif
        <td><p class="client_font_size">Booking Date:</p></td>
        <td><u class="client_font_size">{{ date(session()->get('settings.misc.date_format'), strtotime($invoice->date)) }}</u></td>
    </tr>
    @if ($invoice->sale_order->delivery_date)
        <tr class="header-height">
            <td></td>
            <td><p class="client_font_size">Delivery Date:</p></td>
            <td><u class="client_font_size">{{ date(session()->get('settings.misc.date_format'), strtotime($invoice->sale_order->delivery_date)) }}</u>
            </td>
        </tr>
    @endif
    {{-- Environment variable set to true for RAZA TRACTOR client.
    This variable is only added to the .env file of relevant instance.
    For all other instances it will be null to implement generic code.
    note: "This change eliminates the need to add a particular variable
    in all instances to cater this change of a particular client". --}}

    @if($invoice->bill_number && env('RAZA'))
        <tr class="header-height">
            <td colspan="4"></td>
            <td><p class="client_font_size">Bill Number:</p></td>
            <td><u class="client_font_size">{{ $invoice->bill_number }}</u>
            </td>
        </tr>
    @endif
    <tr>
        @if ($is_purchase != 1)
            @if (env('RELATED_TO_SOURCE'))
                <td width="14%"><p class="client_font_size">Source:</p></td>
                <td width="25%"><u class="client_font_size">{{ $invoice->sale_order->source }}</u></td>
                <td width="10%"><p class="client_font_size">Related To:<p></td>
                <td width="18%"><u class="client_font_size">{{ $invoice->related_to }}</u></td>
            @endif
            {{-- Environment variable set to true for RAZA TRACTOR client.
            This variable is only added to the .env file of relevant instance.
            For all other instances it will be null to implement generic code.
            note: "This change eliminates the need to add a particular variable
            in all instances to cater this change of a particular client". --}}
            @if(env('RAZA'))
            <td width="14%"><p class="client_font_size">Noted By:</p></td>
            <td width="25%"><u class="client_font_size">{{ $invoice->sale_order->source }}</u></td>
            @endif
        {{-- @else --}}
            {{-- <td width="14%"></td>
            <td width="25%"></td>
            <td width="10%"></td>
            <td width="13%"></td> --}}
        @endif
        @if (env('SALES_PERSON') || $invoice->sale_order->saleOrder_person)
            <td width="14%"><p class="client_font_size">Sales Person:</p></td>
            <td width="13%"><u class="client_font_size">{{ $invoice->sales_person?:$invoice->sale_order->saleOrder_person->name }}</u></td>
        @endif
    </tr>
    <?php
    $decoded = json_decode($invoice->custom_inputs);
    ?>
    @if (count($decoded) > 0)
        @foreach ($decoded as $key => $value)
            @if (!empty($value))
                <tr>
                    <td colspan="4">{{ ucwords(ltrim($key)) }}: <u>{{ ucwords($value) }}</u></td>
                </tr>
            @endif
        @endforeach
    @endif
    {{-- <tr class="header-height">
        <td colspan="4"></td>
        <td><p class="client_font_size">Delivery Date:</p></td>
        <td><u class="client_font_size">{{ !empty($invoice->sale_order->delivery_date) ? date(session()->get('settings.misc.date_format'), strtotime($invoice->sale_order->delivery_date)) : '_____________' }}</u></td>
    </tr>
    <tr class="header-height">
        <td>Source: </td>
        <td colspan="3"><u>{{ $invoice->sale_order->source }}</u></td>
        <td><p class="client_font_size">Sales Person:</p></td>
        <td><u class="client_font_size">{{ $invoice->sales_person }}</u></td>
    </tr> --}}
    {{-- <tr>
        <td colspan="4"></td>
        <td><p class="client_font_size">Related To:</p></td>
        <td><u class="client_font_size">{{ $invoice->related_to }}</u></td>
    </tr> --}}
</table>
