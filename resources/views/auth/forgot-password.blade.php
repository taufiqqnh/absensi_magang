<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>

    <!-- Favicon -->
    <link href="{{ asset('assets_landingpage/img/logo_event_bg.jpg') }}" rel="icon">
    <link href="{{ asset('assets_landingpage/img/logo_event_bg.jpg') }}" rel="apple-touch-icon">

    <!-- Bootstrap & Plugins -->
    <link rel="stylesheet" href="{{ asset('assets_login/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_login/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_login/fonts/Linearicons-Free-v1.0.0/icon-font.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_login/vendor/animate/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_login/vendor/css-hamburgers/hamburgers.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_login/vendor/animsition/css/animsition.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_login/vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_login/vendor/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_login/css/util.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_login/css/main.css') }}">
</head>
<body>

<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100">

            <!-- Header -->
            <div class="login100-form-title"
                 style="background-image: url('{{ asset('assets_landingpage/img/logo_event_bg.jpg') }}');">
                <span class="login100-form-title-1">
                    Forgot Password
                </span>
            </div>

            <!-- Info Text -->
            <div class="p-4 text-center text-muted">
                Forgot your password? No problem.<br>
                Enter your email address and we will send you a password reset link.
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success text-center mx-4">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Forgot Password Form -->
            <form method="POST" action="{{ route('password.email') }}" class="login100-form validate-form">
                @csrf

                <!-- Email -->
                <div class="wrap-input100 m-b-20">
                    <span class="label-input100">Email</span>
                    <input
                        class="input100"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter your email"
                        required
                        autofocus
                    >
                    <span class="focus-input100"></span>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="container-login100-form-btn">
                    <button type="submit" class="login100-form-btn">
                        Email Password Reset Link
                    </button>
                </div>

                <!-- Back to Login -->
                <div class="text-center p-t-20">
                    <a href="{{ route('login') }}" class="txt2">
                        ← Back to Login
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('assets_login/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('assets_login/vendor/animsition/js/animsition.min.js') }}"></script>
<script src="{{ asset('assets_login/vendor/bootstrap/js/popper.js') }}"></script>
<script src="{{ asset('assets_login/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets_login/vendor/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets_login/vendor/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('assets_login/vendor/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets_login/vendor/countdowntime/countdowntime.js') }}"></script>
<script src="{{ asset('assets_login/js/main.js') }}"></script>

</body>
</html>
