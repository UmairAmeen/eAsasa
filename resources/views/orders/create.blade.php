@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> Orders / Create </h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-12">

            <form action="{{ route('orders.store') }}" method="POST">
            {{Form::select('product_id', $products, null, ['placeholder' => 'Pick a Product...', 'class'=>'form-control'])}} <br>
            {{Form::select('customer_id', $customers, null, ['placeholder' => 'Pick a Customer...', 'class'=>'form-control'])}} <br>

            <input type="text" name="amount" placeholder="Stock Amount Order" class="form-control"> <br>
            <input type="text" name="saleprice" placeholder="sale price" class="form-control"> <br>
            <input type="text" name="discount" placeholder="discount" class="form-control"> <br>
            <div class="checkbox">
              <label><input type="checkbox" value="" name="discountispercentage">is Percentage</label>
            </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                
                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a class="btn btn-link pull-right" href="{{ route('orders.index') }}"><i class="glyphicon `glyphicon-backward"></i> Back</a>
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
