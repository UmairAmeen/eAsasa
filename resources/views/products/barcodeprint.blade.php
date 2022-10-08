@php($company = session()->get('settings.profile.company'))
@php($lineHeight = session()->get('settings.barcode.line_height'))
@php($titleFont = session()->get('settings.barcode.title_font_size'))
@php($txtFont = session()->get('settings.barcode.text_font_size'))
@php($priceFont = session()->get('settings.barcode.price_font_size'))
@php($selectedFields = explode(',', session()->get('settings.barcode.enable_fields')))

@php($price = empty($product->printPrice) ? $product->salePrice : $product->printPrice)
@php($barcodeWidth = 200)
@php($barcodeHeight = 112)

<html>
    <head>
        <title>{{$company}} Product Barcode</title>
    </head>
    <body style="margin: 0px;">
        @for ($copy = 0; $copy < session()->get('settings.barcode.no_of_copies'); $copy++)
            @php($leftMargin = $copy * $barcodeWidth)
            @php($topMargin = $copy > 0 ? -$barcodeHeight : 0)
            <div style="width: {{$barcodeWidth}}px; 
                        height: {{$barcodeHeight}};
                        margin-top: {{$topMargin}}px;
                        margin-left: {{$leftMargin}}px;
                        line-height: {{$lineHeight}}px;">
                <center>
                    @if(in_array('company', $selectedFields))
                        <span style="font-size: {{$titleFont?:'16'}}px;">{{ $company }}</span><br> 
                    @endif
                    {!! $code !!}<br>
                    @foreach (['barcode', 'name', 'size'] as $name)
                        @if (in_array($name, $selectedFields) && !empty($product->$name))
                            <span style="font-size: {{$txtFont?:'16'}}px;">{{$product->$name}}</span><br> 
                        @endif
                    @endforeach
                    @if (in_array('price', $selectedFields))
                        <span style="font-size: {{$priceFont?:'28'}}px; font-weight:bold;">Rs. {{ number_format($price, 0,'.', ',') }}</span>
                    @endif
                </center>
            </div>
        @endfor
    </body>
</html>
