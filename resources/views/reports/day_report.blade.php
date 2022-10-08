@extends('reporting')

@section('header')
<div class="col-md-12 text-center no-print">
	<h3>Day Report {{ date_format_app($from) }}</h3>
	<a href="{{url('reports')}}" class="btn btn-primary">Back to All Reports</a>
</div>
@endsection
@section('content')
<div class="row showback text-center no-print">
		<div class="col-md-3"></div>
	<form method="GET" action="{{url('reports')}}/day_report" class="col-md-6">
	<br><br><br>

			<div class="form-group">
				<label>Date</label>
				<input type="date" class="form-control" name="from" value="{{$from}}">
			</div>
			<div class="form-group">

			<button type="submit" class="btn btn-success">Calculate Data</button>
		</div>
			
		</form>
</div>
<div class="row showback">

	<center>
	<h3>Day Report {{ date_format_app($from) }}</h3>
	<a onclick="window.print();" class="btn btn-primary btn-xl no-print">Print</a>
	<a href="{{url('reports/log_report')}}?from={{$from}}" class="btn btn-default btn-xl no-print">Full Details</a>

	</center>
	<table class="table table-striped table-bordered table-hover">
		<thead class="thead-dark">
			<tr>
				<th>Account</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			<!-- <tr>
				<td>Total Sales</td>
				<td>{formating_price($totals['total_sales'])}</td>
			</tr>
			<tr>
				<td>Total Sale Order</td>
				<td>{formating_price($totals['total_sale_order'])}</td>
			</tr>
 -->			<tr>
				<td>Total Purchases</td>
				<td>{{formating_price($totals['total_purchases'])}}</td>
			</tr>
			<!-- <tr>
				<td>Total Refund</td>
				<td>{formating_price($totals['total_refund'])}</td>
			</tr> -->
			<tr>
				<td>Total Transaction IN (DEBIT)</td>
				<td>{{formating_price($totals['total_in'])}}</td>
			</tr>
			<tr>
				<td>Total Transaction OUT (CREDIT)</td>
				<td>{{formating_price($totals['total_out'])}}</td>
			</tr>
		</tbody>
	</table>
	<small class="onlyPrint">Report by eAsasa ( 0345 4777487)</small>
	<small class="onlyPrint">Print Timestamp: {{ date("d M Y H:i:s") }}</small>
</div>
@endsection