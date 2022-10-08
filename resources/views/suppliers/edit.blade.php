@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-edit"></i>Edit Supplier: {{$supplier->name}}</h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-12">

            <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
            <div id="log" class=""></div>
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" class="form-control" value="{{$supplier->name}}" placeholder="Supplier Name">
            </div>
            <div class="form-group">
                <label>Phone Number:</label>
                <input type="tel" name="phone_number" value="{{$supplier->phone}}"  class="form-control" placeholder="Supplier Phone Number">
            </div>
            <div class="form-group">
                <label>Company Name:</label>
                <input type="text" name="company_name" value="{{$supplier->company_name}}" class="form-control" placeholder="Company Name">
            </div>
            <div class="form-group">
                <label>Supplier Type:</label>
                <input type="text" name="type" class="form-control" value="{{$supplier->type}}" placeholder="Supplier Type">
            </div>

            <div class="form-group">
                <label>Address:</label>
                <input type="text" name="address" class="form-control" value="{{$supplier->address}}"  placeholder="Supplier Address">
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" maxlength="255" class="form-control" placeholder="Supplier Description">{{$supplier->description}}</textarea>
                <small id="emailHelp" class="form-text text-muted">Optional</small>

            </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                
                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a class="btn btn-link pull-right" href="{{ route('suppliers.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
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
