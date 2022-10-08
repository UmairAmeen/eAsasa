<!DOCTYPE html>
<html>
<head>
	<title>Invoice Print</title>
</head>
<body>
	<style type="text/css">
		*{
			font-size: 1em;
		}
		#xi{
			border: 1px solid black;
		}
		#xi thead{
			border-bottom: 1px solid black;
			background: #ccc
		}
		#xi tbody td{
			border-bottom: 1px dotted black;
		}
		#xi tfoot td{
			border-bottom: 2px solid black;
		}
		#copyright{
			font-size: 0.8em;
		}
	</style>

	<!-- Heading and Company Information -->
	<center>
		<h2>{{session()->get('settings.profile.company')}}</h2>
		<p>{{session()->get('settings.profile.phone')}}</p>
		<p>{{session()->get('settings.profile.address')}}</p>
	</center>
	<!-- Customer Information -->
	<!-- Invoice Heading -->
	<table width="100%" >
		<?php
		if($invoice->customer)
  {
  	$p = $invoice->customer;
  }else{
  	$p = $invoice->supplier;
  }
	$colspanValue=0;
	if(auth()->user()->fixed_discount == 1  && $invoice->type != 'purchase'){
		$colspanValue = 6;
	}
	else{
		$colspanValue = 5;
	}

		?>
		<tbody>
			<tr>
				<td><h3>{{$p->name}}</h3>{{$p->phone}}<br>{{$p->address}}{{$p->city}}</td>
				@if($invoice->type == "sale_order" && $invoice->sale_order->status == 3)
				<td align="right">Quotation #{{$invoice->id}}</td>
				@else
				<td align="right">{{studly_case($invoice->type)}} #{{$invoice->id}}</td>
				@endif
			</tr>
			<?php
			$decoded = json_decode($invoice->custom_inputs);
			?>
			@if (count($decoded) > 0)
				@foreach ($decoded as $key => $value)
					@if (!empty($value))
						<tr>
							<td colspan="4">{{ ucwords(ltrim($key)) }}: {{ ucwords($value) }}</td>
						</tr>
					@endif
				@endforeach
			@endif
			<tr>
				<td></td>
				<td align="right">{{date('M dS ,Y', strtotime($invoice->date))}}<br>{{date('h:i A', strtotime($invoice->created_at))}}</td>
			</tr>
			<tr>
				<td></td>
				<td align="right">
					@if ($invoice->bill_number)
	  				{{"Bill #".$invoice->bill_number}}
	  				@endif
	  			</td>
			</tr>
		</tbody>
	</table>
	<!-- Invoice Details -->
	<table id="xi" width="100%" align="center">
		<thead>
			<tr>
				<th>#</th>
				{{-- <th>Barcode</th> --}}
				<th colspan="2">Name</th>
				<th>Qty</th>
				<th>Price</th>
				@if(auth()->user()->fixed_discount == 1  && $invoice->type != 'purchase')
					<th>Discount</th>
				@endif
				<th>Total</th>
			</tr>
		</thead>
			
		<tbody align="center">
			<?php  $total = 0; $sp_discount = 0; $disc_sub_total=0; ?>
			@foreach ($invoice->orders as $key => $value)
				@php($name = $value->product->name. " ". $value->product->brand)
				@if (!empty(session()->get('settings.products.optional_items')))
					@php($fields = explode(",", session()->get('settings.products.optional_items')))
					@foreach ($fields as $field)
						@if(!empty($value->product->$field) && strpos(session()->get('settings.products.optional_items'), $field) !== false)
							@php($name .= " " . (($field == 'category')?$value->product->$field->name:$value->product->$field))
						@endif
					@endforeach
				@endif
			<tr>
				<td>{{$key + 1}}</td>
				{{-- <td>{{ $value->product->barcode }}</td> --}}
				<td colspan="2">{{$name}}</td>
				<td>{{($value->quantity) + 0}} {{session()->get('settings.sales.enable_units_invoice')?$value->product->unit->name:""}}</td>
				<p hidden>{{ $total_quantity = $total_quantity + $value->quantity}}</p>
				@if(auth()->user()->fixed_discount == 1  && $invoice->type != 'purchase')
				<td>{{($value->original_price) + 0}}</td>
				@php
				$simple = ($value->original_price - $value->salePrice)*$value->quantity; //this is discount
				//discount in percentage
				$discount_single = ($simple/($value->original_price*$value->quantity))*100;
					$sp_discount += $simple;
					$disc_sub_total+=$value->original_price*$value->quantity;
				@endphp
				<td>{{ ($discount_single)?number_format(
					$discount_single
					)."%":""}}</td>
				@else
				<td>{{($value->salePrice) + 0}}</td>
				@endif
				
				<td>{{ number_format($value->quantity*$value->salePrice,2) }}</td>


		<?php
			  $total += $value->quantity*$value->salePrice;
			  $discounted_total += $value->quantity*$value->product->min_sale_price;
			  ?>
		
			</tr>
		@endforeach
		<tr>
			<td colspan="{{ $colspanValue +1 }}"><b>Total Quantity: {{ $total_quantity }}</b></td>
		</tr>
		</tbody>
		<tfoot align="center">
			<tr>
				<td colspan="{{ $colspanValue +1 }}"></td>
			</tr>
			<tr>
				<td colspan="{{ $colspanValue}}">Sub Total</td>
				@if(auth()->user()->fixed_discount == 1  && $invoice->type != 'purchase')
				<td>{{ formating_price($disc_sub_total) }}</td>
					@if ($sp_discount)
					</tr>
					<tr>
						<td  colspan="{{ $colspanValue}}">Discount Applied</td>
						<td>{{ formating_price($sp_discount) }}</td>
					@endif
				@else
				<td>{{ formating_price($total) }}</td>
				@endif
			</tr>
			 @if($invoice->shipping && $invoice->shipping > 0)
	  			<tr>
					<td colspan="{{ $colspanValue}}">Others</td>
					<td>{{ formating_price($invoice->shipping) }}</td>
				</tr>
  			@endif
				@if($invoice->tax)
				<tr>
				<td colspan="{{ $colspanValue}}">Tax ({{ session()->get('settings.sales.tax_percentage') }}%)</td>
				<td>{{ formating_price($invoice->tax) }}</td>
			</tr>
			@endif

			{{-- <tr>
				<td colspan="{{ $colspanValue}}">Total</td>
				<td>{{ formating_price($total +$invoice->tax + $invoice->shipping) }}</td>
			</tr> --}}


  @if($invoice->discount && $invoice->discount > 0)
  				<tr>
					<td colspan="{{ $colspanValue}}">{{ ($sp_discount > 0 )?"Additional":"" }} Discount</td>
					<td>{{ formating_price($invoice->discount) }}</td>
				</tr>
  @endif
			<tr bgcolor="white" style="font-weight: bold; color: #000">
				<td colspan="{{ $colspanValue}}">Grand Total</td>
				@if(auth()->user()->fixed_discount == 1  && $invoice->type != 'purchase')
				<td>{{ formating_price($discounted_total + $invoice->shipping - $invoice->discount) }}</td>
				@else
				<td>{{ formating_price($total + $invoice->shipping - $invoice->discount) }}</td>
				@endif
			</tr>
			@if($invoice->customer)
			<?php
   	$pb = getCustomerBalance($invoice->customer_id);

   	$payment = payment_for_invoice($invoice->id);
   	?>
   	@if($payment)

	   @if(!empty($transaction_cash->amount))
				<tr>
					<td colspan="{{ $colspanValue}}">Payment (Cash)</td>
					<td>{{ formating_price($transaction_cash->amount) }}</td>
				</tr>
		@endif

	   @if(!empty($transaction_card->amount))
		   <tr>
			   <td colspan="{{ $colspanValue}}"> Payment ( {{ $transaction_card->payment_type }} ) </td>
			   <td> {{ formating_price($transaction_card->amount) }} </td>
		   </tr>
		@endif

		{{-- <tr>
			<td colspan="{{ $colspanValue}}">Payment</td>
			<td>{{ formating_price($payment) }}</td>
		</tr> --}}
   	@endif
   	@if (env('PREVIOUS_BALANCE_IN_INVOICE',true))
   	
   	<tr  style="font-weight: bold; color: #000">
				<td colspan="{{ $colspanValue}}">Previous Balance</td>
				@if(auth()->user()->fixed_discount == 1  && $invoice->type != 'purchase')
				<td>{{ formating_price($pb - ($discounted_total + $invoice->shipping - $invoice->discount) + $payment) }}</td>
				@else
				<td>{{ formating_price($pb - ($total + $invoice->shipping - $invoice->discount) + $payment) }}</td>
				@endif
			</tr>
			<tr style="font-size: 20px; font-weight: bold">
				<td colspan="{{ $colspanValue}}">Total Due</td>
				<td>{{ formating_price($pb) }}</td>
			</tr>
			@endif
   	  @endif
		</tfoot>
	</table>
	<!-- Invoice Footer -->
	<center>
		<br><br>
		@if($invoice->fbr_invoice)
		<p>FBR Invoice number: &nbsp;{{$invoice->fbr_invoice}}</p>
		@endif
		<p>
			{{$invoice->description}}
		</p>
		@if(session("settings.fbr.is_fbr_enable"))
		<br>
		<img src="{{ asset('images/fbr_logo.png')}}" width="100" height="100" alt="">
		@endif
		@if(session()->get('settings.sales.is_invoice_qr_enable'))
		 &nbsp; &nbsp; &nbsp;
	  		{!! \DNS2D::getBarcodeSVG($invoice->fbr_invoice, "QRCODE") !!}
  		@endif
		  <br>

  		{!! session()->get('settings.misc.invoice_footer') !!}<br>
  		<span id="copyright">Software by eAsasa (+92 345 4777487)</span>
	</center>

</body>
<script type="text/javascript">
	window.print();
</script>
</html>
