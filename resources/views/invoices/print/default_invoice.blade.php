<style> .header-height {line-height: 20px;} </style>

<table class="table" border="1" cellpadding="6">
    <?php
        $custom_width = null;
        $description_enable = strpos(session()->get('settings.products.invoice_fields'), 'description');
        $category_enable = strpos(session()->get('settings.products.invoice_fields'), 'category');
        $seprate_prod_fields = session()->get('settings.products.seprate_prod_fields')?:0;
        //if sale invoice with image enabled
        if($is_purchase !=1 && $image_enable == 1){$custom_width = "41%";}
        //if purchase invoice with image enabled
        elseif($image_enable == 1 && $is_purchase == 1){$custom_width = "59%";} 
        //if purchase invoice with image disabled
        elseif($image_enable != 1 && $is_purchase == 1){$custom_width = "79%";} 
        //default case with image disabled
        else {
            if(env('SHOES_COMPANY') == 1 && env('DOT_COM')){
                $custom_width ="33%";
            }
            elseif(env('SHOES_COMPANY') == 1){
                $custom_width ="45%";
            }
            elseif($seprate_prod_fields == 1 && $description_enable !== false){
                $custom_width ="20%";
                $description_width = "29%";
            }
            elseif(auth::user()->fixed_discount == 1  && $invoice->type != 'purchase'){
                $custom_width ="50%";
            }
            else{
                $custom_width ="61%";
            }
        }
        ?>
    <thead>
        <tr>
            <td width="10%" height="30px;"><h3>Sr No</h3></td>
            @if($image_enable == 1)
                <td width="20%"><h3>Image</h3></td>
            @endif
            <td width="{{ $custom_width }}"><h3>@if(env('SHOES_COMPANY') != 1 && $seprate_prod_fields != 1)Description @else Article @endif</h3></td>
            @if($description_enable !== false && $seprate_prod_fields == 1)
                <td width="{{ $description_width }}"><h3>Description</h3></td>
            @endif
            @if($category_enable !== false && $seprate_prod_fields == 1)
                <td width="12%"><h3>Category</h3></td>
            @endif
            @if(env('SHOES_COMPANY') == 1)
                <td width="8%"><h3>Color</h3></td>
            @endif
            <td width="8%"><h3>Qty</h3></td>
            @if(env('SHOES_COMPANY') == 1)
                <td width="8%"><h3>Size</h3></td>
            @endif
            @if($is_purchase !=1)
                @if(env('DOT_COM'))
                    <td width="12%"><h3>Rate/Pair</h3></td>
                @endif
                <td width="8%"><h3>Rate</h3></td>
                @if(auth::user()->fixed_discount == 1  && $invoice->type != 'purchase')
                    <td width="11%"><h3>Discount</h3></td>
                @endif
                <td width="10%"><h3>Amount</h3></td>
            @endif
        </tr>
    </thead>
    <tbody>
    @foreach ($items as $item)
        <tr>
            <td width="10%" height="24px;">{{ $serial_no + 1 }}</td>
            @if($image_enable == 1)
                <td width="20%">
                    @if($item['image'])
                        <img src="{{ 'data:image/jpeg;base64,'.base64_encode(file_get_contents(storage_path('app/public/'.$item['image'].''))) }}" width="60px" height="60px" alt="abc">
                    @else
                        -
                    @endif
                </td>
            @endif
            <td width="{{ $custom_width }}">{{ $item['name'] }}
            </td>
            @if($description_enable !== false && $seprate_prod_fields == 1)
                <td width="{{ $description_width }}">{{ $item['description'] }}</td>
            @endif
            @if($category_enable !== false && $seprate_prod_fields == 1)
                <td width="12%">{{ $item['category'] }}</td>
            @endif
            @if(env('SHOES_COMPANY') == 1)
                <td width="8%">{{ $item['color'] }}</td>
            @endif
            <td width="8%">{{ $item['quantity'] }} {{session()->get('settings.sales.enable_units_invoice')?$item['unit']: "" }} </td>
            @if(env('SHOES_COMPANY') == 1)
                <td width="8%">{{ $item['size'] }}</td>
            @endif
            @php
                $pricing = $item['sale_price']; //to check if discount if applied
                if(auth::user()->fixed_discount == 1  && $invoice->type != 'purchase')
                {
                    $pricing = number_format($item['min_sale_price']);
                }

                    $final_price = $item['sale_price'];
                    $orignal_price = $item['min_sale_price'];
                    $discount = $orignal_price - $final_price;
                    $discount_p =   $discount * 100 / $orignal_price;
                @endphp

            @if($is_purchase !=1)
                @if(env('DOT_COM'))
                    <td width="12%">{{ $item['sale_price']/12 }}</td>
                @endif
                <td width="8%">{{ ($invoice->is_manual ? '-' : $pricing) }}</td>
                @if(auth::user()->fixed_discount == 1  && $invoice->type != 'purchase')
                @php
                    $final_price = $item['sale_price'];
                    $orignal_price = $item['min_sale_price'];
                    $discount = $orignal_price - $final_price;
                    $discount_p =   $discount * 100 / $orignal_price;
                @endphp
                    <td width="11%">{{ number_format($discount_p) }}% ({{check_negative($final_price)}})</td>
                @endif

                {{-- @if(auth::user()->fixed_discount == 1  && $invoice->type != 'purchase')
                <td width="10%" align="right">
                    {{ $invoice->is_manual ? '-' : (!empty($item['quantity']) ? number_format(abs($item['min_sale_price'] * $item['quantity'])) : '') }}
                </td>
                @else --}}
                <td width="10%" align="right">
                    {{ $invoice->is_manual ? '-' : (!empty($item['quantity']) ? number_format(abs($item['sale_price'] * $item['quantity'])) : '') }}
                </td>
                {{-- @endif --}}
            @endif
        </tr>
        @php($serial_no++)
    @endforeach
    </tbody>
</table>
