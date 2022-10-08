@extends('layout')
@section('header')
<div class="page-header">
        <h1>Product #{{$product->id}}</h1>
        <a class="btn btn-link" href="{{ route('products.index') }}">
            <i class="glyphicon glyphicon-backward"></i>  Back to All Products
        </a>
        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display: inline;" >
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="btn-group pull-right" role="group" aria-label="...">
                <a class="btn btn-warning btn-group" role="group" href="{{ route('products.edit', $product->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                @if (session()->get('settings.barcode.is_enable'))
                    <a class="btn btn-success btn-group" role="group" target="_blank" href="{{ url('barcode_print/'.$product->id) }}"><i class="glyphicon glyphicon-print"></i> Barcode</a>
                @endif
                <button type="submit" onclick="if(confirm('Delete? Are you sure?')) { return true } else {return false };" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i> Delete</button>
            </div>
        </form>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">


  <ul class="list-group list-group-flush" style="font-size: 15px">
    <li class="list-group-item"><b>ID:</b> {{$product->id}}</li>
    @if (session()->get('settings.barcode.is_enable'))
        <li class="list-group-item"><b>Bar Code:</b> {{$product->barcode}}</li>    
        <li class="list-group-item"><b>Barcode:</b>
        @if ($product->barcode)
            @php
                try {
                    $code = @DNS1D::getBarcodeSVG($product->barcode, "C128",1,40);
                } catch(\Exception $e) {
                    $code = "";
                }
            @endphp
            <ul>
                <li><small>{{ session()->get('settings.profile.company') }}</small></li>
                <li>{!! $code !!}</li>
                <li><small>{{$product->barcode}}</small></li>
                <li><small>{{$product->name}}</small></li>        
            </ul>
        </li>
        @else N/A @endif
    @endif
    <?php $balance = calculateStock($product) ;?> 
    <li class="list-group-item"><b>Name:</b> {{$product->name}}&emsp; {{$product->translation}}</li>
    <li class="list-group-item"><b>Brand:</b>{{$product->brand}}</li>
    @if ((strpos(session()->get('settings.products.optional_items'),'size') !== false) && !empty($product->size))
        <li class="list-group-item"><b>Size: </b>{{$product->size}}</li>
    @endif
    @if ((strpos(session()->get('settings.products.optional_items'), 'color') !== false) && !empty($product->color))
        <li class="list-group-item"><b>Color: </b>{{$product->color}}</li>
    @endif
    @if ((strpos(session()->get('settings.products.optional_items'), 'pattern') !== false) && !empty($product->pattern))
        <li class="list-group-item"><b>Pattern: </b>{{$product->pattern}}</li>
    @endif
    @if ((strpos(session()->get('settings.products.optional_items'), 'length') !== false) && !empty($product->length))
        <li class="list-group-item"><b>Length: </b>{{$product->length}}</li>
    @endif
    @if ((strpos(session()->get('settings.products.optional_items'), 'width') !== false) && !empty($product->width))
        <li class="list-group-item"><b>Width: </b>{{$product->width}}</li>
    @endif
    @if ((strpos(session()->get('settings.products.optional_items'), 'height') !== false) && !empty($product->height))
        <li class="list-group-item"><b>Height: </b>{{$product->height}}</li>
    @endif
    <li class="list-group-item"><b>In Stock:</b>{{$balance}}</li>
    <li class="list-group-item"><b>Notify on Quantity:</b> {{$product->notify}}</li>
  </ul>
</div>
</div>

 <div class="row">
    <div class="col-md-12">
<hr>
 <div class="content-panel">
<h4>Stock Available in Warehouses</h4>
  <ul class="list-group list-group-flush" style="font-size: 15px">
  @foreach($warehouses as $warehouse)
  <?php $st = warehouse_stock($product, $warehouse->id); ?>
  @if($st)
    <li class="list-group-item"><b>{{$warehouse->name}}:</b> <span style="{{($st < 0)?'color:red':''}}">{{$st}}</span></li>
    @endif
  @endforeach
  </ul>
</div>
<hr>
    <div class="content-panel">
    <h4>Stock Status(Batch Wise)</h4>
      <ul class="list-group list-group-flush" style="font-size: 15px">
      @foreach($batches as $key => $value)
        <li class="list-group-item"><b>Batch NO. {{$key}}:</b>&nbsp; <span>{{$value}}</span></li>
      @endforeach
      </ul>
    </div>
<hr>
 <div class="content-panel">
    <table class="table table-bordered table-striped table-condensed table-hover datatable">
         <thead>
             <tr>
                <th>id</th>
                <th>Batch Number</th>
                <th>Date</th>
                <th class="numeric">Warehouse</th>
                <th class="numeric">Type</th>
                <th>Doc#</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Stock Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stocks as $key=> $value)
            <tr>
                <td>{{$value->id}}</td>
                <td>{{ empty($value->batch_id) ? '-' : $value->batch_id }}</td>
                <td>{{$value->date}}</td>
                <td><a href="/warehouses/{{$value->warehouse->id}}">{{$value->warehouse->name}}</a></td>
                <td><label class="label label-info">{{$value->type}}</label></td>
                <td>
                    @if($value->sale || $value->order)
                    <?php $order = ($value->sale)?:$value->order; ?>
                    <a target="_blank" href="{{url('invoices/'.$order->invoice_id)}}" class="btn btn-sm btn-default">Invoice #{{$order->invoice_id}}</a>
                    @else
                    <a target="_blank" href="{{url('stock?search='.$value->id)}}" class="btn btn-sm btn-default">StockEntry #{{$value->id}}</a>
                    @endif
                </td>
                @if ($value->type == "in" || $value->type == "purchase")
                <td><h4><label class="label label-success">+{{$value->quantity + 0}}</label></h4></td>
                @else
                <td><h4><label class="label label-danger">-{{$value->quantity + 0}}</label></h4></td>
                @endif
                @if($value->type != "purchase" && $value->sale)
                    <td>{{$value->sale->salePrice + 0}}</td>
                @else
                    <td>{{ $show_purchase_price ? $value->sale->salePrice +  0 : "-"}}</td>
                @endif
                <td>{{ $balance }}</td>
            </tr>
            <?php
            //reverse couting
            if (in_array($value->type, ['in','purchase']))
            {
                $balance -= $value->quantity;
            }else{
                $balance += $value->quantity;
            }
            ?>
            @endforeach
        </tbody>
      </table>  
 </div>

        </div>
    </div>

@endsection