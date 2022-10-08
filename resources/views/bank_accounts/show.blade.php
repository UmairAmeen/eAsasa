@extends('layout')
@section('header')
    <div class="page-header">
        <h1>BankAccounts / Show #{{ $bank_account->id }}</h1>
        <form action="{{ route('bank_accounts.destroy', $bank_account->id) }}" method="POST" style="display: inline;"
            onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="btn-group pull-right" role="group" aria-label="...">
                <a class="btn btn-warning btn-group" role="group"
                    href="{{ route('bank_accounts.edit', $bank_account->id) }}"><i class="glyphicon glyphicon-edit"></i>
                    Edit</a>
                <button type="submit" class="btn btn-danger">Delete <i class="glyphicon glyphicon-trash"></i></button>
            </div>
        </form>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-12">
                    <div id="log"></div>
                    <div class="card">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><b>ID:</b> {{ $bank_account->id }}</li>
                            <li class="list-group-item"><b>Name:</b> {{ $bank_account->name }}</li>
                            <li class="list-group-item"><b>Account Number:</b> {{ $bank_account->account_number }}</li>
                            <li class="list-group-item"><b>Branch:</b> {{ $bank_account->branch }}</li>
                            <li class="list-group-item"><b>Comment:</b> {{ $bank_account->comment }}</li>
                        </ul>
                    </div>

                </div>
            </div>


        </div>
    </div>


    <a class="btn btn-link" href="{{ route('bank_accounts.index') }}"><i class="glyphicon glyphicon-backward"></i>
        Back</a>

    </div>
    </div>

@endsection
