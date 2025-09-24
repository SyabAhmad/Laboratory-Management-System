
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Sign In</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="Talha Khan" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

        <!-- App css -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
        {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" /> --}}
        <style>
            .login-container {
                display: flex;
                min-height: 100vh;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            .login-card {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                overflow: hidden;
                max-width: 400px;
                width: 100%;
            }
            .login-header {
                background: #f8f9fa;
                padding: 20px;
                text-align: center;
                border-bottom: 1px solid #e9ecef;
            }
            .login-body {
                padding: 30px;
            }
            .btn-login {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 25px;
                padding: 10px 20px;
                font-weight: bold;
            }
            .btn-login:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
        </style>
    </head>

    <body class="login-container">

        <div class="login-card">
            <div class="login-header">
                <a href="index.html">
                    <img src="{{ asset('assets/images/logo-dark.png') }}" alt="Logo" height="40">
                </a>
                <h4 class="mt-3">Welcome Back</h4>
                <p class="text-muted">Sign in to your account</p>
            </div>

            <div class="login-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="emailaddress">Email Address</label>
                        <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="checkbox-signin" checked>
                            <label class="custom-control-label" for="checkbox-signin">Remember me</label>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <button class="btn btn-login btn-block" type="submit">Sign In</button>
                    </div>

                    <div class="text-center">
                        <a href="#" class="text-muted">Forgot your password?</a>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p class="text-muted mb-0">Don't have an account? <a href="{{ route('register') }}" class="text-primary"><b>Sign Up</b></a></p>
                </div>
            </div>
        </div>

        <!-- Vendor js -->
        <script src="{{ asset('js/app.js') }}"></script>
        <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

        <!-- App js -->
        <script src="{{ asset('assets/js/app.min.js') }}"></script>

    </body>
</html>
