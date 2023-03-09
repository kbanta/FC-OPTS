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

<div class="login-dark">
    <form class="login100-form validate-form" method="POST" action="{{ route('login') }}">
        @csrf
        <h2 class="sr-only">Login Form</h2>
        <div class="logo">
            <img src="{{ asset('dist/img/forbeslogo.png')}}" alt="IMG">
        </div>
        <div class="illustration"><i class="icon ion-ios-locked-outline"></i></div>
        <!-- <div class="wrap-input100">
            <input class="form-control" id="email" type="email" class="input100 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
            <span class="focus-input100"></span>
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div> -->
        <div class="wrap-input100">
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
        @if(session('error'))
        <center>
            <span class="text-danger">{{session('error')}}</span>
        </center>
        @endif
        <div class="container-login100-form-btn">
            <button type="submit" class="btn btn-block btn-success">
                Login
            </button>
        </div>
        <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
    </form>
</div>
<style>
    /* .login-dark {
        height: 650px;
        background: #043927 url(".../dist/img/forbeslogo.png");
        background-size: cover;
        position: relative;
    } */

    .login-dark form {
        max-width: 320px;
        width: 90%;
        background-color: #1e2833;
        padding: 40px;
        border-radius: 4px;
        transform: translate(-50%, -50%);
        position: absolute;
        top: 50%;
        left: 50%;
        color: #fff;
        box-shadow: 3px 3px 4px rgba(0, 0, 0, 0.2);
    }

    .login-dark .illustration {
        text-align: center;
        padding: 15px 0 20px;
        font-size: 100px;
        color: #2980ef;
    }

    .login-dark form .form-control {
        background: none;
        border: none;
        border-bottom: 1px solid #434a52;
        border-radius: 0;
        box-shadow: none;
        outline: none;
        color: inherit;
    }

    .login-dark form .btn-primary {
        background: #214a80;
        border: none;
        border-radius: 4px;
        padding: 11px;
        box-shadow: none;
        margin-top: 26px;
        text-shadow: none;
        outline: none;
    }

    .login-dark form .btn-primary:hover,
    .login-dark form .btn-primary:active {
        background: #214a80;
        outline: none;
    }

    .login-dark form .forgot {
        display: block;
        text-align: center;
        font-size: 12px;
        color: #6f7a85;
        opacity: 0.9;
        text-decoration: none;
    }

    .login-dark form .forgot:hover,
    .login-dark form .forgot:active {
        opacity: 1;
        text-decoration: none;
    }

    .login-dark form .btn-primary:active {
        transform: translateY(1px);
    }

    .logo {
        width: 100px;
        margin: auto;
    }

    .logo img {
        width: 100%;
        height: 80px;
        /* object-fit: cover; */
    }
</style>
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
<script src="{{asset('loginplugins/js/main.js')}}"></script>
@endsection