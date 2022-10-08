@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> Warehouses / Create </h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-12">

            <form action="{{ route('warehouses.store') }}" method="POST">
                <div id="log"></div>
             <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" class="form-control">
                 <small id="emailHelp" class="form-text text-muted">This is required</small>
            </div>
            <div class="form-group">
                <label>Address:</label>
                <input type="text" name="address" class="form-control">
            </div>
           
                <input type="hidden" name="_token" value="{{ csrf_token() }}">


                
                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a class="btn btn-link pull-right" href="{{ route('warehouses.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
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
