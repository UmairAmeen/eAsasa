@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{asset('assets/css/croppie.css')}}">
@endsection
@section('header')
  <div class="page-header">
    <h1><i class="glyphicon glyphicon-edit"></i>Edit Products #{{$product->id}}</h1>
  </div>
@endsection
@section('content')
  @include('error')
  <div class="row">
    <div class="col-md-12">
      <div class="col-md-12">
        <div class="content-panel">
          <div class="multiRowInput">
            <form id="pupdate" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data" method="POST">
              <div id="log"></div>
              <input type="hidden" name="_method" value="PUT">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="product_name">Product Name*</label>
                      <input type="text" class="form-control input-sm" name="name" value="{{$product->name}}" placeholder="Product Name" required>
                    </div>
                    <div class="form-group">
                      <label for="product_category">Product Category</label>
                      <select name="product_category" class="form-control input-sm">
                        @foreach ($product_categories as $pcate)
                          <option value="{{$pcate->id}}"  {{is_selected($product->category_id, $pcate->id)}}>{{$pcate->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="product_barcode">Barcode/Item Code</label>
                      <input type="text" class="form-control input-sm" name="barcode" placeholder="Barcode" value="{{$product->barcode}}">
                    </div>
                    @if(session()->get('settings.products.enable_advance_fields'))
                      <div class="form-group">
                        <label for="sale_price">Sale Price*</label>
                        <input type="text" class="form-control input-sm" name="sale_price" placeholder="Sale Price" value="{{$product->salePrice + 0}}" required>
                      </div>
                      <div class="form-group">
                        <label for="min_sale_price">Min Sale Price</label>
                        <input type="text" class="form-control input-sm" name="min_sale_price" placeholder="Min Sale Price" value="{{$product->min_sale_price + 0}}">
                      </div>
                    @endif
                    @if(session()->get('settings.products.is_image_enable'))
                      <div class="col-md-4 form-group">
                        <label>Image (optional)</label>
                        <div id="upload-image"></div>
                        <input type="file" class="form-control" placeholder="Product Image" id="images">
                        <input type="hidden" name="image" id="imageBinary">
                        <div class="col-md-4 crop_preview">
                          <div id="upload-image-i"></div>
                        </div>
                      </div>
                    @endif
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="product_barcode">Units</label>
                      <select name="unit" class="form-control input-sm">
                        @foreach($units as $unit)
                        <option value="{{$unit->id}}" {{is_selected($product->unit_id, $unit->id)}}>{{$unit->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    @if(session()->get('settings.products.enable_advance_fields'))
                      <div class="form-group">
                        <label for="product_quantity_alert">Low Quantity Alert</label>
                        <input type="number" min="0" class="form-control input-sm" name="notify_quantity" placeholder="Low Stock Alert" value="{{$product->notify}}">
                      </div>
                      <div class="form-group">
                        <label for="supplier">Supplier</label>
                        <select name="supplier" class="form-control select-2">
                          <?php $supp = get_supplier_of_product($product->id) ?>
                          @if($supp)
                            <option value="{{ $supp->id }}">{{ $supp->name }}</option>
                          @else
                            <option value=0>Select A Supplier</option>
                          @endif
                          @foreach (get_all_suppliers() as $supplier)
                            @if($supplier->id == $supp->id)
                              @continue
                            @endif
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="cost_price">Cost Price</label>
                        <input type="number" class="form-control input-sm" name="purchase_price" placeholder="Cost Price" value="{{last_purchased_price($product->id)}}">
                      </div>
                      <div class="form-group">
                        <label for="PCT_Code">PCT Code</label>
                        <input type="text" class="form-control input-sm" name="pct_code" placeholder="PCT Code" value="{{$product->pct_code}}">
                      </div>
                      <div class="form-group">
                        <label for="Tax-Rate">Tax Rate (in %)</label>
                        <input type="text" class="form-control input-sm" name="tax_rate" placeholder="Tax Rate (in %)" value="{{$product->tax_rate}}" required>
                      </div>
                    @endif
                    <div class="form-group">
                      <label>Brand:</label>
                      <input type="text" name="brand" value="{{$product->brand}}" class="form-control" placeholder="Product Brand">
                    </div>
                    @if(strpos(session()->get('settings.products.optional_items'), 'size') !== false)
                    <div class="form-group">
                      <label>Size:</label>
                      <input type="text" name="size" value="{{$product->size}}" class="form-control" placeholder="Product Size">
                    </div>
                    @endif
                    @if(strpos(session()->get('settings.products.optional_items'), 'color') !== false)
                    <div class="form-group">
                      <label>Color:</label>
                      <input type="text" name="color" value="{{$product->color}}" class="form-control" placeholder="Product Color">
                    </div>
                    @endif
                    @if(strpos(session()->get('settings.products.optional_items'), 'pattern') !== false)
                    <div class="form-group">
                      <label>Pattern:</label>
                      <input type="text" name="pattern" value="{{$product->pattern}}" class="form-control" placeholder="Product Pattern">
                    </div>
                    @endif
                    @if(strpos(session()->get('settings.products.optional_items'), 'pattern') !== false)
                    <div class="form-group">
                      <label>Length:</label>
                      <input type="text" name="length" value="{{$product->length}}" class="form-control" placeholder="Product Length">
                    </div>
                    @endif
                    @if(strpos(session()->get('settings.products.optional_items'), 'pattern') !== false)
                    <div class="form-group">
                      <label>Width:</label>
                      <input type="text" name="width" value="{{$product->width}}" class="form-control" placeholder="Product Width">
                    </div>
                    @endif
                    @if(strpos(session()->get('settings.products.optional_items'), 'pattern') !== false)
                    <div class="form-group">
                      <label>Height:</label>
                      <input type="text" name="height" value="{{$product->height}}" class="form-control" placeholder="Product Height">
                    </div>
                    @endif

                    @if(!session()->get('settings.products.enable_advance_fields'))
                      <div class="form-group">
                        <label>Price:</label>
                        <input type="text" name="sale_price" value="{{$product->salePrice + 0}}" class="form-control" placeholder="Product Price">
                        <small id="emailHelp" class="form-text text-muted">This is required</small>
                      </div>
                    @endif
                    <div class="form-group">
                      <label>Next Purchase Price:</label>
                      <input type="text" name="next_purchase_price" value="{{$product->next_purchase_price + 0}}" class="form-control" placeholder="Product Next Purchase Price">
                    </div>
                    <div class="form-group">
                      <label for="description">Description</label>
                      <textarea placeholder="Product Description/Notes" name="description" id="product_desc" class="form-control input-sm" cols="30" rows="5">{{$product->description}}</textarea>
                    </div>
                  </div>
                </div>
                @if(!empty($purchasePrices))
                  <div class="col-md-12">
                    <h3>Purchase History</h3>
                    <div class="table-responsive">
                      <table class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Supplier Name</th>
                            <th>Batch Number</th>
                            <th>Purchase Price</th>
                          </tr>            
                        </thead>
                        <tbody>
                          @if(count($purchasePrices) > 0)
                            @foreach ($purchasePrices as $priceData)
                            <tr>
                              <th>{{date_format_app($priceData->date)}}</th>
                              <th>{{!empty($suppliers[$priceData->supplier_id]) ? $suppliers[$priceData->supplier_id] : ""}}</th>
                              <th>{{$priceData->id}}</th>
                              <th>{{$priceData->price + 0}}</th>
                            </tr>
                            @endforeach
                          @else
                            <tr><td colspan="3">No previous purchase price record found.</td></tr>
                          @endif
                        </tbody>
                      </table>
                    </div>
                  </div>
                @endif
              </div>
              <div class="well well-sm">
                  <a type="submit" class="btn btn-primary" href="javascript:void(0)" onclick="return updateProducts()">Save</a>
                  <a class="btn btn-link pull-right" href="{{ route('products.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="{{asset('assets/js/croppie.min.js')}}"></script>
<script>
  const image = "{{ empty($product->image_path) ? null : 'data:image/jpeg;base64,'.base64_encode(file_get_contents(storage_path("app/public/".$product->image_path))) }}";
  $('.date-picker').datepicker({});
</script>
@include('products.croppieImage')
@endsection
