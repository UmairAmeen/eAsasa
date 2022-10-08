@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i>Create Sales</h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-12">
          <div class="col-md-3">
            <div class="form-group">
                <label>Product:</label>
                <select name="product" class="form-control product"></select>
                 <small id="emailHelp" class="form-text text-muted">This is required</small>
            </div>
          </div>
            
          <div class="col-md-3">
            <div class="form-group">
                <label>Warehouse:</label>
                <select name="warehouse" class="form-control warehouse"></select>
                 <small id="emailHelp" class="form-text text-muted">This is required</small>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group maybehidden" style="display: none">
                <label>Quantity:</label>
               <input type="number" name="amount" value="1" min="0" max="20" id="qty" class="form-control">
                 <small id="emailHelp" class="form-text text-muted">This is required</small>
            </div>
          </div>
            
          <div class="col-md-3">
            <div class="form-group maybehidden" style="display: none">
                <label>Sale Price:</label>
               <input type="number" name="saleprice" value="1" min="0" id="price" class="form-control">
                 <small id="emailHelp" class="form-text text-muted">This is required</small>
            </div>
          </div>
                <input type="hidden" id="product_id" value="0">
                <input type="hidden" id="product_qty" value="0">
                <input type="hidden" id="product_price" value="0">

            <div class="form-group maybehidden" style="display: none">
               <button id="add" class="btn btn-primary">Add</button>
            </div>

          </div>

          <div class="col-md-12">
            <form action="{{ route('sales.store') }}" method="POST" >
            <div id="log" class=""></div>
            <!-- <div id="orders">
                
            </div> -->
            <div class="col-md-12 pos_table" style="display:none;">
                <div class="content-panel">
                    <table id="pos" class="table table-condensed table-striped" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <td>Product</td>
                                <td>Warehouse</td>
                                <td>Price</td>
                                <td>Quantity</td>
                                <td>Total</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12 total_pos" style="display:none;">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <div class="col-md-6">
                        <h4>Sub Total</h4>
                        <h4>Discount (%)</h4>
                        <h4>GST (16%)</h4>
                        <h4>Total</h4>
                    </div>
                    <div class="col-md-6">
                        <h4 id="sub_total"></h4>
                        <input type="number" name="discount" id="discount" value="0" min="0" />
                        <h4 id="gst"></h4>
                        <h4 id="total"></h4>
                    </div>    
                </div>
            </div>

               <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Make Sale</button>
                    <a class="btn btn-link pull-right" href="{{ route('sales.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
                </div>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
    var counter = 0;
    var sub_total = 0;
    var gstPercent = 0;
    var total = 0;
    var discount = 0;
    var discountAmount = 0;

    pselect = $(".product").select2({
         ajax: {
          url: "/pagination_product_json/",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              page: params.page
            };
          },
          processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
              results: data.items,
              pagination: {
                more: (params.page * 10) < data.total_count
              }
            };
          },
          cache: true
        },
        // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 0,
    });

$(pselect).on('select2:select', function (evt) {
    $(".warehouse").val(0);
    $('.warehouse').trigger('change');
    $(".maybehidden").hide();


    var url = "/product_price_json?product_id="+$(pselect).select2('val');

    $.get( url, function( data ) {
      $("#qty").val(1);
        $("#product_price").val(data);
        $("#price").val(data);
    });
});

var wselect = $(".warehouse").select2({
         ajax: {
          url: "/warehousejson",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              product: $(pselect).select2('val'),
              // page: params.page
            };
          },
          processResults: function (data, params) {
            return {
              results: data,
             
            };
          },
          cache: true
        },
        minimumInputLength: 0
    });

$(wselect).on('select2:close', function (evt) {
    var warehouse_id = $(wselect).select2('val');
    if (warehouse_id == 0 || warehouse_id == null)
    {
        $(".maybehidden").hide();
        return;
    }else{
        $(".maybehidden").show();
    }
    var product_id = $(pselect).select2('val');
    var url = "/warehouse_product_json?warehouse_id="+warehouse_id+"&product_id="+product_id;

    $.get( url, function( data ) {
      $("#qty").val(1);
        $("#qty").attr('max',data);
        $("#product_qty").val(data);
    });
    ;
});

function remove(d,p)
{
    $(d).parents('tr').remove();
    
    sub_total = sub_total - p;
    gst = gstPercent * sub_total;
    total = sub_total + gst;

    $("#sub_total").html(sub_total);
    $("#gst").html(gst);
    $("#total").html(total);

    counter--;

    if (counter == 0) {
        $('.pos_table').slideUp('slow');
        $('.total_pos').slideUp('slow');
    }
    return ;
}

$("#add").click(function(e){
    var product_id = $(pselect).select2('val');
    var product_name = $(pselect).select2('data');

    var warehouse_id = $(wselect).select2('val');
    var warehouse_name = $(wselect).select2('data');

    var quantity = $("#qty").val();
    var saleprice = $("#price").val();
    var price = quantity * saleprice;

    var url = "/warehouse_product_json?warehouse_id="+warehouse_id+"&product_id="+product_id;

    $.get( url, function( data ) {
        if (quantity > data) {
          alert('Quantity exceeds');
          return false;
        } else {
          $("#pos").append('<tr><td>'+product_name[0].text+'<input type="hidden" name="product_ids[]" value="'+product_id+'"></td>'+
                '<td>'+warehouse_name[0].text+'<input type="hidden" name="warehouse_ids[]" value="'+warehouse_id+'"></td>'+
                '<td>'+saleprice+'<input type="hidden" name="saleprice[]" value="'+saleprice+'"></td>'+
                '<td>'+quantity+'<input type="hidden" name="quantity[]" value="'+quantity+'"></td>'+
                '<td>'+price+'</td>'+
                '<td><a onclick="return remove(this,'+price+')"><i class="fa fa-times" aria-hidden="true"></i></a></td>'+
                '</tr>');

          sub_total = sub_total + price;
          gst = gstPercent * sub_total;
          total = sub_total + gst;

          $("#sub_total").html(sub_total);
          $("#gst").html(gst);
          $("#total").html(total);

          counter++;

            // product_name[0].text+" - "+ warehouse_id + " - "+ warehouse_name[0].text + " - " + quantity + " - "+ saleprice);
          $('.pos_table').slideDown('slow');
          $('.total_pos').slideDown('slow');
        }
    });
});

$('#discount').on('change', function() {
  discount = $(this).val();

  gst = gstPercent * sub_total;
  total = sub_total + gst; 
  
  if (discount != 0) {
      discountAmount = total * (discount / 100);
      total = total - discountAmount;
  } 

  $("#total").html(total);  
});
  </script>

@endsection
