<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>

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
                    Verify Email
                </span>
            </div>

            <div class="login100-form p-t-30 p-b-30">

                <!-- Info Text -->
                <p class="text-center text-muted mb-4">
                    Thanks for signing up! Before getting started, please verify your email
                    address by clicking the link we just emailed to you.
                    If you didn’t receive the email, we can send another one.
                </p>

                <!-- Status -->
                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success text-center">
                        A new verification link has been sent to your email address.
                    </div>
                @endif

                <!-- Actions -->
                <div class="row mt-4">
                    <div class="col-md-6 mb-2">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="login100-form-btn w-100">
                                Resend Verification Email
                            </button>
                        </form>
                    </div>

                    <div class="col-md-6 mb-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="btn btn-outline-secondary w-100">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>

            </div>

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
