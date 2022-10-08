<style>
    .tr-height {
        line-height: 24px;
    }

    .tr-font {
        font-size: 2em;
        text-decoration-color: #392A10;
    }

    /* .td-bottom { border-bottom: 1px solid black; } */
    .center-text {
        text-align: center;
    }

</style>


<table width="100%" border="0">
    <tr>
        <td>
            <table width="100%" border="0">
                <tr class="tr-height">
                    <td width="12%">Date: </td>
                    <td width="28%">{{ date('d-m-Y', strtotime($challan->date)) }}</td>
                    <td width="15%"></td>
                    <td width="15%"></td>
                    <td width="12%">Delivery No:</td>
                    <td width="18%">{{ $challan->id }}</td>
                </tr>
                <tr class="tr-height">
                    <td>Customer: </td>
                    <td><b>{{ $customer->name }}</b></td>
                    <td class="center-text">Mobile:</td>
                    <td><b>{{ $customer->phone }}</b></td>
                    <td width="12%">Order No:</td>
                    <td width="18%">{{ str_pad($challan->order_no, 6, '0', STR_PAD_LEFT) }}</td>

                </tr>
                <tr class="tr-height">
                    <td>Address: </td>
                    <td colspan="3" rowspan="2">{{ !empty($challan->address) ? $challan->address : $customer->address }}
                    </td>
                    <td width="12%">Rep By:</td>
                    <td width="18%">{{ $challan->rep_by }}</td>
                    {{-- <td  width="18%"></td> --}}
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2"></td>
                </tr>
            </table>

        </td>
    </tr>
    <tr>
        <td></td>
    </tr>
</table>
<?php
    $image_enable = session()->get('settings.sales.is_image_enable_in_purchase_order');
    //if image enabled
    if($image_enable == 1){$custom_width = "28%";}
    //if image disabled    
    else{$custom_width = "48%";} 
?>
<table border="1" cellpadding="6" width="100%">
    <tr>
        <td width="10%" height="30px;"><b>Sr No</b></td>
        @if($image_enable == 1)
                <td width="20%"><h3>Image</h3></td>
        @endif
        <td width="{{ $custom_width }}"><b>Description</b></td>
        <td width="12%"><b>Qty</b></td>
        <td width="12%"><b>Status</b></td>
        <td width="18%"><b>Remarks</b></td>
    </tr>
    @php($invoice_fields_array = explode(",",session()->get('settings.products.invoice_fields')))
    @foreach ($challan->o_details as $detail)
        @php($order = $orders->where('id', intval($detail['order_id']))->first())
        <tr>
            <td height="24px;">{{ $serial_no + 1 }}</td>
            @if($image_enable == 1)
                <td width="20%">
                    @if($item['image'])
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
            <td>{{ $detail['quantity'] }}</td>
            <td>{{ !empty($detail['status']) ? ucfirst($detail['status']) : '' }}</td>
            <td>{{ !empty($detail['remarks']) ? $detail['remarks'] : '' }}</td>
        </tr>
        @php($serial_no++)
    @endforeach
    </tbody>
</table>
</div>
</div>