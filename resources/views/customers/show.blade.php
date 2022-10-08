@extends('layout')
@section('header')
<div class="page-header">
        <h1>Customers / Show #{{$customer->id}}</h1>
        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display: inline;" >
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="btn-group pull-right" role="group" aria-label="...">
                <a class="btn btn-warning btn-group" role="group" href="{{ route('customers.edit', $customer->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                <a class="btn btn-default btn-group" href="{{url('reports')}}/balance_sheet?customer_id={{$customer->id}}">Ledger</a>
                <button type="submit" class="btn btn-danger" onclick="return (confirm('Delete? Are you sure?'))">Delete <i class="glyphicon glyphicon-trash"></i></button>
            </div>
        </form>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><b>ID:</b> {{$customer->id}}</li>
    <li class="list-group-item"><b>Name:</b> {{$customer->name}}</li>
    <li class="list-group-item"><b>Mobile Number:</b> {{$customer->phone}}</li>
    <li class="list-group-item"><b>Address:</b> {{$customer->address}}</li>
    <li class="list-group-item"><b>Type:</b> {{$customer->type}}</li>
    <li class="list-group-item"><b>Notes:</b> {{$customer->notes}}</li>
    
  </ul>
</div>
<hr>
 <!-- <div class="content-panel">
    <h1>Ledger</h1>
    <small</small>
    <table class="table table-bordered table-striped table-condensed table-hover xdatatable">
         <thead>
             <tr>
             <th>ID</th>
             <th>Date</th>
             <th>Type</th>
             <th>Payment Type</th>
             <th>Transaction#</th>
             <th>Credit</th>
             <th>Debit</th>
             <th>Balance</th>
            

            </tr>
        </thead>
        <php $balance = 0>
        <tbody>
            foreach($trans as $tran)
            <php
            if ($tran->type == "in")
            {
              //debit
              $balance -= $tran->amount;
            }else{
              //credit
              $balance += $tran->amount;
            }
            >
            <tr>
                <td>{$tran->id}</td>
                 <td>{date_format_app($tran->date)}}</td>
                 <td>{($tran->type == "in")?"debit":"credit"}}</td>
                 <td>{($tran->type == "in")?$tran->payment_type:"-"}}</td>
                 <td>{$tran->transaction_id}}</td>
                 <td>{($tran->type!="in")?$tran->amount:""}}</td>
                 <td>{($tran->type=="in")?$tran->amount:""}}</td>
                 <td>{$balance}}</td>
            </tr>
            endforeach
            
        </tbody>
        <tfoot>
            <tr style="    background: beige">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th><u>Remaining Balance</u></th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{($balance > 0)?"Credit":"Debit"}}</td>
                <td>{number_format(getCustomerBalance($customer->id))}}</td>
            </tr>
        </tfoot>
      </table>  
 </div> -->

            <a class="btn btn-link" href="{{ route('customers.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>

        </div>
    </div>

@endsection