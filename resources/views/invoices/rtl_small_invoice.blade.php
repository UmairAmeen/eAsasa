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
		tbody td{
			text-align: right;
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

	if(env('SHOES_COMPANY') == 1){$colspanValue = 8;}
	else{$colspanValue = 6;}
		?>
		<tbody >
			<tr>
				<td style="text-align: left"><h3>{{$p->name}}</h3>{{$p->phone}}<br>{{$p->address}}{{$p->city}}</td>
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
				<th>ٹوٹل</th>
				<th>رقم</th>
				<th>تعداد</th>
				<th colspan="2">نام</th>
				@if(env('SHOES_COMPANY') == 1)
					<th>سائز</th>
					<th>رنگ</th>
				@endif
				<th>بار کوڈ</th>
				<th>نمبر شمار</th>
				
			</tr>
		</thead>
			
		<tbody align="center">
			<?php  $total = 0; ?>
			@foreach ($invoice->orders as $key => $value)
				@php($name = $value->product->name. " ". $value->product->brand)
				@if(env('SHOES_COMPANY') != 1)
				@if (!empty(session()->get('settings.products.optional_items')))
					@php($fields = explode(",", session()->get('settings.products.optional_items')))
					@foreach ($fields as $field)
						@if(!empty($value->product->$field) && strpos(session()->get('settings.products.optional_items'), $field) !== false)
							@php($name .= " " . (($field == 'category')?$value->product->$field->name:$value->product->$field))
						@endif
					@endforeach
				@endif
				@endif
			<tr>
				<td>{{ formating_price($value->quantity*$value->salePrice) }}</td>
				<td>{{($value->salePrice) + 0}}</td>
				<td>{{($value->quantity) + 0}} {{session()->get('settings.sales.enable_units_invoice')?$value->product->unit->name:""}}</td>
				<p hidden>{{ $total_quantity = $total_quantity + $value->quantity}}</p>
				<td colspan="2">{{$name}}</td>
				@if(env('SHOES_COMPANY') == 1)
					<td>{{ $value->product->size }}</td>
					<td>{{ $value->product->color }}</td>
				@endif
				<td>{{ $value->product->barcode }}</td>
				<td>{{$key + 1}}</td>
		<?php
			  $total += $value->quantity*$value->salePrice;
			  ?>
		
			</tr>
		@endforeach
		
		</tbody>
		<tfoot align="center">
			<tr>
				<td colspan="{{ $colspanValue +1 }}"><b>{{ $total_quantity }} :ٹوٹل تعداد </b></td>
			</tr>
			<tr>
				<td colspan="{{ $colspanValue +1 }}"></td>
			</tr>
			<tr>
				<td>{{ formating_price($total) }}</td>
				<td colspan="{{ $colspanValue}}">ذیلی ٹوٹل</td>
			</tr>
			 @if($invoice->shipping)
	  			<tr>
					  <td>{{ formating_price($invoice->shipping) }}</td>
					<td colspan="{{ $colspanValue}}">پیکنگ</td>
				</tr>
  			@endif
  @if($invoice->discount && $invoice->discount > 0)
  				<tr>
					  <td>{{ formating_price($invoice->discount) }}</td>
					<td colspan="{{ $colspanValue}}">رعایت</td>
				</tr>
  @endif
			<tr bgcolor="white" style="font-weight: bold; color: #000">
				<td>{{ formating_price($total + $invoice->shipping - $invoice->discount) }}</td>
				<td colspan="{{ $colspanValue}}">گرینڈ ٹوٹل</td>
			</tr>
			@if($invoice->customer)
			<?php
   	$pb = getCustomerBalance($invoice->customer_id);

   	$payment = payment_for_invoice($invoice->id);
   	?>
   	@if($payment)

	   @if(!empty($transaction_cash->amount))
				<tr>
					<td>{{ formating_price($transaction_cash->amount) }}</td>
					<td colspan="{{ $colspanValue}}">ادائیگی (کیش)</td>
				</tr>
		@endif

	   @if(!empty($transaction_card->amount))
		   <tr>
			   <td> {{ formating_price($transaction_card->amount) }} </td>
			   <td colspan="{{ $colspanValue}}">  ادائیگی  ( {{ $transaction_card->payment_type }} ) </td>
		   </tr>
		@endif

		<tr>
			<td>{{ formating_price($payment) }}</td>
			<td colspan="{{ $colspanValue}}">ادائیگی</td>
		</tr>
   	@endif
   	@if (env('PREVIOUS_BALANCE_IN_INVOICE',true))
   	
   	<tr  style="font-weight: bold; color: #000">
		<td>{{ formating_price($pb - ($total + $invoice->shipping - $invoice->discount) + $payment) }}</td>
				<td colspan="{{ $colspanValue}}">گزشتہ بیلنس</td>
			</tr>
			<tr style="font-size: 20px; font-weight: bold">
				<td>{{ formating_price($pb) }}</td>
				<td colspan="{{ $colspanValue}}">ٹوٹل واجب الادا</td>
			</tr>
			@endif
   	  @endif
		</tfoot>
	</table>
	<!-- Invoice Footer -->
	<center>
		<br><br>
		<p>
			{{$invoice->description}}
		</p>
		<br>
		@if(session()->get('settings.sales.is_invoice_qr_enable'))
	  		{!! \DNS2D::getBarcodeSVG($invoice->id, "QRCODE") !!}
  		@endif

  		{!! session()->get('settings.misc.invoice_footer') !!}<br>
  		<span id="copyright">Software by eAsasa (+92 345 4777487)</span>
	</center>

</body>
<script type="text/javascript">
	window.print();
</script>
</html>
