@extends('layout')
@section('header')
<div class="row">
	<div class="col-md-12">
		<center>Name: {{$customer->name}}<br>
			Invoice # {{$invoice->id}}, Balance: {{number_format(getInvoiceBalance($invoice->id))}}
		</center>
		<form action="{{route('transactions.store')}}" method="POST">
			<div id="log"></div>
		<div class="form-group">
			<label>Payment Date</label>
			<input class="form-control" type="date" name="date" required="required" value="{{ date('Y-m-d') }}">
		</div>
		<div class="form-group">
			<label>Type</label>
			<select name="type" class="form-control">
				<option value="in">Debit</option>
				<option value="out">Credit</option>
			</select>
		</div>
		<div class="form-group">
			<label>Payment Type</label>
			<select name="payment_type[]" class="form-control">
				<option value="cash">Cash</option>
				<option value="cheque">Cheque</option>
				<option value="transfer">Bank Transfer</option>
			</select>
		</div>
		<div class="form-group">
			<label>Amount</label>
			<input class="form-control" type="text" name="amount[]" required="required">
		</div>
		<div class="form-group">
			<label>Bank (optional)</label>
			{!! Form::select('bank[]', App\BankAccount::GetBankDropDown(), "", ['class'=> 'form-control']) !!}
		</div>
		<div class="form-group">
			<label>Transaction Id (optional)</label>
			<input type="text" name="transacion_id[]" class="form-control" placeholder="Transaction ID (if any)">
		</div>
		<div class="form-group">
			<label>Release Date  (optional)</label>
			<input class="form-control" type="date" name="release_date[]">
		</div>
		<input type="hidden" name="customer" value="{{$customer->id}}">
		<input type="hidden" name="invoice_id" value="{{$invoice->id}}">
		<div class="form-group">
			<button class="btn btn-primary" type="submit" id="submit" onclick="setTimeout(closeIt,1000)" >Add</button>
		</div>
		</form>
	</div>
</div>
<hr>
@endsection

@section('content')
<div class="row">
	<div class="col-md-12">
		<h4>All Transactions of Invoice #{{$invoice->id}}</h4>
		<table class="table">
			<thead>
				<th>Id</th>
				<th>Date</th>
				<th>Type</th>
				<th>Payment Form</th>
				<th>Amount</th>
				<th>Transaction Id</th>
			</thead>
			<tbody>
				@foreach($transactions as $transaction)
				<tr>
					<td>{{$transaction->id}}</td>
				<td>{{app_date_format($transaction->date)}}</td>
				<td>{{($transaction->type == "in")?"Debit":"Credit"}}</td>
				<td>{{$transaction->payment_type}}</td>
				<td>{{$transaction->amount}}</td>
				<td>{{$transaction->transaction_id}}</td>
				</tr>
				@endforeach
			</tbody>
			
		</table>
	</div>
</div>
@endsection
<script>
	function closeIt(){
		window.close();
	}
</script>