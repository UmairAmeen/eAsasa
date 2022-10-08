@extends('layout')
@section('css')
  <link href="{{asset('assets/css/bootstrap-datepicker.css')}}" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> Making a Sale Return </h1>
        <h3><a href="{{url('sales')}}">Back to All Sales</a></h3>
        <input type="hidden" value="{{ auth::user()->fixed_discount }}" id="fixed_discount" name="fixed_discount">
        <input type="hidden" value="{{ auth::user()->master_discount }}" id="master_discount" name="master_discount">
        <input type="hidden" value="{{ env('SALEPRICE_EDITABLE') ? 1 : 0}}" id="saleprice_readonly" name="saleprice_readonly">
    </div>
@endsection

@section('content')
    @include('error')
    <form id="product_add_form" action="{{ route('sales.store') }}" >
    <div class="row">
          <div class="col-md-12">
                            <div id="log"></div>

          <div class="col-md-12">
            <div class="content-panel">
               <div class="col-md-12" style="    background-color: cornsilk;">
                   <div class="col-md-3">
                       <div class="form-group" id="date_form_group">
                            <label>Date:</label>
                            <input type="date" name="date" value="{{date("Y-m-d")}}" class="form-control" required="required">
                             <small id="emailHelp" class="form-text text-muted">This is required</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                       <div class="form-group" id="date_form_group">
                            <label>Bill Number:</label>
                            <input type="text" name="bill_number" class="form-control" value="{{$sale_order->bill_number}}">
                             <small id="emailHelp" class="form-text text-muted">Optional, Bill number</small>
                        </div>
                    </div>
                   <div class="col-md-6">
                       <div class="form-group" id="customer_form_group">
                            <label>Customer:</label>
                            <select name="customer" class="form-control customer"   required="required">
                              @foreach($customers as $customer)
                              <option value="{{$customer->id}}">{{$customer->name}}</option>
                              @endforeach
                            </select>
                             <small id="emailHelp" class="form-text text-muted">This is required</small>
                        </div>
                    </div>

               </div>
<hr>
               <div class="col-md-12">
                  <div class="col-md-2">
                    <div class="form-group">
                            <label>Type:</label>
                           <select id="s_type" class="form-control" placeholder="s_type">
                             <option value="sale">Sale</option>
                             <option value="refund">Return</option>
                             <option value="damage">Damage</option>
                           </select>
                             <a id="emailHelp" data-toggle="modal" href="#addproductgrpoup"  class="form-text text-muted" accesskey="y">Add Product Group (ALT + Y)</a>
                        </div>
                  </div>
                   <div class="col-md-3">
                        <div class="form-group" id="product_form_group">
                            <label>Product:</label>
                           <select id="product" class="form-control product" placeholder="product"></select>
                             <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
                        </div>
                   </div>
                   <div class="col-md-3">
                        <div class="form-group" id="warehouse_form_group">
                            <label>Warehouse:</label>
                            <select id="warehouse" class="form-control warehouse"></select>
                             <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
                        </div>
                   </div>
                   <div class="col-md-2">
                        <div class="form-group" id="sale_form_group">
                            <label>Quantity:</label>
                            <input type="text" id="quantity" class="form-control textbox" placeholder="Quantity" onkeydown="enter(event, this)">
                            <small>Total Stock in Warehouse: <span id="display_limit"></span></small>
                            <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
                        </div>
                   </div>
                   <div class="col-md-2">
                        <div class="form-group" id="qty_form_group">
                            <label>Sale Price:</label>
                            <input type="text" class="form-control" @if(!is_allowed('allow-edit-sale-price')) readonly @endif placeholder="Sale Price" id="saleprice" value="0" onkeydown="keyDown(event,this)">
                            <small id="emailHelp" class="form-text text-muted">Press ENTER to ADD</small>
                        </div>
                   </div>
                   <!-- <div class="col-md-1">
                     <div class="form-group">
                     <label></label>
                       <button onclick="add()" class="btn btn-lg btn-primary">Add</button>
                     </div>
                   </div> -->
               </div>
               <?php
                $custom_explode = explode(',', session()->get('settings.sales.custom_items'));
                $decoded = json_decode($sale_order->custom_inputs);
                $merged_array =array();
                foreach ($custom_explode as $key => $value) {
                  $merged_array[$value] = '';
                }
                foreach ($decoded as $key => $value) {
                  if($value){
                  $merged_array[$key] = $value;
                  }
                }
               ?>
               @if (count($merged_array) > 0)
                   <div class="col-md-12">
                   @foreach ($merged_array as $key => $value)
                           <div class="col-md-2">
                               <div class="form-group">
                                   <label for="custom input">{{ ucwords(ltrim($key)) }}:</label>
                                   <input type="text" class="form-control" name="custom_field[]" value="{{ ($value)?ucwords(ltrim($value)):'' }}" id="">
                                   <input type="hidden" class="form-control" name="custom_labels[]" id=""
                                       value="{{ $key }}">
                               </div>
                           </div>
                       @endforeach
                   </div>

                @elseif(count($custom_explode) > 0)
                <div class="col-md-12">
                  @foreach ($custom_explode as $item)
                          <div class="col-md-2">
                              <div class="form-group">
                                  <label for="custom input">{{ ucwords(ltrim($item)) }}:</label>
                                  <input type="text" class="form-control" name="custom_field[]" id="">
                                  <input type="hidden" class="form-control" name="custom_labels[]" id=""
                                      value="{{ $item }}">
                              </div>
                          </div>
                      @endforeach
                  </div>

               @endif

                <div class="multiRowInput">
                 <table class="table table-bordered table-striped table-hover">
                  <thead>
                    <tr>
                    <th>Type</th>   
                      <th>Product</th>
                      <th>Warehouse</th>
                      <th>Quantity</th>
                      <th class="numeric">Sale Price</th>
                      @if(auth::user()->fixed_discount)
                    <th>Discounted Price</th>
                    @endif
                      <th>Sub Total</th>
                      <th>Option</th>
                    </tr>
                  </thead>
                    <tbody id="appendMe">

                      @foreach($sale_order->orders as $order)
                      <tr>

                        @php
                            //as we are making a sale return thing
                            //so we need to set sale product as refund product
                            //and delete refund product
                            if ($order->quantity < 0){
                                continue;//skip this row
                            }
                            $order->quantity *= -1;
                        @endphp
                        
                      <td>
                        @if($order->quantity > 0)
                        <input type="hidden" name="stype[]" value="sale"><span>sale</span>
                        @elseif($order->stocks && $order->quantity < 0)
                        <input type="hidden" name="stype[]" value="refund"><span>refund</span>
                        @else
                        <input type="hidden" name="stype[]" value="damage"><span>damage</span>
                        @endif
                      </td>
                      <td>
                        <input type="hidden" name="product[]" value="{{$order->product_id}}">
                        <span>
                          @php
                            $name = $order->product->name. " - ". $order->product->brand;
                            if (!empty(session()->get('settings.products.optional_items'))) {
				                      $fields = explode(",", session()->get('settings.products.optional_items'));
				                      foreach ($fields as $field) { 
					                      if(!empty($order->product->$field) && strpos(session()->get('settings.products.optional_items'), $field) !== false) {
						                      $name .= " " . (($field == category)?$order->product->$field->name:$order->product->$field);
					                      }
				                      }
			                      }
                          @endphp
                          {{$name}}
                        </span>
                      </td>

                      <td>
                        <input type="hidden" name="warehouse[]" value="{{$order->stocks->warehouse_id}}"><span>{!! ($order->stocks)?$order->stocks->warehouse->name:" - <small><i>Stock is not stored</i></small>" !!}</span>
                      </td>



                        <td><input type="text" name="quantity[]" class="form-control" value="{{$order->quantity}}"></td>

                        <td>
                          <input type="text" name="sale_price[]" class="form-control" onchange="minimum_sale_price(this)" placeholder="Sale Price" @if(env('SALEPRICE_EDITABLE') == 1) @else readonly @endif value="{{$order->salePrice}}">
                        </td>

                        @if(auth::user()->fixed_discount)
                        <td>
                          <input type="text" name="discounted_price[]" class="form-control" placeholder="Discounted Price" readonly value="{{$order->product->min_sale_price}}">
                        </td>
                        @endif
                          <td><span class="sum">{{$order->quantity*$order->salePrice}}</span></td>

                        <td><a data-id="'+product_id+'" data-qty="'+qty+'" data-wh="'+warehouse_id+'" style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td>
                      </tr>
                      @endforeach
                 
                      <tr class="parent_row">
                         
                      </tr>

                    </tbody>
                  </table>
                 
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">

                </div>
                 </div>
              <div class="content-panel">
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <p>
                      
                      <strong>Total Items: <span id="item_count">{{count($sale_order->orders)}}</span></strong>
                    </p>
                            <label>Notes:</label>
                           <textarea name="description" class="form-control">Refund of Invoice#{{$sale_order->id}} {{$sale_order->description}}</textarea>
                  </div>
                  @if(session()->get('settings.sales.is_bank_enable_in_direct_sale_invoice') == 1)
                      <div class="row">
                          <div class="col-xs-12">
                              <div class="form-group">
                                  <h4>Bank(optional):</h4>
                                  {!! Form::select('bank', App\BankAccount::GetBankDropDown(), !empty($transaction->bank) ? $transaction->bank : '', ['class' => 'form-control']) !!}
                              </div>
                          </div>
                      </div>
                  @endif

                  <div class="form-group">
                    <div id="payment_block">
                        <h4>Payment</h4>
                        <input type="text" class="form-control" name="payment" value="{{get_sale_invoice_payment_amount($sale_order->id)}}">
                      </div>
                  </div>
                  
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                            <label style="font-size: 16px">Sub Total:</label>&emsp;
                            <span style="font-size: 16px" class="sub_total">0</span>
                  </div>
                  <div class="form-group">
                            <label>Packaging & Shipping:</label>
                            <input class="form-control " id="shipping" autocomplete="off" onkeyup="adjustTotal()" type="text" name="shipping" value="{{$sale_order->shipping}}">
                  </div>
                  <div class="form-group">
                            <label>Discount(%):</label>
                            @php
                            $orignal_price = $sale_order->discount + $sale_order->total; 
                            $discounted_price = $sale_order->total;
                            $discount_percentage = 100 * ($orignal_price - $discounted_price) / $orignal_price; 
                            @endphp
                            <input class="form-control" id="discount_percent" @if(auth::user()->master_discount != 1) readonly @endif autocomplete="off" onkeyup="adjustDiscount(this);adjustTotal()" type="text" value="{{(number_format((float)$discount_percentage, 2, '.', ''))}}">
                  </div>
                  <div class="input-group">
                    <label>Discount(Fixed):</label>
                    <input class="form-control" placeholder="Discount in Fixed Price" id="discount" autocomplete="off" onkeyup="checkFixedDiscount(this);adjustTotal()"
                    type="text" name="discount" value="{{ $sale_order->discount }}" min="0" max="{{ auth::user()->allowed_discount_pkr }}">
                </div>

                   <div class="form-group">
                            <label style="font-size: 20px">Total:</label>&emsp;
                            <input type="hidden" name="total" value="0">
                            <span style="font-size: 20px" id="total">0</span>
                  </div>
                  <div class="form-group">
                            <label style="font-size: 16px">Previous Balance:</label>&emsp;
                            <span style="font-size: 16px" id="previous_balance">0</span>
                  </div>
                </div>
              </div>
                     
              </div>  
            </div>          
          </div>
          <div class="col-md-12" style="margin-top: 10px;margin-bottom: 10px;">
          <center>
            {!! get_invoice_submit_buttons("Sale", 'Return') !!}
            </center>
        </div>
        </div>
    </form>
    <input type="hidden" id="warehouse_limit">
@endsection
@section('scripts')
<script type="text/javascript" src="{{asset('assets/js/jquery.numeric.js')}}"></script>
  <!-- <script src="{{asset('assets/js/bootstrap-switch.js')}}"></script> -->
    <!-- <script type="text/javascript" src="{{url('productgroup.json')}}"></script> -->
    <script type="text/javascript" src="{{url('warehouse.json')}}"></script>

<script type="text/javascript">


  $(document).on("keyup","input[name^=quantity],input[name^=sale_price]",function(e){
    adjustTotal();
  });

    var products_json_d = [];
   function updateProducts()
  {
     $.getScript("{{asset('products.json')}}", function(){
      var hrm = '<label>Product:</label><select id="product" class="form-control product" placeholder="product"></select><br><a data-toggle="modal" href="#addproductmodal"  class="form-text text-muted"><small>Add Product</small></a><br><a href="javascript:void(0)" onclick="updateProducts()"><small>Update Products</small></a>';
      $("#product_form_group").html("");
      $("#product_form_group").append(hrm);
      choices('.product');
      $("#product").val("").trigger("change");//select none
    });
  }

  function minimum_sale_price(e) {
            var prod_id = $(e).parent().parent().children(":nth-child(2)").children().val();
            var prod_price = $(e).val();
            $.each(products_json_d, function(idss, objss) {
                if (objss.id == prod_id) {
                    if (objss.min_sale_price) {
                        if (parseFloat(prod_price) < parseFloat(objss.min_sale_price)) {
                            alertify.error('Minimum Product Price is ' + objss.min_sale_price).dismissOthers();
                            $(e).val(parseFloat(objss.min_sale_price));
                            // $("#saleprice").select();
                            // quit;
                            return false;
                        }
                    }
                }
            });
            adjustTotal();
        }


  $(document).ready(function(){
   updateProducts();
   adjustTotal();
   // $(".customer").select2("open");
   $(".customer").val("{{$sale_order->customer_id}}").trigger("change");
      set_user_balance($('.customer').val(), "#previous_balance");

   // $(".customer").select2("close");
  });
  var current_bill = [];
    function enter(e, me)
    {
      var keyCode = e.keyCode || e.which; 

      if (keyCode == 9 || keyCode == 13) { 
        e.preventDefault(); 
        $("#saleprice").select();
      }
    }

    function choices(identifier){
      // var warehouse_id = $("#warehouse_d").val();
          $(identifier).select2({
            placeholder: "Select a product",
            data:products_json_d,
            matcher: function (params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }

            keywords=(params.term).split(" ");

            for (var i = 0; i < keywords.length; i++) {
                if ((((data.text).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1) && (((data.barcode).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1) )  
                return null;
            }
            return data;
        },
            minimumInputLength: 0,
        });
              $(identifier).on('select2:close', function (e) {
               $(identifier).blur();
                $.ajax({
                  url: "/product_price_json/",
                  data: {'product_id': $(identifier).val(), 'customer_id': $('.customer').val()}
              }).done(function(data){
                    $("#saleprice").val(data);
                });

              // $("#warehouse").val(4); //selected by default, asked by shan
              // $("#warehouse").select2().trigger('change');
              // $("#warehouse").focus();
              $("#warehouse").select2('open').select2('close');
              // $("#warehouse").select2('open').select2('close')
        });

      }


      function warehouse(identifier){
      // var warehouse_id = $("#warehouse_d").val();
          $(identifier).select2({

      placeholder: "Select a warehouse",

            data: warehouse_d,
            // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
        });
          $(identifier).on('select2:close', function (e) {
            load_start();
            $.ajax({
                  url: "/warehouse_product_json/",
                  data: {'product_id': $("#product").val(), 'warehouse_id': $(identifier).val()}
              }).done(function(data){
                load_end();
                $("#warehouse_limit").val(data);
                $("#display_limit").html(data);
                    $("#qty").val(1);
                }).error(function(d){
                  load_end();
                  alertify.error('Some error occurred, try again').dismissOthers();
                  $(identifier).focus();
              });
          $("#quantity").select();
    });

      }


      choices('.product');
      warehouse('.warehouse');


      $(".customer").select2({
        placeholder: 'Select A customer',
             
            // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
        });

      $(".customer").on('select2:close', function (e) {
          $("#product").select2("open");
        $("#product").val("").trigger('change');
           set_user_balance($('.customer').val(), "#previous_balance");


    });


 $('#saleprice').numeric({ negative: false, decimalPlaces: 2 }, function() { alertify.error("No negative values").dismissOthers(); this.value = ""; this.focus(); });
 $("#shipping").numeric({ negative: false, decimalPlaces: 2 }, function() { alertify.error("No negative values").dismissOthers(); this.value = ""; this.focus(); });
  $("#discount").numeric({ negative: true, decimalPlaces: 2 }, function() { alertify.error("Non numeric values").dismissOthers(); this.value = ""; this.focus(); });
 $("#quantity").numeric({ decimal: false, negative: false }, function() { alertify.error("Positive integers only").dismissOthers(); this.value = ""; this.focus(); });
    function keyDown(e, me)
    {
      var keyCode = e.keyCode || e.which; 

      if (keyCode == 9 || keyCode == 13) { 
        e.preventDefault(); 


        var product_id = $("#product").val();
        var fixed_discount = $("#fixed_discount").val();
        var master_discount = $("#master_discount").val();
        var type= $("#s_type").val();

        

        var warehouse_id = $("#warehouse").val();

        var qty = $('#quantity').val();
        var discounted_price;


        if(type == "sale")
        {
          qty *= 1;
        }else{
          qty *= -1;
        }

        var saleprice = $('#saleprice').val();

        if (showError($('.customer').val(), "customer",".customer", "Please select Customer") || showError(product_id, "product", "#product", "Please select Product") || showError(warehouse_id, "warehouse", "#warehouse", "Please select Warehouse") || showError(qty, "qty","#quantity", "Please enter quantity") || showError(saleprice, "sale","#saleprice", "Please enter sale price") )
        {
          return;
        }
        $.each(products_json_d, function(idss, objss) {
        if (objss.id == product_id) {
          discounted_price = objss.min_sale_price;

          if (parseFloat(saleprice) < parseFloat(objss.min_sale_price)) {
            alertify.error('Minimum Product Price is '+ objss.min_sale_price).dismissOthers();
            $("#saleprice").select();
            quit;
          }
          return false;
        }
      });

        var current_stock  = +(calculateConsumedQuantity(product_id, warehouse_id, qty));

        if (current_stock > +( $("#warehouse_limit").val()) && 0==1) //disable it
        {
          var remaining = +($("#warehouse_limit").val());
          $("#qty_form_group").addClass('has-error');
          alertify.error('Maximum Product Quantity in warehouse is: '+ remaining).dismissOthers();
          //remove all entry
          calculateConsumedQuantity(product_id, warehouse_id, qty*-1);
          return;
        }

        if (qty == 0)
        {
          alertify.error("Please Add Some Quantity").dismissOthers();
          $("#quantity").select();
          return;
        }

        var product_text = $('#product').select2('data');
        var warehouse_text = $("#warehouse").select2('data');

        $("#product").val("").trigger('change');
        // $("#warehouse").val("").trigger('change');
        $("#quantity").val("");
        $("#saleprice").val("");
        $("#warehouse_limit").val(0);
        $("#display_limit").html("");
        is_read_only = "{{ !is_allowed('allow-edit-sale-price') }}" ? "readonly" : "";
        // is_read_only = ($("#saleprice_readonly").val() == "1") ? "" : "readonly";

        clearAllErrorClass();

        $("#product").select2('open');
        var v;
        if(fixed_discount == 1){
        v = '<td><input type="hidden" name="stype[]" value="'+type+'"><span>'+type+'</span></td><td><input type="hidden" name="product[]" value="'+product_id+'"><span>'+product_text[0].text+'</span></td><td><input type="hidden" name="warehouse[]" value="'+warehouse_id+'"><span>'+warehouse_text[0].text+'</span></td><td><input type="number" name="quantity[]" class="form-control" value="'+qty+'"></td><td><input type="number" min="0" step="0.01" '+is_read_only+' name="sale_price[]" class="form-control" placeholder="Sale Price" value="'+saleprice+'"></td><td><input type="number" min="0" step="0.01" name="discounted_price[]" class="form-control" placeholder="Discounted Price" value="'+discounted_price+'"></td><td><span class="sum">'+discounted_price*qty+'</span></td><td><a data-id="'+product_id+'" data-qty="'+qty+'" data-wh="'+warehouse_id+'" style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td>';
        }
        else{
        v = '<td><input type="hidden" name="stype[]" value="'+type+'"><span>'+type+'</span></td><td><input type="hidden" name="product[]" value="'+product_id+'"><span>'+product_text[0].text+'</span></td><td><input type="hidden" name="warehouse[]" value="'+warehouse_id+'"><span>'+warehouse_text[0].text+'</span></td><td><input type="number" name="quantity[]" class="form-control" value="'+qty+'"></td><td><input type="number" min="0" step="0.01" '+is_read_only+' name="sale_price[]" class="form-control" placeholder="Sale Price" value="'+saleprice+'"></td><td><span class="sum">'+saleprice*qty+'</span></td><td><a data-id="'+product_id+'" data-qty="'+qty+'" data-wh="'+warehouse_id+'" style="cursor: pointer;" onclick="clean(event, this)"><span class="fa fa-trash"></span>Clean</a></td>';
        }

        var xo = $("#appendMe").append("<tr class='parent_row'>"+v+"</tr>");
        adjustDiscount();
        adjustTotal();
      }
    }
   function adjustTotal()
    {
      updateItemCount();
      var fixed_discount = $("#fixed_discount").val();
      var master_discount = $("#master_discount").val();

      if(fixed_discount == 1){
        $("#appendMe input[name^=discounted_price]").each(function(index, data) {
        // debugger
        var quantity = $("#appendMe input[name^=quantity]").eq(index).val();
        $(data).parentsUntil("tbody").find(".sum").html(($(data).val() * quantity).toFixed(2));
        });
        }
      else{
        $("#appendMe input[name^=sale_price]").each(function(index, data){
          // debugger
          var quantity = $("#appendMe input[name^=quantity]").eq(index).val();
          $(data).parentsUntil("tbody").find(".sum").html(($(data).val() * quantity).toFixed(2));
        });
      }
        var sum = 0;
        $(".sum").each(function()
        {
          sum += +parseFloat($(this).html());
        });
        $(".sub_total").html(sum.toFixed(2)); 
        var ship =  +($("#shipping").val());
        var discount = +($("#discount").val());
        var total = ship  + sum - discount;
         $("#total").html(total.toFixed(2));
         $("input[name=total]").val(total.toFixed(2));
    }

    function calculateConsumedQuantity(product_id, warehouse_id, quantity)
    {

      //if new product
      if (current_bill[product_id] == undefined)
      {
        current_bill[product_id] = [];
        current_bill[product_id][warehouse_id] = +(quantity);
        return +(quantity);
      }

      //if from new warehouse
      if (current_bill[product_id][warehouse_id] == undefined)
      {
        current_bill[product_id][warehouse_id] = +(quantity);
        return +(quantity);
      }
      //old is gold
      current_bill[product_id][warehouse_id] += +(quantity);
      return current_bill[product_id][warehouse_id];

    }
    function clean(e, ele)
    {
      calculateConsumedQuantity($(ele).data('id'), $(ele).data('wh'), -1*$(ele).data('qty'));
      var row = $(ele).parent().parent();
      var boss = $("tbody#appendMe").children();
      if (boss.length < 2)
      {
            var xo = $("#appendMe").append("<tr class='parent_row'>"+$(".parent_row").html()+"</tr>");
            xo.children().last().children().find(".textbox").attr('onkeydown','keyDown(event, this)');
      }
      $(row).remove();
      adjustTotal();
      // debugger;
    }

        function isInt(n) {
        return (Math.floor(n) == n && $.isNumeric(n));
    }

    function showError(data, identifier, selector, message)
    {
      if ($.isNumeric(data))
      {
        return false;
      }
       $("#"+identifier+"_form_group").addClass('has-error');
       $(selector).focus();
       alertify.error(message).dismissOthers();
       return true;
    }

    function clearAllErrorClass()
    {
      $("div.form-group").removeClass('has-error');
    }
    function adjustDiscount(e) {
            let allowed_discount = parseInt($("#allowed_discount").val());
            $("#discount_percent").val(($("#discount_percent").val() < 0) ? 0 :($("#discount_percent").val() > allowed_discount) ? allowed_discount : $("#discount_percent").val());
            var sum = 0;
            $(".sum").each(function() {
                sum += +($(this).html());
            });
            var percent = $("#discount_percent").val() * sum / 100;
            console.log(percent);
            percent = Math.round(percent, 2);
            $("input[name=discount]").val(percent);
        }
        function checkFixedDiscount(e){
          if(parseFloat($(e).val()) > parseFloat($(e).attr('max'))){
            $(e).val($(e).attr('max'));
          }
        }
</script>
{!! hotkey_print_script() !!}
@include("product_groups.widget")
@include("products.modal")

@endsection