@extends('layout')
@section('header')
    <div class="page-header">
        <h1>Sales Person / Show #{{ $salesperson->id }}</h1>
        <form action="{{ route('salesPerson.destroy', $salesperson->id) }}" method="POST" style="display: inline;">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="btn-group pull-right" role="group" aria-label="...">
                <a class="btn btn-warning btn-group" role="group" href="{{ route('salesPerson.edit', $salesperson->id) }}"><i
                        class="glyphicon glyphicon-edit"></i> Edit</a>
                <button type="submit" class="btn btn-danger" onclick="return (confirm('Delete? Are you sure?'))">Delete <i
                        class="glyphicon glyphicon-trash"></i></button>
            </div>
        </form>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><b>ID:</b> {{ $salesperson->id }}</li>
                    <li class="list-group-item"><b>Name:</b> {{ $salesperson->name }}</li>
                    <li class="list-group-item"><b>Mobile Number:</b> {{ $salesperson->phone }}</li>
                    <li class="list-group-item"><b>Address:</b> {{ $salesperson->address }}</li>
                </ul>
            </div>
            <hr>
            <a class="btn btn-link" href="{{ route('customers.index') }}"><i class="glyphicon glyphicon-backward"></i>
                Back</a>

        </div>
    </div>

@endsection
