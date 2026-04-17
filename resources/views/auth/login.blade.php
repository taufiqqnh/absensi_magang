<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

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
                    Sign In
                </span>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success text-center m-3">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="login100-form validate-form">
                @csrf

                <!-- Username -->
                <div class="wrap-input100 m-b-26">
                    <span class="label-input100">Username</span>
                    <input
                        class="input100"
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Enter username"
                        required
                        autofocus
                        autocomplete="username"
                    >
                    <span class="focus-input100"></span>
                    @error('username')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Password -->
                <div class="wrap-input100 m-b-18">
                    <span class="label-input100">Password</span>
                    <input
                        class="input100"
                        type="password"
                        name="password"
                        placeholder="Enter password"
                        required
                        autocomplete="current-password"
                    >
                    <span class="focus-input100"></span>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex-sb-m w-full p-b-30">
                    <div class="contact100-form-checkbox">
                        <input
                            class="input-checkbox100"
                            id="remember"
                            type="checkbox"
                            name="remember"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <label class="label-checkbox100" for="remember">
                            Remember me
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <div>
                            <a href="{{ route('password.request') }}" class="txt1">
                                Forgot Password?
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Submit -->
                <div class="container-login100-form-btn">
                    <button type="submit" class="login100-form-btn">
                        Login
                    </button>
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
