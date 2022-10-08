@extends('layout')

@section('header')
    
@endsection

@section('content')
    <div class="row mt">

    <div class="col-md-12">

    

    </div>
        <div class="col-md-12 p-x-2">
          <?php $products = stock_notice() ?>
            @if(count($products))

            <div class="content-panel">
                          <h3><i class="fa fa-angle-right"></i><span class="fa fa-warning" style="color:red"></span>Out of Stock</h3>(<a href="/products">View All Products</a>)<hr>
                          <table class="table table-bordered table-striped table-condensed table-hover datatable">
                              
                              
                              <thead>
                              <tr>
                                <th class="numeric">ID</th>
                                <th class="numeric">Barcode</th>
                                <th>Name</th>
                                @if(strpos(session()->get('settings.products.optional_items'),'description') !== false)
                                <th>Description</th>
                                @endif
                                <th>Brand</th>
                                <th class="numeric">Total Stock</th>
                                <th class="numeric">Notify Quantity</th>
                              </tr>
                              </thead>
                              <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td class="numeric">{{$product->id}}</td>
                                <td class="numeric">@if ($product->barcode):<?=DNS1D::getBarcodeSVG($product->barcode, "C128")?><br>{{$product->barcode}}@else {{"N/A"}} @endif</td>
                                <td>{{$product->name}}</td>
                                @if(strpos(session()->get('settings.products.optional_items'),'description') !== false)
                                <td>{{$product->description?:'-'}}</td>
                                @endif
                                <td class="numeric">@if ($product->brand) {{$product->brand}}@else {{"N/A"}} @endif</td>
                                <td class="numeric" style="color:red">{{calculateStockById($product->id)}}</td>
                                <td class="numeric" style="color:brown">{{$product->notify}}</td>
                            </tr>
                        @endforeach
                              </tbody>
                          </table>
                      </div>
            @else
                <h3 class="text-center alert alert-info">Congratulation All Products have nice stock!</h3>
            @endif

        </div>


@endsection

@section('scripts')
<script type="text/javascript">



</script>
@endsection