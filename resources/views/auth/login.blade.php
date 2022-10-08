@extends('layout')
@section('header')
<div id="login-page">
@endsection
@section('content')
                    <form class="form-login no-ajax" role="form" method="POST" action="{{ url('/login') }}">
                    <h2 class="form-login-heading">sign in now <br> {{date("h:i:s a")}}</h2>
                <div class="login-wrap">
                <span id="log"></span>
                    <input id="phone" type="tel" class="form-control" name="email" placeholder="Phone Number" value="{{ old('email') }}" autofocus required="required">
                        @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ str_replace('email','phone',$errors->first('email')) }}</strong>
                                    </span>
                        @endif
                        <br>

                                <input id="password" type="password" placeholder="Password" class="form-control" name="password" required="required">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif

                        <label class="checkbox">
                        <span class="pull-right">
                            <a data-toggle="modal" href="#myModal"> Forgot Password?</a>
        
                        </span>
                    </label>
{{ csrf_field() }}
                                 <button class="btn btn-theme btn-block" type="submit"><i class="fa fa-lock"></i> SIGN IN</button>

                            </div>
                            </form>
                              <!-- Modal -->
                  <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
                      <div class="modal-dialog">
                        <form method="POST" action="{{url('reset_access')}}" id="reset_form">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                  <h4 class="modal-title">Forgot Password ?</h4>
                              </div>
                              <div class="modal-body">
                                  <h3>If you forget your password, Please call our helpline and ask them to reset!</h3>
                                  <h4>Your Application Token: {{$mac}} </h4> <br><br>
                                  <span id="log"></span>
                                  <br><br>
                                    RESET TOKEN: <input type="text" style="text-transform: uppercase" id="reset_pass_master" name="reset_pass_master" class="form-control">
                                    {{ csrf_field() }}
                              </div>
                              <div class="modal-footer">
                                  <button data-dismiss="modal" class="btn btn-default" type="button">Cancel</button>
                                  <button class="btn btn-theme" type="submit">Submit</button>
                              </div>
                                  </form>
                          </div>
                      </div>
                  </div>
                  <!-- modal -->
                    
                </div>
            
@endsection
@section('scripts')    

    <!--BACKSTRETCH-->
    <!-- You can use an image of whatever size. This script will stretch to fit in any screen size.-->
    <script type="text/javascript" src="assets/js/jquery.backstretch.min.js"></script>
    <script>
        $.backstretch("assets/img/login-bg.jpg", {speed: 500});
        $('#phone').mask("0999-9999999");
        $("#reset_pass_master").mask("*****-*****-*****-*****-*****-*****-**");
    </script>
@endsection