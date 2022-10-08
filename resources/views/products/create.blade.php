@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> Products / Create </h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-12">

            <form action="{{ route('products.store') }}" method="POST">
            <input type="text" name="name" placeholder="Name" class="form-control"> <br>
            <input type="text" name="barcode" placeholder="barcode" class="form-control"> <br>
            <input type="text" name="sale_price" placeholder="sale price" class="form-control"> <br>
            <input type="text" name="initial_stock" placeholder="Initial stock" class="form-control"> <br>
            <input type="text" name="notify_quantity" placeholder="notify_quantity" class="form-control">

                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                
                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a class="btn btn-link pull-right" href="{{ route('products.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
                </div>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
    
</script>
@endsection
