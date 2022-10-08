@extends('layout')

@section('header')
    
@endsection

@section('content')
  <div class="row content-panel">
    <div class="panel-body">
      <div class="col-lg-8 detailed">
        <h4 class="mb">Personal Information</h4>
          <!-- {!! Form::open(array('url'=>'save_profile','method'=>'POST','class'=>'form-horizontal ', 'files'=>true)) !!} -->
          <form enctype="multipart/form-data" role="form" class="form-horizontal" action="/profile" method="POST" >
          <div id="log"></div>
              <div class="form-group">
                  <label class="col-lg-2 control-label">Name</label>
                  <div class="col-lg-6">
                      <input type="text" value="{{$name}}" placeholder="Your Name" name="name" id="name" class="form-control">
                  </div>
              </div>
              <div class="form-group">
                  <label class="col-lg-2 control-label">License</label>
                  <div class="col-lg-6">
                      <input type="text" value="{{$license}}" placeholder="Your License Key" name="license" id="lives-in" class="form-control">
                  </div>
              </div>
              <div class="form-group">
                  <label class="col-lg-2 control-label">Password</label>
                  <div class="col-lg-6">
                      <input type="password" placeholder="Your New Password" name="password" id="password" class="form-control">
                  </div>
              </div>
              <div class="form-group">
                  <label class="col-lg-2 control-label">Confirm Password</label>
                  <div class="col-lg-6">
                      <input type="password" placeholder="Confirm Your New Password" name="confirm_password" id="password" class="form-control">
                  </div>
              </div>

              <div class="form-group">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-success">Save Profile</button>
              </div>
          <!-- {!! Form::close() !!} -->
          </form>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
<script type="text/javascript">


</script>
@endsection