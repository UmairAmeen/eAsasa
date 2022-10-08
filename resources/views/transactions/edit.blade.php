@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-edit"></i> Transaction / Edit #{{$transaction->id}}</h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-12">

            <h3>Name: {{($transaction->customer)?$transaction->customer->name:""}} {{($transaction->supplier)?$transaction->supplier->name:""}}</h3>

            <h3>Type: {{$transaction->type}}</h3>


            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" placeholder="Date" value="{{$transaction->date}}">
                </div>

                <div class="form-group">
                    <label>Payment Type</label>
                    <select name="payment_type" class="form-control">
                        <option value="cash" {{($transaction->payment_type=="cash")?"selected":""}}>Cash</option>
                        <option value="cheque" {{($transaction->payment_type=="cheque")?"selected":""}}>Cheque</option>
                        <option value="transfer" {{($transaction->payment_type=="transfer")?"selected":""}}>Online Transfer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount</label>
                    <input type="text" name="amount" class="form-control" placeholder="Amount" value="{{$transaction->amount}}">
                </div>
                <div class="form-group">
                    <label>Bank</label>
                    {!! Form::select('bank', App\BankAccount::GetBankDropDown(), !empty($transaction->bank) ? $transaction->bank : "", ['class'=> 'form-control']) !!}
                </div>

                <div class="form-group">
                    <label>Payment Release Date</label>
                    <input type="date" name="release_date" class="form-control" placeholder="payment release date" value="{{$transaction->release_date}}">
                </div>

                <div class="form-group">
                    <label>Transaction Id/Ref</label>
                    <input type="text" name="transaction_id" class="form-control" placeholder="Transaction ID/ Cheque Number or Reference (if any)" value="{{$transaction->transaction_id}}">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Enter Description" value="{{$transaction->description}}">
                </div>
                    
                </div>
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                
                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a class="btn btn-link pull-right" href="{{ route('transactions.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
                </div>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.min.js"></script>
  <script>
    $('.date-picker').datepicker({
    });
  </script>
@endsection
