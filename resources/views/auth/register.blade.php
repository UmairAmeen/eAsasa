@extends('layout')
@section('header')
<div id="login-page">
@endsection
@section('content')
                    <form class="form-login no-ajax" role="form" method="POST" action="{{ url('/register') }}">
                    <h2 class="form-login-heading">Welcome</h2>
                <div class="login-wrap">
                <span id="log"></span>

                <input id="name" type="text" class="form-control" name="name" placeholder="Name" value="{{ old('name') }}" autofocus required="required">

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                                <br>

                    <input id="phone" type="text" class="form-control" name="phone" placeholder="Phone Number" value="{{ old('phone') }}" required="required">
                        @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                        @endif
                        <br>

                                <input id="password" type="password" placeholder="Password" class="form-control" name="password" required="required">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                                 <br>

                                <input id="password" type="password" placeholder="Password Confirm" class="form-control" name="password_confirmation" required="required">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                                <br>
{{ csrf_field() }}
                                 <button class="btn btn-theme btn-block" type="submit"><i class="fa fa-lock"></i> Register User</button>

                            </div>
                            </form>
                     
                    
                </div>
            
@endsection
@section('scripts')    

    <!--BACKSTRETCH-->
    <!-- You can use an image of whatever size. This script will stretch to fit in any screen size.-->
    <script type="text/javascript" src="assets/js/jquery.backstretch.min.js"></script>
    <script>
        $.backstretch("assets/img/login-bg.jpg", {speed: 500});
        $('#phone').mask("0999-9999999");
    </script>
@endsection