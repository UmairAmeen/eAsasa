@extends('layout')
@section('css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" type="text/css" href="{{asset('assets/css/croppie.css')}}">
@endsection
@section('content')

    <div class="row mt">
    <div class="col-md-12">
    <!-- <a href="#search" data-toggle="modal">
      <div class="col-md-2 col-sm-2 box0">
        <div class="box1">
          <span class="fa fa-search"></span>
            <h3>Search</h3>
        </div>
        <p>Search Products</p>
      </div></a> -->
    @if (is_allowed('product-create'))
    <a href="#addproduct" data-toggle="modal" accesskey="n">
      <div  class="col-md-3 box0">
        <div class="box1">
          <span class="fa fa-plus"></span>
            <h3>Add New</h3>
            <small>ALT + N</small>
        </div>
        <p>Add Products</p>
      </div>
    </a>
    @endif
<a href="{{url('product_groups')}}" accesskey="x">
      <div  class="col-md-3 box0">
        <div class="box1">
          <span class="fa fa-plus"></span>
            <h3>Product Bundle</h3>
            <small>ALT + X</small>
        </div>
        <p>Manage Product Grouping</p>
      </div>
      </a>

      @if (is_allowed('product-import-export'))
      <a href="{{url('products.xlsx')}}">
        <div  class="col-md-3 box0">
        <div class="box1">
          <span class="fa fa-arrow-down"></span>
          <h3>Download Excel</h3>
          <span>With Purchase Price Facility</span>
        </div>
      </div></a>

        <a data-toggle="modal" data-target="#uploadExcel">
        <div  class="col-md-3 box0">
        <div class="box1">
          <span class="fa fa-arrow-up"></span>
          <h3>Import Excel</h3>
        </div>
      </div></a>
      @endif

    </div>
        <div class="col-md-12 p-x-2">
          @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
          @endif
          @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
          @endif
          @if($products_count > 0)
            <div class="content-panel">
              <h4><i class="fa fa-angle-right"></i>Products ({{$products_count}})</h4>
              <hr>
              <p>
                <div class="col-md-12" style="display: none">
                  <div class="col-md-6">
                    <div class="form-group">
                        <label>Product Barcode</label>  
                        <input type="text" id="search_barcode" name="product_barcode" class="form-control" placeholder="Search Prodct Barcode.">
                      </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                        <label>Product Name</label>  
                        <input type="text" id="search_name" name="product_name" class="form-control" placeholder="Search Prodct Name.">
                      </div>
                  </div>
                  <div class="col-md-12" style="background: #9c27b047;padding: 10px;">
                    <div class="form-group">
                      <label>Super Search</label>  
                      <input type="text" id="search_brand" name="brand" class="form-control" placeholder="Super Search">
                    </div>
                  </div>
                </div>
              </p>
              <hr>
              <div class="table-responsive">
                <table class="table table-bordered table-condensed table-hover table-striped products_listing table-responsive">
                  <thead>
                    <tr>
                      <th class="numeric">ID</th>
                      @if(session()->get('settings.products.is_image_enable'))<th>Image</th>@endif
                      @if(session()->get('settings.barcode.is_enable'))<th class="numeric">Barcode</th>@endif
                      <th>Item Code</th>
                      <th>Name</th>
                      @if(strpos(session()->get('settings.products.optional_items'), 'description') !== false)
                      <th>Description</th>
                      @endif
                      <th>Brand</th>
                      <th>Category</th>
                      @if(strpos(session()->get('settings.products.optional_items'), 'size') !== false)
                      <th>Size</th>
                      @endif
                      @if(strpos(session()->get('settings.products.optional_items'), 'color') !== false)
                      <th>Color</th>
                      @endif
                      @if(strpos(session()->get('settings.products.optional_items'), 'pattern') !== false)
                      <th>Pattern</th>
                      @endif
                      @if(strpos(session()->get('settings.products.optional_items'), 'length') !== false)
                      <th>Length</th>
                      @endif
                      @if(strpos(session()->get('settings.products.optional_items'), 'width') !== false)
                      <th>Width</th>
                      @endif
                      @if(strpos(session()->get('settings.products.optional_items'), 'height') !== false)
                      <th>Height</th>
                      @endif
                      <th class="numeric">Sale Price</th>
                      <th class="numeric">Minimum Sale Price</th>
                      <th>Next Purchase Price</th>
                      <th>Landing Cost</th>
                      <!-- <th class="numeric">Notify Quantity</th> -->
                      <th>Total Stock</th>
                      <th>Last Purchased Price</th>
                      <th>Added By</th>
                      <th>Updated By</th>
                      <th class="text-right"><i class=" fa fa-edit"></i> Options</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
            @else
              <h3 class="text-center alert alert-info">Empty!</h3>
            @endif
        </div>
        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" data-backdrop="static" id="addproduct" class="modal fade">
        <div class="modal-dialog" style="width: 95%">
         <div class="modal-content col-md-12">
          <div class="col-md-12">
            <div class="">
               <form id="product_add_form" action="{{ route('products.store') }}" >
              <div class="multiRowInput">
              <div id="log"></div>
              <div class="col-md-12" id="appendMe">
                <div class="parent_row">
                  <div class="row">
                    @php($class = (session()->get('settings.products.is_image_enable')) ? "col-md-8" : "col-md-12")
                    <div class="{{$class}}">
                      @if(session()->get('settings.barcode.is_enable'))
                      <div class="col-md-4 form-group">
                        <label>Barcode</label>
                        <input type="text" name="barcode[]" class="form-control" placeholder="Barcode" autofocus>
                      </div>
                      @endif
                      <div class="col-md-4 form-group">
                        <label>Name</label>
                        <input type="text" name="name[]" class="form-control" placeholder="Name">
                      </div>
                      <div class="col-md-4 form-group">
                        <label>Urdu Name [optional]</label>
                        <input type="text" name="translation[]" class="form-control" placeholder="Urdu Name">
                      </div>
                      <div class="col-md-4 form-group">
                        <label>Product Category</label>
                        {!! Form::select('product_category[]', $category->pluck('name', 'id')->toArray(), NULL,['class' => 'form-control']) !!}
                      </div>
                      @if(strpos(session()->get('settings.products.optional_items'), 'size') !== false) 
                      <div class="col-md-4 form-group">
                        <label>Size</label>
                        <input type="text" name="size[]" class="form-control" placeholder="Size">
                      </div>
                      @endif
                      @if(strpos(session()->get('settings.products.optional_items'), 'color') !== false) 
                      <div class="col-md-4 form-group">
                        <label>Color</label>
                        <input type="text" name="color[]" class="form-control" placeholder="Color">
                      </div>
                      @endif
                      @if(strpos(session()->get('settings.products.optional_items'), 'pattern') !== false) 
                      <div class="col-md-4 form-group">
                        <label>Pattern</label>
                        <input type="text" name="pattern[]" class="form-control" placeholder="Pattern">
                      </div>
                      @endif
                      @if(strpos(session()->get('settings.products.optional_items'), 'pattern') !== false) 
                      <div class="col-md-4 form-group">
                        <label>Length</label>
                        <input type="text" name="length[]" class="form-control" placeholder="Length">
                      </div>
                      @endif
                      @if(strpos(session()->get('settings.products.optional_items'), 'pattern') !== false) 
                      <div class="col-md-4 form-group">
                        <label>Width</label>
                        <input type="text" name="width[]" class="form-control" placeholder="Width">
                      </div>
                      @endif
                      @if(strpos(session()->get('settings.products.optional_items'), 'pattern') !== false) 
                      <div class="col-md-4 form-group">
                        <label>Height</label>
                        <input type="text" name="height[]" class="form-control" placeholder="Height">
                      </div>
                      @endif
                      <div class="col-md-4 form-group">
                        <label>Brand</label>
                        <input type="text" name="brand[]" class="form-control" placeholder="Brand">
                      </div>
                      <div class="col-md-4 form-group">
                        <label>PCT Code</label>
                        <input type="text" name="pct_code[]" class="form-control" placeholder="PCT Code">
                      </div>
                      <div class="col-md-4 form-group">
                        <label>Tax Rate (in %)</label>
                        <input type="text" name="tax_rate[]" class="form-control" placeholder=" Tax Rate (in %)">
                      </div>
                      <div class="col-md-4 form-group">
                        <label>Notify on Quantity</label>
                        <input type="text" name="notify_quantity[]" value="0" class="form-control" placeholder="Notify on Quantity">
                      </div>
                      <div class="col-md-4 form-group">
                        <label>Unit</label>
                        {!! Form::select('unit[]', $units->pluck('name', 'id')->toArray(), NULL,['class' => 'form-control']) !!}
                      </div>
                      @if(session()->get('settings.products.enable_advance_fields'))
                        <div class="col-md-4 form-group">
                          <label>Initial Quantity</label>
                          <input type="text" name="initial_stock[]" class="form-control" placeholder="Initial Quantity">
                        </div>
                        <div class="col-md-4 form-group">
                          <label>Purchase Price</label>
                          <input type="text" name="purchase_price[]" class="form-control" placeholder="Purchase Price">
                        </div>
                        <div class="col-md-4 form-group supp_ware">
                          <label>Supplier</label>
                          {!! Form::select('supplier[]', $suppliers->pluck('name', 'id')->toArray(), NULL,['class' => 'form-control suppliedr', 'data-live-search'=> 'true', 'placeholder'=> 'Select Supplier' ]) !!}
                        </div>
                        <div class="col-md-4 form-group supp_ware">
                          <label>Warehouse</label>
                          {!! Form::select('warehouse[]', $warehouse->pluck('name', 'id')->toArray(), NULL,['class' => 'form-control suppliedr', 'data-live-search'=> 'true' ]) !!}
                        </div>
                        <div class="col-md-4 form-group">
                          <label>Min Sale Price</label>
                          <input type="text" name="min_sale_price[]" class="form-control" placeholder="Min Sale Price" value="0">
                        </div>
                        <div class="col-md-4 form-group">
                          <label>Sale Price</label>
                          <input type="text" name="sale_price[]" class="form-control" placeholder="Sale Price" value="0">
                        </div>
                        <div class="col-md-4 form-group">
                          <label>Next Purchase Price</label>
                          <input type="text" name="next_purchase_price[]" class="form-control" placeholder="Next Purchase Price" value="0">
                        </div>
                        <div class="col-md-8 form-group">
                          <label>Description</label>
                          <textarea name="description[]" id="product_desc" class="form-control input-sm" cols="30" rows="5" placeholder="Product Description/Notes" ></textarea>
                        </div>
                      @endif
                      <div class="col-md-4 form-group">
                        <label>Is Raw Material ?</label>
                        <input data-toggle="tooltip"  title="Product that is used as raw material" type="checkbox" name="raw[]" class="form-control textbox" onkeydown="keyDown(event, this)">
                      </div>
                      <div class="col-md-4 form-group">
                        <label></label>
                        <a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a>
                      </div>
                    </div>
                    @if(session()->get('settings.products.is_image_enable'))
                    <div class="col-md-4 form-group">
                      <label>Image (optional)</label>
                      <div id="upload-image"></div>
                      <input type="file" class="form-control" placeholder="Product Image" id="images">
                      <input type="hidden" name="image[]" id="imageBinary">
                      <div class="col-md-4 crop_preview">
                        <div id="upload-image-i"></div>
                      </div>
                    </div>
                    @endif
                    <div class="col-md-12">
                      <hr>
                    </div>
                    {{-- <td><input type="text" name="initial_stock[]" class="form-control" placeholder="Initial Quantity"></td>
                    <td><select name="unit[]" class="form-control">
                      @foreach($units as $unit)
                      <option value="{{$unit->id}}">{{$unit->name}}</option>
                      @endforeach
                    </select></td>
                    <td><input type="text" name="sale_price[]" class="form-control" placeholder="Sale Price" value="0"></td>
                    <td><input type="text" name="notify_quantity[]" value="0" class="form-control" placeholder="Notify on Quantity"></td>
                    <td><input type="text" name="purchase_price[]" class="form-control" placeholder="Purchase Price"></td>
                    <td class="supplier_td"><select name="supplier[]" class="form-control suppliedr"  data-live-search="true">
                      @foreach($suppliers as $supplier)
                       <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                      @endforeach
                    </select></td>
                    <td><select name="warehouse[]" class="form-control">
                      @foreach($warehouse as $wareh)
                      <option value="{{$wareh->id}}">{{$wareh->name}}</option>
                      @endforeach
                    </select></td>
                    <td><input data-toggle="tooltip"  title="Product that is used as raw material" type="checkbox" name="raw[]" class="form-control textbox" onkeydown="keyDown(event, this)"></td>
                    <td><a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td> --}}
                  </div>
                </div>
                {{-- <tr class="parent_row">
                  <td><input type="text" name="barcode[]" class="form-control" placeholder="Barcode" autofocus></td>
                   <td><input type="text" name="name[]" class="form-control" placeholder="Name"></td>
                   <td><input type="text" name="translation[]" class="form-control" placeholder="Urdu Name"></td>
                   <td><input type="text" name="size[]" class="form-control" placeholder="Size"></td>
                   <td><input type="text" name="brand[]" class="form-control" placeholder="Brand"></td>
                  <td><input type="text" name="initial_stock[]" class="form-control" placeholder="Initial Quantity"></td>
                  <td><select name="unit[]" class="form-control">
                    @foreach($units as $unit)
                    <option value="{{$unit->id}}">{{$unit->name}}</option>
                    @endforeach
                  </select></td>
                  <td><input type="text" name="sale_price[]" class="form-control input-sm" placeholder="Sale Price" value="0"></td>
                  <td><input type="text" name="min_sale_price[]" class="form-control input-sm" placeholder="Min Sale Price" value="0"></td>
                  <td><input type="text" name="notify_quantity[]" value="0" class="form-control input-sm" placeholder="Notify on Quantity"></td>
                  <td><input type="text" name="purchase_price[]" class="form-control input-sm" placeholder="Purchase Price"></td>
                  <td class="supplier_td"><select name="supplier[]" class="form-control suppliedr input-sm"  data-live-search="true">
                    @foreach($suppliers as $supplier)
                     <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                    @endforeach
                  </select></td>
                  <td><select name="warehouse[]" class="form-control input-sm" onkeydown="keyDown(event, this)">
                    @foreach($warehouse as $wareh)
                    <option value="{{$wareh->id}}" >{{$wareh->name}}</option>
                    @endforeach
                  </select></td>
                  <td><input data-toggle="tooltip"  title="Product that is used as raw material" type="checkbox" name="raw[]" class="form-control textbox" onkeydown="keyDown(event, this)"></td>
                  <td><a style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td>
                </tr> --}}

              </div>
              </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
              </div>
                </form>
            {{-- </div>           --}}
          </div>
          <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
          <center>
            <button class="btn btn-primary btn-lg" id="submit_btn" onclick="addproducts()">Add Product</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
            </center>
        </div>
        </div>
      </div>
    </div>
  </div>


<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" data-backdrop="static" id="showPricingTable" class="modal fade">
        <div class="modal-dialog" style="width: 90%">
         <div class="modal-content col-md-12">
               <form id="showPricingTableForm" action="{{ url('update_pricing_globally') }}" >
          <div class="col-md-12">
            <div class="content-panel">
              <div id="log"></div>
              <center><h1>Update Pricing By Amount</h1></center>
                  <div class="form-group">
                    <h4>Product # <span id="pidpricing"></span></h4>
                    <h4>Current Sale Price <span id="pricingspan"></span></h4>
                    <h3>Increase/Decrease Price By</h3>
                    <input type="number" class="form-control" step="0.01" name="value" placeholder="Price Increased or Decreased By">
                    <small>Add +50 / -50, this will increase/decrease price to all customers sale price too.</small>
                  </div>
            </div>          
          </div>
          <input type="hidden" name="product_id" value="0">
          <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
          <center>
            <button class="btn btn-primary btn-lg" type="submit">Update Pricing by Amount</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
            </center>
        </div>
            </form>
        </div>
      </div>
    </div>

    <div aria-hidden="true" aria-labelledby="Upload Excel" role="dialog" data-backdrop="static" id="uploadExcel" class="modal fade">
        <div class="modal-dialog" style="width: 90%">
         <div class="modal-content col-md-12">
               <form id="uploadExcelFile" action="{{ url('uploadExcel') }}" method="POST" enctype="multipart/form-data" class="no-ajax">
          <div class="col-md-12">
            <div class="content-panel">
              <div id="log"></div>
              <center><h1>Please Attach Excel File (xls/xlsx)</h1></center>
                  <div class="form-group">
                    <input type="file" name="importexcel" class="form-control" accept=".xlsx,.xls">
                  </div>
            </div>          
          </div>
          <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
          <center>
            <button class="btn btn-primary btn-lg" type="submit">Upload</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
            </center>
        </div>
            </form>
        </div>
      </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/js/bootstrap-select.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="{{url('supplier.json')}}"></script>
<script type="text/javascript">
image = null;
function displaypricing(element) {
  $("#pidpricing").html( $(element).data('id'));
  $("input[name=product_id]").val($(element).data('id'));
  $("#pricingspan").html( $(element).data('pricing'));
  $("#showPricingTable").modal('show');
}
function switchStatus(e, id) {
  if (e.innerHTML =="Active") {
    e.classList.remove('btn-success');
    e.classList.add('btn-danger');
    e.innerHTML ="In-Active";
  } else {
    e.classList.remove('btn-danger');
    e.classList.add('btn-success');
    e.innerHTML ="Active";
  }
  var request=$.ajax({type:'get',url:'{{url("/products")}}/'+id+'/switchStatus',dataType:'json',data:{'id':id}});
}
$(document).ready(function() {
  $('.suppliedr').selectpicker();
  $('.hover_stock').tooltip({
    content: '... fetching stock ...',
    open: function(evt, ui) {
      var elem = $(this);
      var id = $(this).data('id');
      $.ajax('{{url("get_product_stock_warehouse")}}?id='+id).always(function(data) {
        elem.tooltip('option', 'content', data);
      });
    }
  });
  // choices('.supplier','supplier_json_d');
  /*$('#search_brand', this).on('keyup change', function () {
    if (table.search() !== this.value) {
      table.search(this.value).draw();
    }
  });
  $('#search_name', this).on('keyup change', function () {
    if (table.column(2).search() !== this.value) {
      table.column(2).search(this.value).draw();
    }
  });
  $('#search_barcode', this).on('keyup change', function() {
    if (table.column(1).search() !== this.value) {
      table.column(1).search(this.value).draw();
    }
  });
  $(".pdatatable").DataTable({"processing": true,"serverSide": true,"ajax": "/dataTable_products"});*/
});
function clean(e, ele) {
  var row = $(ele).parent().parent().parent();
  var boss = $("#appendMe").children();
  if (boss.length > 1) {
    // var xo = $("#appendMe").append("<div class='parent_row'>"+$(".parent_row").html()+"</div>");
    // xo.children().last().children().find(".textbox").attr('onkeydown','keyDown(event, this)');
    $(row).remove();
  }// debugger;
}

{{--var new_supplier_html = '<select name="supplier[]" class="form-control suppliedr"  data-live-search="true">\
  @foreach($suppliers as $supplier)\
    <option value="{{$supplier->id}}">{{$supplier->name}}</option>\
  @endforeach\
</select>';--}}
                  
function keyDown(e, me) {
  var keyCode = e.keyCode || e.which; 
  if (keyCode == 9) { 
    e.preventDefault(); 
    var xo = $("#appendMe").append("<div class='parent_row'>"+$(".parent_row").html()+"</div>");
    xo.children().last().children().find(".textbox").attr('onkeydown','keyDown(event, this)');
    xo.children().last().children().first().find("input").focus();
    xo.children().last().children().first().find(".supp_ware").find("button").remove();
    $('.suppliedr').selectpicker();
    // debugger;
    // xo.children().last().children("td.supplier_td").html(new_supplier_html);
    // xo.children().last().children().find("td.supplier_td").html(new_supplier_html);
    //$(me).removeAttr('onkeydown');
    $('[data-toggle="tooltip"]').tooltip(); 
    // $(".suppliedr").selectpicker("refresh");
    // $(".supplier").selectpicker("refresh");
    // $('.supplier').selectpicker();
    // choices('.supplier','supplier_json_d');
    // call custom function here
  }
}
function del_init() {
  $(".delete_the_product").click(function(e){
    e.preventDefault();
    var ele = $(this);
    alertify.confirm('Are you sure, you want to delete this product ?', 'Deleting this product will delete all related transactions, refunds, sale, purchase & inventory In/Out', function(){ return ele.parent('form').submit(); } , function(){ return true;});
  });
}

<?php $messageTop = "Product List";
$exportingCol = "0,2,3,4,6,7" ?>
var table = false;
table = $('.products_listing').DataTable({
  "ajax": "/products_listing_datatable",
  serverSide:true,
  processing:true,
  dom: "Blfrtip",
  lengthMenu : [[10, 25, 50, -1], [10, 25, 50, "All"]],
  "order": [[2,'asc']],
  stateSave: true,
  "stateLoadParams": function (settings, data) {
      data.search.search = "";
      data.length = 10;
      data.start = 0;
      data.order = [[2,'asc']];
  },
  "columns": [
    { "data": "id" },
    @if(session()->get('settings.products.is_image_enable')) { "data": "image",defaultContent:"-", searchable:false }, @endif
    @if(session()->get('settings.barcode.is_enable')) { "data": "barcode", defaultContent:"-", visible:true, searchable:false }, @endif
    { "data": "itemcode" },
    { "data": "name" },
    @if(strpos(session()->get('settings.products.optional_items'), 'description') !== false)
    { "data": "description", defaultContent:"-", visible:true, searchable:true },
    @endif
    { "data": "brand", defaultContent:"-", searchable:false },
    { "data": "category", defaultContent:"-", searchable:false },
    @if(strpos(session()->get('settings.products.optional_items'), 'size') !== false)
    { "data": "size", defaultContent:"-", visible:true, searchable:true },
    @endif
    @if(strpos(session()->get('settings.products.optional_items'), 'color') !== false)
    { "data": "color", defaultContent:"-", visible:true, searchable:true },
    @endif
    @if(strpos(session()->get('settings.products.optional_items'), 'pattern') !== false)
    { "data": "pattern", defaultContent:"-", visible:true, searchable:true },
    @endif
    @if(strpos(session()->get('settings.products.optional_items'), 'length') !== false)
    { "data": "length", defaultContent:"-", visible:true, searchable:true },
    @endif
    @if(strpos(session()->get('settings.products.optional_items'), 'width') !== false)
    { "data": "width", defaultContent:"-", visible:true, searchable:true },
    @endif
    @if(strpos(session()->get('settings.products.optional_items'), 'height') !== false)
    { "data": "height", defaultContent:"-", visible:true, searchable:true },
    @endif
    { "data": "salePrice" },
    { "data": "min_sale_price" },
    { "data": "next_purchase_price", defaultContent:"0", visible:true, searchable:false },
    { "data": "landing_cost" },
    { "data": "stock", defaultContent:"0", searchable:false, orderable:false },
    { "data": "purchase_price", defaultContent:"-", searchable:false, orderable:false, visible:true},
    { "data": "added_by", defaultContent:"-", "orderable": false, "searchable": false },
    { "data": "updated_by", defaultContent:"-", "orderable": false, "searchable": false },
    {"data": "options", searchable:false, orderable:false},
  ],
  "fnDrawCallback": function (oSettings) {
    del_init();
    //for stock
    $('.hover_stock').tooltip({
      classes:{"ui-tooltip":"highlight"},
      position:{ my:'left center', at:'right+50 center'},
      content:function(result) {
          $.get('{{url("get_product_stock_warehouse")}}', {
            id:$(this).data('id')
          }, function(data){
            result(data);
          });
      }
    });
    //low stock red
    $(".low_stock").parent().parent().addClass('danger')
  },
  buttons: [
    <?=get_print_template(".datatable", 'copy', 'Products', $messageTop, $exportingCol)?>, <?=get_print_template(".datatable", 'excel', 'Products', $messageTop, $exportingCol)?>, <?=get_print_template(".datatable", 'print', 'Products', $messageTop, $exportingCol);?>,'colvis'
  ]
});
del_init();
//choices('.supplier','supplier_json_d');
function choices(identifier,file_name){
  // var warehouse_id = $("#warehouse_d").val();
  $(identifier).select2({
    data: eval(file_name),        
    minimumInputLength: 0,
  }).focus();
  $(identifier).on('select2:close', function (e) {
    $(identifier).blur();
    $(this).parentsUntil('tr').siblings().children(".warehouse").focus();
  });
}
</script>
@include('products.croppieImage')
@endsection