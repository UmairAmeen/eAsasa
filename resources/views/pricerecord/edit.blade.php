@extends('layout')

@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-edit"></i> Price Record / Edit #{{$content->id}}</h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-12">

            <form action="{{ route('price_record.update', $content->id) }}" method="POST">
              Name:<h4>{{$content->product->name}} {{$content->product->brand}}</h4>
              Date:<h4>{{date_format_app($content->date)}}</h4>
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                 <div id="log" class=""></div>
            
            <div class="form-group">
                <label>Price:</label>
                <input type="text" name="price" value="{{$content->price}}" class="form-control" placeholder="Update Purchase Price">
                <!-- <small id="emailHelp" class="form-text text-muted">This is required</small> -->
            </div>
           
           

                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a class="btn btn-link pull-right" href="{{ route('customers.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>
                </div>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.min.js"></script>
    <!--custom switch-->
  <script src="{{asset('assets/js/bootstrap-switch.js')}}"></script>
  <script>
    $('.date-picker').datepicker({
    });
    $(document).ready(function(){
    $("[data-toggle='switch']").wrap('<div class="switch" />').parent().bootstrapSwitch();
  
});
  </script>
@endsection
