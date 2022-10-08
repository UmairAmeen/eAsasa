@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> Licenses / Create </h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-12">

<h3>License Generator</h3>
<form action="{{ route('licenses.store') }}">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="text" name="package" value="A1" class="form-control"><br>
                  <input type="text" name="phone" value="" class="form-control"><br>
                  <input type="text" name="date" class="form-control date-picker"><br>
                  <input type="submit" name="submit">
                  <span id="log"></span>
</form>
license for users: {{$license_for_users}} <br>
license generated: {{$license_generated}}
{{dump($license_data)}}
            <form action="{{ route('licenses.store') }}" method="POST" id="fsa">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="text" name="license" id="number-field" class="form-control"> <br>
                <span id="log"></span>
                <p></p>
                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a class="btn btn-link pull-right" href="{{ route('licenses.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
                </div>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.min.js"></script>
  <script src="{{URL::to('/')}}/assets/js/jquery.maskedinput.min.js"></script>
  <script>
    $('.date-picker').datepicker({ dateFormat:'m/d/y'});
    $('#number-field').mask("*9***-*****-*****-*****-***");
    $("form").submit(function(e){
        e.preventDefault();
        var form = $(this);
        $.ajax({
          url: $(this).attr('action'),
          data: $(this).serialize(),
          method: "POST",
          dataType: "json"
        }).done(function(e) {
          $(form).find("#log").removeClass();
          $(form).find("#log").addClass( "alert alert-success" );
          $(form).find("#log").html(e.message);
        }).error(function(e){
            $(form).find("#log").removeClass();
            $(form).find("#log").addClass( "alert alert-danger" );
            $(form).find("#log").html(e.responseJSON.message);
        });
    });
  </script>

@endsection
