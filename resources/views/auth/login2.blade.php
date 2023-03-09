@extends('layouts.app')

@section('content')
<!--===============================================================================================-->
<link rel="icon" type="image/png" href="{{asset('loginplugins/images/icons/favicon.ico')}}" />
<!--===============================================================================================-->
<link rel="stylesheet" type="text/css" href="{{asset('loginplugins/vendor/bootstrap/css/bootstrap.min.css')}}">
<!--===============================================================================================-->
<link rel="stylesheet" type="text/css" href="{{asset('loginplugins/fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
<!--===============================================================================================-->
<link rel="stylesheet" type="text/css" href="{{asset('loginplugins/vendor/animate/animate.css')}}">
<!--===============================================================================================-->
<link rel="stylesheet" type="text/css" href="{{asset('loginplugins/vendor/css-hamburgers/hamburgers.min.css')}}">
<!--===============================================================================================-->
<link rel="stylesheet" type="text/css" href="{{asset('loginplugins/vendor/select2/select2.min.css')}}">
<!--===============================================================================================-->
<link rel="stylesheet" type="text/css" href="{{asset('loginplugins/css/util.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('loginplugins/css/main.css')}}">
<!--===============================================================================================-->
<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100">
            <div class="login100-pic js-tilt" data-tilt>
                <img src="{{ asset('dist/img/forbeslogo.png')}}" alt="IMG">
            </div>

            <form class="login100-form validate-form" method="POST" action="{{ route('login') }}">
                @csrf
                <span class="login100-form-title">
                    Faculty Login
                </span>

                <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                    <!-- <input class="input100" type="text" name="email" placeholder="Email"> -->
                    <input placeholder="Email" id="email" type="email" class="input100 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                    </span>
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="wrap-input100 validate-input" data-validate="Password is required">
                    <!-- <input class="input100" type="password" name="pass" placeholder="Password"> -->
                    <input placeholder="Password" id="password" type="password" class="input100 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </span>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="container-login100-form-btn">
                    <button type="submit" class="login100-form-btn">
                        Login
                    </button>
                </div>

                <div class="text-center p-t-12">
                    <span class="txt1">
                        Forgot
                    </span>
                    <a class="txt2" href="{{ route('password.request') }}">
                        Password?
                    </a>
                </div>

                <div class="text-center p-t-136">
                    <!-- <a class="txt2" href="#">
                        Create your Account
                        <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
                    </a> -->
                </div>
            </form>
        </div>
    </div>
</div>
<!--===============================================================================================-->
<script src="{{asset('loginplugins/vendor/jquery/jquery-3.2.1.min.js')}}"></script>
<!--===============================================================================================-->
<script src="{{asset('loginplugins/vendor/bootstrap/js/popper.js')}}"></script>
<script src="{{asset('loginplugins/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
<!--===============================================================================================-->
<script src="{{asset('loginplugins/vendor/select2/select2.min.js')}}"></script>
<!--===============================================================================================-->
<script src="{{asset('loginplugins/vendor/tilt/tilt.jquery.min.js')}}"></script>
<script>
    $('.js-tilt').tilt({
        scale: 1.4
    })
</script>
<!--===============================================================================================-->
<script src="js/main.js"></script>
@endsection