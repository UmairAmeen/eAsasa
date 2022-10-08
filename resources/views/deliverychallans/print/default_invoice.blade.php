<?php
    //if image enabled
    if($image_enable == 1){$custom_width = "28%";}
    //if image disabled    
    else{$custom_width = "48%";} 
?>
<table border="1" cellpadding="6" width="100%">
    <tr>
        <td width="10%" height="30px;"><b>Sr No</b></td>
        @if($image_enable == 1)
                <td width="20%"><b>Image</b></td>
        @endif
        <td width="{{ $custom_width }}"><b>Description</b></td>
        <td width="12%"><b>Qty</b></td>
        <td width="12%"><b>Status</b></td>
        <td width="18%"><b>Remarks</b></td>
    </tr>
    @php($invoice_fields_array = explode(",",session()->get('settings.products.invoice_fields')))
    @foreach ($orders as $order)
        <tr>
            <td height="24px;">{{ $serial_no + 1 }}</td>
            @if($image_enable == 1)
                <td width="20%" height="60">
                    @if($order->product->image_path)
                        <img src="{{ 'data:image/jpeg;base64,'.base64_encode(file_get_contents(storage_path('app/public/'.$order->product->image_path.''))) }}" width="60px" height="60px" alt="abc">
                    @else
                        -
                    @endif
                </td>
            @endif
            <td><b>
                @foreach ($invoice_fields_array as $columns )
                {{ $order->product->$columns?:""." " }}
                @endforeach
            </b></td>
            @foreach($challan->o_details as $details)
                @if($details['order_id'] == $order->id)
                    @php($detail = $details)
                @endif 
            @endforeach
            <td>{{ $detail['quantity'] }}</td>
            <td>{{ !empty($detail['status']) ? ucfirst($detail['status']) : '' }}</td>
            <td>{{ !empty($detail['remarks']) ? $detail['remarks'] : '' }}</td>
        </tr>
        @php($serial_no++)
    @endforeach
    </tbody>
</table>