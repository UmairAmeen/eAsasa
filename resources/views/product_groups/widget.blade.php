    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" data-backdrop="static" id="addproductgrpoup" class="modal fade">
      <div class="modal-dialog" style="width: 90%;">
         <div class="modal-content col-md-12 content-panel" style=" min-height: 300px">
          <div class="col-md-12">
            <div class="" style=" min-height: 300px">
              <div class="col-md-12">
                <h4 class="help"></h4>
              </div>
              <div class="col-md-4">
                <label class="form-text">Product Group</label><br>
                 <select class="form-control" name="p_group" id="p_group" style="width: 100%"></select>
              </div>
               <div class="col-md-4">
               <label class="form-text">Quantity</label><br>
               <input type="number" min="0" step="0.01" class="form-control" name="group_quantity" id="group_quantity" value="1.00">
             </div>
              
              <div class="col-md-4">
               <label class="form-text">Price</label><br>
               <input type="number" onkeyup="adjustPricing()" min="0" step="0.01" class="form-control" name="group_pricing" id="group_pricing">
             </div>
             <br>
               <div class="col-md-12" id="group_listing"></div>
            </div>          
          </div>
          <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
            <center>
              <button accesskey="m" class="btn btn-primary btn-lg" id="submit_btn" onclick="addproducts()">Add Product Group [ALT + M]</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
            </center>
          </div>
        </div>
      </div>
    </div>
@if(isset($purchase))
<script type="text/javascript" src="{{url('productgroup.json')}}?purchase=1"></script>
@else
<script type="text/javascript" src="{{url('productgroup.json')}}"></script>
@endif
<script type="text/javascript">
  $(document).on('shown.bs.modal', '#addproductgrpoup', function (e) {
    if ($("#s_type").length > 0) {
      $("#addproductgrpoup").find(".help").html("Selected Warehouse: "+$("#warehouse option:selected").html()+"<br> Transaction Type: "+$("#s_type").val());
    }
    $("#product").select2("close");
    $("#p_group").select2("open");
    $("#group_pricing").val("");
    $("#group_listing").html("");
    $("#group_quantity").val(1);
    group_cart = [];
  });
	var group_cart = [];
  var defined_price = 0;
  $("#p_group").select2({
    placeholder: "Select a product group",
    data:product_group_json_d,
    matcher: function (params, data) {
      if ($.trim(params.term) === '') {
          return data;
      }
      keywords=(params.term).split(" ");
      for (var i = 0; i < keywords.length; i++) {
        if ((((data.text).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1)) {
          return null;
        }        
      }
      return data;
    },
    minimumInputLength: 0,
  });
  $("#p_group").on('select2:close', function (e) {
    $("#group_listing").html("");
    $("#group_pricing").val("");
    var details = null;
    var selected_id = $(this).val();
    $.each(product_group_json_d, function(index, data) {
      if (data.id == selected_id) {
        details = data;
        return false;
      }
    });
    $("#group_pricing").val(details.price);
    $("#group_quantity").select();
    defined_price = details.price;
    var table = '<table class="table" id="xop"><thead><th>Product Detail</th><th>Quantity</th><th>Price</th></thead><tbody>';
    group_cart = [];
    $.each(details.products, function (index, data) {
      group_cart.push(jQuery.extend(true, {}, data));
      table += "<tr>";
      table += "<td>" + data.product.name + " " + data.product.brand + "</td>";
      table += "<td>" + data.quantity + "</td>";
      table += "<td><input data-default='" + data.product.salePrice + "' type='text' data-qty='" + data.quantity + "' data-id='" + data.product.id + "' class='form-control group_single_price' onkeyup='changeTotal()' data-misc='" + data.product.salePrice + "' value='" + data.product.salePrice + "'><small>{{ isset($purchase) ? "Purchase Price" : "Market Price" }}:" + data.product.salePrice + "</small></td>";
      table += "</tr>"
    });
    table +='<tbody></table>';
    $("#group_listing").html(table);
    @if(isset($purchase))
      changeTotal();
    @else
      adjustPricing();
    @endif
  });
  function changeTotal() {
	  var t_sum = 0;
	  $(".group_single_price").each(function(index, data) {
      var opo = parseFloat($(data).val());
      var qty = parseFloat($(data).data("qty"));
      if (!opo) {
        $(data).val(0);
        opo = 0;
      }
		  t_sum += opo * qty;
      $(group_cart).each(function(i, eprod) {
        if (eprod.product.id == $(data).data('id')) {
          group_cart[i].product.salePrice = opo;
        }
      });
	  });
    $("#group_pricing").val(t_sum.toFixed(0));
  }
  function adjustPricing() {
	  var group_price = parseFloat($("#group_pricing").val());
	  if (!group_price) {
		  return;
	  }
    var sub_total = 0;
    $(".group_single_price").each(function(index, data){
      var opo = parseFloat($(data).data('misc'));
      var qty = parseFloat($(data).data("qty"));
      if (!opo) {
        $(data).val(0);
        opo = 0;
      }
      sub_total += opo*qty;
    });
    if (sub_total == group_price) {
      return false;
    }
    difference = group_price - sub_total; //group's set prices
    // debugger
    $(".group_single_price").each(function(index, data) {
      var qty = parseFloat($(data).data("qty"));
      var x = (parseFloat($(data).data('misc')))/sub_total; //we got the ratio here
      var ppf = parseFloat($(data).data('misc')) + (x*difference);
      $(data).val(ppf.toFixed(0));
      $(group_cart).each(function(i, eprod) {
        if (eprod.product.id == $(data).data('id')) {
          group_cart[i].product.salePrice = ppf.toFixed(0);
        }
      });
    });
  }
  function addproducts() {
    var g_quantity = $("#group_quantity").val();
    if (!$.isNumeric(g_quantity) || g_quantity <= 0) {
      alertify.error('Invalid Group Quantity').dismissOthers();
    }
    var pricing = $("#group_pricing").val() * g_quantity;
    if (!$.isNumeric(pricing) || pricing < 0) {
      alertify.error('Invalid Group Pricing').dismissOthers();
    }
    var difference = 0;
    var ex = jQuery.Event("keydown");
    ex.which = 13;
    $.each(group_cart, function(index, data) {
      $("#product").val(data.product.id).trigger("change");
      $("#quantity").val(data.quantity * g_quantity).trigger("change");
      $("#saleprice").val(data.product.salePrice).trigger("change");
      difference += data.product.salePrice * data.quantity * g_quantity;
      $("#saleprice").trigger(ex);
    });
    // if (pricing != difference) {
    //   var sidas = parseFloat($("input[name=discount]").val());
    //   if (!sidas) {
    //     sidas = 0;
    //   }// 3000 + 6000 = -3000
    //   $("input[name=discount]").val(sidas - (pricing - difference)).trigger(jQuery.Event("keyup"));
    // }
    $("#addproductgrpoup").modal("hide");
  }
</script>