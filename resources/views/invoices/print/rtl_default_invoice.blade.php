<style> .header-height {line-height: 20px;}
     td{
        text-align: right;
    }
</style>

<table class="table" border="1" cellpadding="6">
    <?php
        $custom_width = null;
        //if sale invoice with image enabled
        if($is_purchase !=1 && $image_enable == 1){$custom_width = "30%";}
        //if purchase invoice with image enabled
        elseif($image_enable == 1 && $is_purchase == 1){$custom_width = "67%";} 
        //if purchase invoice with image disabled
        elseif($image_enable != 1 && $is_purchase == 1){$custom_width = "77%";} 
        //default case with image disabled
        else {
            if(env('SHOES_COMPANY') != 1){
                $custom_width ="63%";
            }
            else{
                $custom_width ="37%";
            }
        }
        ?>
    <thead>
        <tr>
            @if($is_purchase !=1)
                <td width="8%"><h3>رقم</h3></td>
                <td width="8%"><h3>ریٹ</h3></td>
            @endif
            @if(env('SHOES_COMPANY') == 1)
                <td  width="8%"><h3>سائز</h3></td>
            @endif
            <td width="8%"><h3>تعداد</h3></td>
            @if(env('SHOES_COMPANY') == 1)    
                <td  width="8%"><h3>رنگ</h3></td>
            @endif
            <td width="{{ $custom_width }}"><h3>تفصیل</h3></td>
            @if($image_enable == 1)
            <td width="20%"><h3>تصویر</h3></td>
            @endif
            @if(env('SHOES_COMPANY') == 1)    
            <td width="10%"><h3>آرٹیکل</h3></td>
            @endif
            <td width="10%" height="30px;"><h3>سیریل نمبر</h3></td>
        </tr>
    </thead>
    <tbody>
    @foreach ($items as $item)
    <tr>
            @if($is_purchase !=1)
            <td width="8%" align="right">
                {{ $invoice->is_manual ? '-' : (!empty($item['quantity']) ? number_format($item['sale_price'] * $item['quantity']) : '') }}
            </td>
            <td width="8%">{{ ($invoice->is_manual ? '-' : $item['sale_price']) }}</td>
            @if(env('SHOES_COMPANY') == 1)
                <td width="8%">{{ $item['size'] }}</td>
            @endif
            <td width="8%">{{ $item['quantity'] }} {{session()->get('settings.sales.enable_units_invoice')?$item['unit']: "" }} </td>
            @if(env('SHOES_COMPANY') == 1)
                <td width="8%">{{ $item['color'] }}</td>
            @endif


            @endif
            <td width="{{ $custom_width }}">{{ $item['Description'] }}</td>
            @if($image_enable == 1)
            <td width="20%">
                @if($item['image'])
                <img src="{{ 'data:image/jpeg;base64,'.base64_encode(file_get_contents(storage_path('app/public/'.$item['image'].''))) }}" width="60px" height="60px" alt="abc">
                @else
                -
                @endif
            </td>
            @endif
            <td width="10%">{{ $item['name'] }}</td>
            <td width="10%" height="24px;">{{ $serial_no + 1 }}</td>
        </tr>
        @php($serial_no++)
    @endforeach
    </tbody>
</table>
