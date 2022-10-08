@php($invoice_fields_array = explode(",",session()->get('settings.products.invoice_fields')))
@foreach ($orders as $order)
    <tr class="tr_remove">
        {{--<td>
            <input name="details[order_id][]" type="checkbox" value="{{$order['orderid']}}"
                   @if($order['delivery_status'] == App\DeliveryChallan::DELIVERED)
                    disabled
                   @else
                   class="order"
                   @endif >
        </td>--}}
        <td class="description">
            
            @foreach ($invoice_fields_array as $columns )
            {{ $order[$columns]?:""." " }}
            @endforeach

            {{-- {{$order['pro_name']}} {{$order['pro_brand']}}
            @if ((strpos(session()->get('settings.products.invoice_fields'), 'pattern') !== false))
                {{$order['pro_pattern']}}
            @endif
            @if ((strpos(session()->get('settings.products.invoice_fields'), 'color') !== false))
                 {{$order['pro_color']}}
            @endif
            @if ((strpos(session()->get('settings.products.invoice_fields'),'size') !== false))
                  {{$order['pro_size']}}
            @endif --}}

        </td>
        <td>
            <input type="hidden" name="details[order_id][]" value="{{$order['orderid']}}">
            <input type="hidden" name="details[product_id][]" value="{{$order['product_id']}}">
            <input type="text" name="details[quantity][]" id="quantity_{{$order['product_id']}}" @if($is_quantity_fixed) readonly @endif
                   value="{{$order['quantity'] - getProductStockDeliveredInSaleOrder($order['product_id'], $sale_order_id)}}" maxlength="4" size="4"
           > / {{floatval($order['quantity'])}}
           <input type="hidden" name="remaining_quantity[]" value="{{$order['quantity'] - getProductStockDeliveredInSaleOrder($order['product_id'], $sale_order_id)}}">
           <input type="hidden" name="complete_quantity[]" value="{{floatval($order['quantity'])}}">
        </td>
        {{-- @if($order['delivery_status'] == App\DeliveryChallan::DELIVERED)
        <td>{{$order['delivery_status']}}</td>
        @else --}}
        <td>
            {!! Form::select('details[status][]', \App\DeliveryChallan::$deliveryStatuses, $order['delivery_status'], ['id' => 'status_'.$order['product_id']]) !!}
        </td>
        {{-- @endif --}}
        <td><input type="text" name="details[remarks][]" id="remark_{{$order['product_id']}}" value=""></td>
    </tr>
@endforeach