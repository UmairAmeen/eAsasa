<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="Bootstrap-ecommerce by Vosidiy">
<title>eAsasa POS</title>
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/logos/squanchy.jpg')}}" >
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/logos/squanchy.jpg')}}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/logos/squanchy.jpg')}}">
<!-- jQuery -->
<!-- Bootstrap4 files-->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" type="text/css"/> 
<!-- <link href="assets/css/ui.css" rel="stylesheet" type="text/css"/> -->
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/select2.min.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('assets/css/custom.css')}}">

<link href="{{asset('assets/fonts/fontawesome/css/fontawesome-all.min.css')}}" type="text/css" rel="stylesheet">
<link href="{{asset('assets/css/OverlayScrollbars.css')}}" type="text/css" rel="stylesheet"/>
<!-- Font awesome 5 -->

<!-- custom style -->
</head>
<body>
	<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="exampleModalLabel">
<div class="modal-dialog modal-dialog-centered  modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Complete SALE Order</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


	  <div class="modal-body">

		<div class="col-12">
			<div id="log"></div>
			<div class="row">
				<div class="col-6">
				  <h4 class="modal-h4"> Items </h4>

					<table class="table table-bordered">
					  <thead>
						  <tr>
							  <th>#</th>
							  <th>Item</th>
							  <th>Qty</th>
							  <th>Price</th>
							  <th>Total</th>
						  </tr>
					  </thead>
					  <tbody id="final_sale">
						  
					  </tbody>
					  <tfoot id="final_foot"></tfoot>
				  </table>
				</div>
				<div class="col-6" id="payment_details">
				  <h4 class="modal-h4"> Payment Details</h4>


				  <table class="table table-bordered">
					  <tr>
						  <th colspan="2"> Bill Number </th>
					  </tr>
					  <tr>
						  <td colspan="2"> <input id="bill_number" class="form-control" name="bill_number" placeholder="Bill Number (if any)"> </td>
					  </tr>

					  <tr>
						  <th colspan="2"> Payment Type </th>
					  </tr>

					  <tr>
						  <td> <select class="form-control" id="payment_type">
							  <option value="cash">Cash</option>
							  <option value="card"> Credit / Debit Card </option>
							  <option value="cheque">Cheque</option>
							  <option value="online">Online</option>
						  </select> </td>
						  <td> <input id="amount_paid" onkeyup="difference_calculator()" class="form-control" name="payment" placeholder="Amount Paid"> </td>
					  </tr>

					  <tr>
						  <td> <select class="form-control" id="payment_type_2">
							  <option value="card"> Credit / Debit Card </option>
							  <option value="cash">Cash</option>
							  <option value="cheque">Cheque </option>
							  <option value="online">Online</option>
						  </select> </td>
						  <td> <input id="amount_paid_2" onkeyup="difference_calculator()" class="form-control" value="0" name="payment_creditcard" placeholder="Amount Paid"> </td>
					  </tr>

					  <tr>
						  <th colspan="2"> Amount Remaining </th>
					  </tr>

					  <tr>
						  <td colspan="2"> <input id="remaining" class="form-control" value="0" readonly="readonly"></td>
					  </tr>

					  <tr>
						  <th colspan="2"> Note </th>
					  </tr>

					  <tr>
						  <td colspan="2"> <textarea class="form-control" id="description" placeholder="Invoice Note/description"></textarea> </td>
					  </tr>

				  </table>

				</div>
			</div>
		</div>
	  
	</div>


      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
        <button type="button" class="btn btn-primary" onclick="complete_sale('sm')">Complete Sale Order &amp; Print Small</button>
        <button type="button" class="btn btn-primary" onclick="complete_sale('lg')">Complete Sale Order &amp; Print Lg</button>
        <button type="button" class="btn btn-primary" onclick="complete_sale('')">Complete Sale Order &amp; Close</button>
      </div>
    </div>
  </div>
</div>

<!-- ========================= SECTION CONTENT ========================= -->
<section class="section-content padding-y-sm bg-default ">
<div class="container-fluid">
<div class="row">
	<div class="col-md-6 row top-bg-left">

		<div class="brand-wrap">
			<h2 class="logo-text pos-text"> &nbsp; POINT OF SALE - POS  
				
				<a href="{{ url('/') }}"> <i class="fa fa-home backhomeico" title="Back to Dashboard"> </i> </a>
				<i class="fa fa-search searchico" title="Search Product"> </i> 
			</h2>
		</div> <!-- brand-wrap.// -->


		<div class="col-md-12">

			<input type="text" class="barcode" name="barcode_search" id="barcode_search" placeholder="Barcode Scanning..." >

            <div class="inputsearch_area" >
				<div class="input-group mb-3 searcharea" style="display:none;margin-top:5px">
					<input type="text" accesskey="a" id="search" name="search" placeholder="FIND... [ALT+W]" autocomplete="off" class="form-control searchrelated_input" onkeyup="search(this)">
					<div class="input-group-append">
						<a href="javascript:void(0)" onclick="updateProducts()" class="btn btn-default relatedproduct_btn"> <i class="fa fa-redo-alt"></i> &nbsp; Reload Products</a>
					</div>
				</div>
			</div>	
			
		</div> 
		{{-- <div class="col-md-4">
			<!-- <a href="javascript:void(0)" onclick="toggle()" class="btn btn-primary btn-sm m-btn m-btn--icon m-btn--icon-only"><i class="fa fa-eye"></i>Toggle OS Products</a> -->
			<a href="javascript:void(0)" onclick="updateProducts()" class="btn btn-primary  btn-sm m-btn m-btn--icon m-btn--icon-only"><i class="fa fa-redo-alt"></i>Reload Products</a>
		</div> --}}
	</div>

	<div class="col-md-6 top-bg-right">
		<div class="row">
			<div class="col-md-6 topsaleicons customerico_btn whiteclass">
				<i class="fa fa-user whiteclass"> </i> <br>
				<span> CUSTOMERS  </span> <br><br>
			</div>

			<div class="col-md-6 topsaleicons orderico_btn">
				<i class="fa fa-clipboard"> </i> <br>
				<span> ORDERS  </span> <br><br>
			</div>
		</div>
	</div>


	<div class="col-12 row">
		
<div class="col-md-6 padding-y-sm card pos_leftcard">

	<div class="row topleft_heading" id="catalog_heading" >
		<center><h3 id="selectproceeding" class="pos-text-mid">Select Customer before Proceeding</h3></center>
	</div>

	<div class="row" id="catalog">

	</div>
</div>


<div class="col-md-6">
<div class="">


	<div class="customerarea" >
		<!-- Show Customer Info Here -->
<div class="row">
 <div class="col-12 rightsec_saletop">
	 <div class="btn-group" style="width:100%">
		 <a data-type="sale" style="width:50%" class="btn btn-default salesbtn pos_type" href="{{url('pos/direct')}}"> SALE </a>
		 <a href="#" type="button" style="width:50%" class="btn btn-default salesbtn" data-type="sale_order"> SALE ORDER / QUOTATION </a>
		 {{-- <a type="button" class="btn btn-default salesbtn" href="{{url('pos/purchase')}}"> PURCHASE </a> --}}
	 </div> 
	 {{-- <a class="btn btn-outline-success btn-sm pos_type" data-type="sale" href="{{url('pos/direct')}}">Sale</a>
	 <button class="btn btn-success btn-sm pos_type" data-type="sale_order">Sale Order</button>
	 <button class="btn btn-outline-success btn-sm pos_type" data-type="quotation">Quotation</button>
	 <a class="btn btn-outline-success btn-sm pos_type" href="{{url('pos/purchase')}}" data-type="purchase">Purchase</a> --}}
 </div>
 <div class="col-12 rightsec_sale"  >
	 <label>Date</label>
	 <input type="date" class="saledate" id="date" value="{{date('Y-m-d')}}">
			 <small class="text-muted">ALT+I</small>
<br>
	 <label>Customer</label>
	 <select onchange="updateProducts()" id="customer_select" name="customer_id" class="sale_dropdown">
		 <option value=""> - Select Customer -  </option>
		 @foreach($customers as $customer)
		 <option value="{{$customer->id}}">{{$customer->name}} [<small>{{$customer->city}}</small>]</option>
		 @endforeach
	 </select>
	 <small class="text-muted">ALT+C</small>

 </div>
 <hr>
</div>
</div>

   <div class="orderarea" style="display:none">
	   <center> <img class="add_item_img" src="{{ asset('assets/img/add-item-icon.png') }}"> </center>
	   <div class="order_details_area" style="display:none">

			<span id="cart" class="pos_table">
				<table class="table table-hover  table-bordered shopping-cart-wrap">
				<thead class="text-muted">
				<tr>
				<th scope="col"> Actions </th>
				<th scope="col">Item</th>
				<th scope="col" width="120">Qty</th>
				<th scope="col" width="120">Price</th>
				<th scope="col" width="120">Total</th>
				</tr>
				</thead>
				<tbody id="cartTable">
			
				</tbody>
				</table>
			</span>
   		</div>
	</div>

   
   		
	<div class="box">
		<div class="pos_table">
			<table class="table">
				<tr>
					<td> Discount </td>
					<td> <input type="number" onkeyup="updateTotals()" class="form-control" min="0" step="0.01" id="discount" name="discount" value="0.00">  </td>
					<td> Sub Total </td>
					<td class="text-right" id="subTotal"> 0  </td>
					
				</tr>

				<tr>
					<td> Shipping & Packing:  </td>
					<td width="25%"> <input type="number" onkeyup="updateTotals()" class="form-control" min="0" step="0.01" id="shipping" name="shipping" value="0.00">  </td>
					<td> Grand Total </td>
					<td width="30%" > <dd class="text-right h4 b" id="total"> 0 </dd> </td>
				</tr>

				<tr>
					<td colspan="4"> 
						<a href="javascript:void(0)" onclick="finishSale()"> <div class="bottomicons printbtn"> <i class="fa fa-print"> </i> <p class="p-print"> Print </p> </div> </a> 
						<a href="javascript:void(0)">  <div class="bottomicons"> <i class="fa fa-bell-slash "> </i> <p class="no-sale"> No Sale </p> </div>  </a> 
					</td>
				</tr>

				<tr>
					<td> <a href="{{url('pos')}}" class="btn btn-default btn-error btn-lg btn-block btn_sale_cancel"><i class="fa fa-times-circle "></i> Cancel  </a> </td>
					<td colspan="2"> <a href="javascript:void(0)" onclick="finishSale()" class="btn btn-lg btn-block btn_sale_complete"><i class="fa fa-shopping-bag"> </i> Complete <br> <small> shift+s </small> </a> </td>
					<td> <a href="javascript:void(0)" onclick="finishSale()"  class="btn btn-default btn-error btn-lg btn-block btn_payment"><i class="fa fa-check"></i> Pay  </a> </td>
				</tr>
				
			</table>
		</div>
	</div>




{{-- <dl class="dlist-align">
  <dt> Sub Total: </dt>
  <dd class="text-right" id="subTotal">0</dd>
</dl>
<dl class="dlist-align">
  <dt>Discount:</dt>
  <dd class="text-right">
  	<input type="number" onkeyup="updateTotals()" class="form-control" min="0" step="0.01" id="discount" name="discount" value="0.00">
  </dd>
</dl>
<dl class="dlist-align">
  <dt>Shipping &amp; Packing: </dt>
  	<input type="number" onkeyup="updateTotals()" class="form-control" min="0" step="0.01" id="shipping" name="shipping" value="0.00">
</dl>
<dl class="dlist-align">
  <dt>Total: </dt>
  <dd class="text-right h4 b" id="total"> 0</dd>
</dl> --}}

</div> <!-- box.// -->
	</div>
</div>
</div><!-- container //  -->
</section>

<form id="my_form" action="{{ route('sale_orders.store') }}">
	<!-- combine all -->
</form>

<style type="text/css">
	.selected{
		border: 2px solid blue;
	}
</style>


<!-- ========================= SECTION CONTENT END// ========================= -->
<script src="{{asset('assets/js/jquery-2.0.0.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/js/OverlayScrollbars.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/js/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/js/hotkey.min.js')}}" type="text/javascript"></script>
<script type="text/javascript">


	$(document).ready(function(){

		$("#barcode_search").focus();


		$(".searchico").click(function(){
			$('.searcharea').toggle(100);
		});

		$(".customerico_btn").click(function(){
			$('.customerico_btn, .fa-user').addClass('whiteclass');
			$('.orderico_btn, .fa-clipboard').removeClass('whiteclass');
			$('.customerarea').show();
			$('.orderarea').hide();
		});

		$(".orderico_btn").click(function(){
			$('.orderico_btn, .fa-clipboard').addClass('whiteclass');
			$('.customerico_btn, .fa-user').removeClass('whiteclass');
			$('.orderarea').show();
			$('.customerarea').hide();
		});
		
	});


	var cart = [];
	var products = [];
	var quotation = false;


	$(".pos_type").click(function(e)
	{
		quotation = false;
		$("[data-type='quotation']").addClass("btn-outline-success").removeClass('btn-success');
		$("[data-type='sale_order']").addClass("btn-outline-success").removeClass('btn-success');
		$("#payment_details").show();
		if ($(this).data('type') == "quotation")
		{
			quotation = true;
			$(this).removeClass("btn-outline-success").addClass('btn-success');
			$("#payment_details").hide();
		}
	});

	function iterate(direction = 1)
	{
		var found = false;

		var totalSize = $(".product").length;
		$.each($(".product"), function (index, data){
			if ($(data).hasClass("selected"))
			{
				found = index;
			}
		});
		// debugger;
		if (found === false){
			$(".product").first().addClass("selected");
		}else{
			$(".product").eq(found).removeClass("selected");

			if (direction > 0)
			{
				if ((found + 1) >= totalSize)
				{
					found = -1;
				}
				$(".product").eq(found+1).addClass("selected");
			}else{

				if (found == 0)
				{
					found = totalSize;
				}
				$(".product").eq(found-1).addClass("selected");
			}
			
		}
	}

	function selectTheProduct()
	{
		if ($(".selected").length > 0)
		{
			$(".selected a").click();
		}
	}



	$(function() {
		// select POS 
		// select Customer
		$("#customer_select").val("");
		$.when(
		    $("#customer_select").select2()
		).done(function(){
		     $("#customer_select").select2('open');
		});
		$("#customer_select").focus();


		$(window).bind('keydown', 'alt+w', function(){$("#search").select();$(".selected").removeClass("selected");});
		$("#search").bind('keydown', 'return', function(){iterate()});

		$(window).bind('keydown', 'alt+i', function(){$("#date").select();$(".selected").removeClass("selected");});
		$(window).bind('keydown', 'alt+c', function(){$("#customer_select").select2('open');$(".selected").removeClass("selected");});
		$(document).bind('keydown', 'right', function(){iterate()});
		$(document).bind('keydown', 'left', function(){iterate(-1)});
		$(document).bind('keydown', 'return', function(){selectTheProduct()});
		$(document).bind("keydown",'shift+s',function(){finishSale()});
		// $(document).bind('keydown', 'tab', function(){$("#date").select();});
		//render products based on customer pricing
		// updateProducts();
	});





	function updateProducts()
	{
		$("#search").val("");
		$("#catalog").html("<center><h3 class='pos-text-mid'>Loading Products</h3></center>");
		var customer_id = $("#customer_select").val();
		$.getScript("{{asset('products_full.json')}}?customer_id="+customer_id, function(){
    		renderProducts();
		});
		
	}

	function search(element)
	{
	    var value = $(element).val().toLowerCase();
	    // $("#catalog .product").filter(function() {
	    //   $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
	    // });

	    renderProducts(24, 1,value);
	}

	function selectProduct(id)
	{
		$(products_json_d).each(function(index,element){
			if (element.id == id || element.barcode == id)
			{				
				syncCart(id,'add', element);
			}
		});

		$('.orderarea').show();
		$('.customerarea').hide();
		$('.customerico_btn, .fa-user').removeClass('whiteclass');
		$('.orderico_btn, .fa-clipboard').addClass('whiteclass');
	}
		$("#barcode_search").change(function(){
			var id = $("#barcode_search").val();
			selectProduct(id);

			$("#barcode_search").val('');
		});

	function selectPOS()
	{

	}



	var display_os = false; //display out of stock
	
	function toggle()
	{
		display_os = !display_os;
		renderProducts();
	}


	function renderProducts(count=24, current_page=1,search="")
	{
		var template = "";
		var cc = 0; //temp
		var search_count = 0;
		search = $.trim(search);

		$(products_json_d).each(function(index,element){
			mix_tea = element.name+" "+element.brand+" "+element.barcode;
			if ((element.name && element.name.toLowerCase().indexOf(search) > -1) || (element.brand && element.brand.toLowerCase().indexOf(search) > -1) || (element.urdu && element.urdu.toLowerCase().indexOf(search) > -1) || (element.barcode && element.barcode.toLowerCase().indexOf(search) > -1) || mix_tea.toLowerCase().indexOf(search) > -1)
			{
				//hide out of stock products
				// if (!display_os && element.stock < 0.01)
				// {
				// 	//skip the product
				// 	return true;
				// }
				unlocked = false;
				if ((count * (current_page-1)) <= search_count)
				{
					unlocked = true;
				}
				// if ()
				if (unlocked && ++cc <= count)
				{

					if(element.stock<=element.notify) {
						var notifystock = '<span class="badge redcircle"> '+parseInt(element.stock)+' </span>';
					} else {
						var notifystock = '<span class="badge greencircle"> '+parseInt(element.stock)+' </span>';
					}

					// <b>Stock: </b>'+element.stock+" ("+element.unit+')
					template += '<div class="col-md-4">\
					<div class="productshow"  onclick="selectProduct('+element.id+')">\
						<h5 class="product_heading">'+$.trim(element.name)+''+$.trim(element.urdu)+'\</h5>\
						<p>'+$.trim(element.brand)+'</p>\
						<p class="price-p">'+element.price+' '+notifystock+' </p>\
						</div>\
				    </div>';
				}
				search_count++;
			}
			// $("#barcode_search").focus();
			$("#selectproceeding").html("<h3> Choose Items </h3>");
		});


		// pagination start
		if (search_count > 1)
		{
			var total_pages = parseInt(search_count/count);
			if (search_count % count > 0)
			{
				total_pages++;
			}
			if (total_pages > 1)
			{
					template += '<nav aria-label="...">\
			    <ul class="pagination justify-content-center">';

			  if (current_page > 1)
			    {
			    	template+='<li class="page-item">\
			    			      <a href="javascript:void(0)" class="page-link" onclick="renderProducts('+count+', '+(current_page-1)+',\''+search+' \' )" tabindex="-1">Previous</a>\
			    			    </li>';
			   	}else{
			   		template+='<li class="page-item disabled">\
			    			      <a href="javascript:void(0)" class="page-link" tabindex="-1">Previous</a>\
			    			    </li>';
			   	}

			   	var my_round = 0;

			   	for (i=1; i<total_pages; i++)
			   	{
			   		if (i > current_page - 4 && i < current_page + 5)
			   		{

				   		if (current_page == i)
				   		{
				   			template += '<li class="page-item active">';
				   		}else{
				   			template += '<li class="page-item">';
				   		}

				   		template += '<a href="javascript:void(0)" class="page-link" onclick="renderProducts('+count+', '+i+',\''+search+' \')" >'+i+'</a></li>';
				   		my_round=i;
			   		}

			   	}
			   	if (my_round < total_pages-1)
			   	{
			   		template += '<li class="page-item disabled"><a href="javascript:void(0)" class="page-link" >...</a></li>';
			   	}

			   	if (current_page == total_pages)
			   		{
			   			template += '<li class="page-item active">';
			   		}else{
			   			template += '<li class="page-item">';
			   		}
			   	template += '<a X="Y" href="javascript:void(0)" class="page-link" onclick="renderProducts('+count+', '+total_pages+',\''+search+' \')" >'+total_pages+'</a></li>';


			    if (total_pages > current_page)
			    {
			    	template+='<li class="page-item">\
			    			      <a href="javascript:void(0)" class="page-link" onclick="renderProducts('+count+', '+(current_page+1)+',\''+search+' \')"  tabindex="-1">Next</a>\
			    			    </li>';
			   	}else{
			   		template+='<li class="page-item disabled">\
			    			      <a href="javascript:void(0)" class="page-link"  tabindex="-1">Next</a>\
			    			    </li>';
			   	}
			   template += '</ul></nav>';
			}//end page > 1
		}
		// pagination end
		$("#catalog").html(template);
	}


	function syncCart(id, type, value)
	{
		var removeIndex = -1;
		var added = false;
		var total = 0;
		$(cart).each(function(index, element){
			if (element.id == id || element.barcode == id)
			{
				if (type=="quantity")
				{
					// if (parseFloat($(value).val()) > element.stock)
					// {
					// 	alert("Out of Stock");
					// }else{

						cart[index].quantity = parseFloat($(value).val());
					// }
				}

				if (type == "add")
				{
					added = true;
					// if (cart[index].quantity >= element.stock)
					// {
					// 	alert("Out of Stock");
					// }else{

						cart[index].quantity +=1;
					// }
				}

				if (type == "remove")
				{
					cart[index].quantity -=1;
				}

				if (type=="price")
				{
					cart[index].price = parseFloat($(value).val());
				}

				if (type=="removeFull")
				{
					removeIndex = index;
				}

				if (element.quantity < 1)
				{
					removeIndex = index;
				}
			}

			cart[index].total = cart[index].quantity * cart[index].price;

			total += element.total;

		});

		if (removeIndex > -1)
		{
			cart.splice(removeIndex, 1);
		}

		if (added==false && type == "add")
		{
			value.quantity = 1;
			// if (value.quantity > value.stock)
			// {
			// 	alert("Out of Stock");
			// }else{

				//id, name, brand, quantity, price, barcode
				value.total = value.quantity * value.price;
				total+= value.total;
				cart.push(value);
			// }
		}
		// $("#subTotal").html(total);
		// var discount = $("#discount").val();
		// var shipping = $("#shipping").val();

		// $("#total").html(total + shipping - discount);
		updateTotals();
		renderCart();
	}

	function updateTotals(){
		var total = 0;
		$(cart).each(function(index, element){
			total += element.total;
		});
		$("#subTotal").html(total);
		var discount = parseFloat($("#discount").val());
		var shipping = parseFloat($("#shipping").val());

		var final = total + shipping - discount;

		$("#total").html(final.toFixed(2));

		if(final<1) {
			$(".add_item_img").show();
		} else {
			$(".add_item_img").hide();
			$(".order_details_area").show();
		}
	}


	function renderCart(){
		var cart_content = "";
		$(cart).each(function(index, element){
			//we would have id, name, brand, quantity, price, barcode
			cart_content +='\
			<tr>\
				<td>\
				<a class="btn btn-danger btnremovecart" onclick="syncCart('+element.id+',\'removeFull\',this)" ><small><i class="fa fa-trash"></i></small></a>\ </td>\
				<td>\ <input type="hidden" name="product_id[]" value="'+element.id+'">\
				<h6>'+element.name+'</h6>\
					<small class="text-truncate">'+element.brand+'</small>\
				</td>\
				<td class="text-center"> \
					<div class="m-btn-group m-btn-group--pill btn-group mr-2" role="group" aria-label="...">\
																				\
					<input class="form-control" type="text" onchange="syncCart('+element.id+',\'quantity\',this)" name="quantity[]" min="1" step="1" value="'+element.quantity+'">\
																				</div>\
				</td>\
				<td> \
					<div class="price-wrap"> \
						<var class="price"><input type="text" class="form-control" style="width:150px"  onfocusout="syncCart('+element.id+',\'price\',this)"  name="price[]" value="'+element.price+'"></var> \
					</div> <!-- price-wrap .// -->\
				</td>\
				<td class="text-right"> \
				<h6>'+element.total+'</h6>\
				</td>\
			</tr>';
		});


		$("#cartTable").html(cart_content);
		
	}

	function finishSale(){
		if (cart.length < 1)
		{
			alert("Please Add Some item in cart");
			return false;
		}
		$('#exampleModal').modal('show');
		var ht = "";
		var cc = 1;
		var subTotal = 0;
		$(cart).each(function(index, element){
			ht += '<tr>\
        			<td>'+(cc++)+'</td>\
        			<td>'+element.name+'<br><small class="text-truncate">'+element.brand+'</small></td>\
        			<td>'+element.quantity+'</td>\
        			<td>'+element.price+'</td>\
        			<td>'+element.quantity*element.price+'</td>\
        		</tr>';
        	subTotal += element.quantity*element.price;
		});

		var shipping = parseFloat($("#shipping").val());
		var discount = parseFloat($("#discount").val());

		ft = "<tr>\
		<td colspan='3'>Sub Total</td>\
		<td colspan='2'>"+subTotal+"</td>\
		</tr>"

		ft += "<tr>\
		<td colspan='3'>Discount</td>\
		<td colspan='2'>"+discount+"</td>\
		</tr>"

		ft += "<tr>\
		<td colspan='3'>Packing and Shipping</td>\
		<td colspan='2'>"+shipping+"</td>\
		</tr>"

		ft += "<tr>\
		<td colspan='3'><h3>Grand Total</h3></td>\
		<td colspan='2'>"+(subTotal+shipping-discount)+"</td>\
		</tr>";
		
		$("#amount_paid").val(subTotal+shipping-discount);
		$("#amount_paid").data('cart',subTotal+shipping-discount);
		$("#final_sale").html(ht);
		$("#final_foot").html(ft);
		
	}

	function difference_calculator()
	{
		$("#remaining").val( $("#amount_paid").data('cart') - $("#amount_paid").val() - $("#amount_paid_2").val()  );
	}


	function complete_sale(print_type="sm")
	{
		$("#my_form").html("");
		var ht = "";
		$(cart).each(function(index, element){
			ht += '<input type="hidden" name="product[]" value="'+element.id+'">\
        			<input type="hidden" name="quantity[]" value="'+element.quantity+'">\
        			<input type="hidden" name="sale_price[]" value="'+element.price+'">\
        			<input type="hidden" name="stype[]" value="sale">';
		});
		ht+='<input type="hidden" name="date" value="'+$("#date").val()+'">';
		ht+='<input type="hidden" name="customer" value="'+$("#customer_select").val()+'">';
		ht+='<input type="hidden" name="discount" value="'+$("#discount").val()+'">';
		ht+='<input type="hidden" name="shipping" value="'+$("#shipping").val()+'">';
		ht+='<input type="hidden" name="description" value="'+$("#description").val()+'">';
		ht+='<input type="hidden" name="total" value="'+parseFloat($("#subTotal").html())+'">';
		ht+='<input type="hidden" name="print" value="'+print_type+'">';
		if (quotation)
		{
			ht+='<input type="hidden" name="status" value="3">';
			ht+='<input type="hidden" name="post_order" value="0">';
		}else{

			ht+='<input type="hidden" name="bill_number" value="'+$("#bill_number").val()+'">';
			ht+='<input type="hidden" name="payment_type" value="'+$("#payment_type").val()+'">';
			ht+='<input type="hidden" name="payment_type_2" value="'+$("#payment_type_2").val()+'">';
			ht+='<input type="hidden" name="payment" value="'+$("#amount_paid").val()+'">';
			ht+='<input type="hidden" name="payment_creditcard" value="'+$("#amount_paid_2").val()+'">';
			ht+='<input type="hidden" name="status" value="1">';
			ht+='<input type="hidden" name="post_order" value="1">';
		}

		$("#my_form").html(ht);
		$("#my_form").submit();
		// console.log($("#my_form").serialize());
	}


	var form;
	var old_request = null;

	            $(document).on("submit", "form",function(e){
        form = $(this);
            if($(form).hasClass('no-ajax'))
            {
                return;
            }
            //disable submit button
        $("#submit_btn").addClass('disabled');

        e.preventDefault();
        // load_start();
        try{

        old_request.abort();
        
      }catch(e)
      {
        console.log(e);
        //nothing to do
      }
        old_request = $.ajax({
          url: $(this).attr('action'),
          data: $(this).serialize(),
          method: "POST",
          dataType: "json"
        }).done(function(e) {
          // load_end();
          $("#log").removeClass();
          $("#submit_btn").removeClass('disabled');
          $("#log").addClass( "alert alert-success" );
          $("#log").html(e.message);
          if (e.action)
          {
            setTimeout(function(){callback(e.action, e.do);}, 100);
          }
        }).error(function(e){
            // load_end();
            $("#log").removeClass();
            $("#submit_btn").removeClass('disabled');
            $("#log").addClass( "alert alert-danger" );
            IS_JSON = false;
            var obj = "";
            try
            {
               var obj = $.parseJSON(e.responseText);
               IS_JSON = true;
            }
            catch(err)
            {
               IS_JSON = false;
            }  

           if (IS_JSON)
           {
              var errhtml = "";
              // var obj = jQuery.parseJSON(e.responseText);
              $.each(obj, function(key,value) {
                // alert(value.com);
                errhtml = errhtml + value + "<br>";
              });
              $("#log").html(errhtml);
           }else
           try{
            if (e.responseJSON.message)
            {
                $("#log").html(e.responseJSON.message);
            }else{
                $("#log").html("Some Error Occured, Try Again");
            }
            }catch(err)
            {
                  $("#log").html("Some Error Occured, Try Again or Call our helpline");
            }
        });
    });
        function callback(action, data)
        {
            switch(action)
            {
                case 'redirect':
                    window.location = data;
                    return;
                case 'reload':
                  window.location.reload();
                return;
                case 'update':
                  // $(data).dataTable('refresh');

                  var table = $(data).DataTable();
                  table.ajax.reload();

                  $("[role=dialog]").modal('hide');
                  return;
            }
        }
</script>
</body>
</html>