@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> Suppliers / Create </h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-10">

            <form action="{{ route('suppliers.store') }}" method="POST">
             <div id="log" class=""></div>
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" class="form-control" placeholder="Supplier Name">
                 <small id="emailHelp" class="form-text text-muted">This is required</small>
            </div>
            <div class="form-group">
                <label>Phone Number:</label>
                <input type="tel" name="phone_number" class="form-control" placeholder="Supplier Phone Number">
            </div>
            <div class="form-group">
                <label>Company Name:</label>
                <input type="text" name="company_name" class="form-control" placeholder="Company Name">
            </div>
            <div class="form-group">
                <label>Supplier Type:</label>
                <input type="text" name="type" class="form-control" placeholder="Supplier Type">
            </div>

            <div class="form-group">
                <label>Address:</label>
                <input type="text" name="address" class="form-control" placeholder="Supplier Address">
                <small id="emailHelp" class="form-text text-muted">This is required</small>
            </div>

            <div class="form-group">
                <label>Opening Balance</label>
                <input class="form-control" placeholder="Opening Balance" name="openingbalance">
                <small id="emailHelp" class="form-text text-muted">This is Debit amount</small>
              </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" maxlength="255" class="form-control" placeholder="Supplier Description"></textarea>
                <small id="emailHelp" class="form-text text-muted">Optional</small>

            </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                
                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a class="btn btn-link pull-right" href="{{ route('suppliers.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
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
