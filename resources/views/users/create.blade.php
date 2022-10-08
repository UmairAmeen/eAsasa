@extends('layout')
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> Add User </h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-10">
          {!! Form::open(array('route' => 'users.store','method'=>'POST')) !!}
              <div class="form-group">
                  <strong>Name:</strong>
                  {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
              </div>
              <div class="form-group">
                  <strong>Email:</strong>
                  {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control email')) !!}
              </div>
              <div class="form-group">
                  <strong>Password:</strong>
                  {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
              </div>
              <div class="form-group">
                  <strong>Confirm Password:</strong>
                  {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
              </div>
              @if (is_admin())
              <div class="form-group">
              <strong>Discount(%):</strong>
              {!! Form::number('allowed_discount', null, array('placeholder' => 'Discount','class' => 'form-control','min' => '0','max' => '100','onkeyup' => 'adjustDiscount(this)')) !!}
                </div>
              <div class="form-group">
              <strong>Discount(PKR):</strong>
              {!! Form::number('allowed_discount_pkr', null, array('placeholder' => 'Discount','class' => 'form-control','min' => '0')) !!}
                </div>
              <div class="form-group switch_align">
              <strong>Only Fixed Discount:</strong>
              {!! Form::checkbox('fixed_discount', null,false, array('placeholder' => 'Discount','class' => 'form-control')) !!}
                </div>
              <div class="form-group switch_align">
              <strong>Master Discount:</strong>
              {!! Form::checkbox('master_discount', null, true, array('placeholder' => 'Discount','class' => 'form-control')) !!}
                </div>
              @endif
              <div class="form-group">
                  <strong>Role:</strong>
                  {!! Form::select('roles[]', $roles,[], array('class' => 'form-control','multiple')) !!}
              </div>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
              <div class="well well-sm">
                  <button type="submit" class="btn btn-primary">Create</button>
                  <a class="btn btn-link pull-right" href="{{ route('users.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
              </div>
            {!! Form::close() !!}

        </div>
    </div>
@endsection
@section('scripts')  
    <script src="{{asset('assets/js/bootstrap-switch.js')}}"></script> 
    <script>
        $('.email').mask("0999-9999999");
        // $("#reset_pass_master").mask("*****-*****-*****-*****-*****-*****-**");

        function adjustDiscount(e) {
            if ($(e).val() < 0) {
                $(e).val(0);
            }
            if ($(e).val() > 100) {
                $(e).val(100);
            }
        }

        $(document).ready(function(){
          $("input[type='checkbox']").wrap('<div class="switch" />').parent().bootstrapSwitch();
        });
        
    </script>
@endsection