
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
            /* Color palette
               Primary: #2563EB
               Secondary: #64748B
               Background (Cards): #F8FAFC
               Surface (Main BG): #FFFFFF
               Success: #10B981
               Warning: #F59E0B
               Danger: #EF4444
               Text Headings: #1E293B
               Text Body: #475569
            */
            :root{
                --primary: #2563EB;
                --secondary: #64748B;
                --bg-cards: #F8FAFC;
                --surface: #FFFFFF;
                --success: #10B981;
                --warning: #F59E0B;
                --danger: #EF4444;
                --text-heading: #1E293B;
                --text-body: #475569;
            }

            html,body{
                height:100%;
                background: var(--surface);
                color: var(--text-body);
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            }

            .login-container {
                display: flex;
                min-height: 100vh;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            }
            .login-card {
                background: var(--surface);
                border-radius: 12px;
                box-shadow: 0 8px 24px rgba(15,23,42,0.08);
                overflow: hidden;
                max-width: 420px;
                width: 100%;
                border: 1px solid rgba(15,23,42,0.04);
            }
            .login-header {
                background: var(--bg-cards);
                padding: 20px;
                text-align: center;
                border-bottom: 1px solid rgba(15,23,42,0.05);
                color: var(--text-heading);
            }
            .login-body {
                padding: 28px;
                color: var(--text-body);
            }
            .btn-login {
                background: var(--primary);
                color: white;
                border: none;
                border-radius: 10px;
                padding: 10px 18px;
                font-weight: 600;
            }
            .btn-login:hover {
                transform: translateY(-1px);
                box-shadow: 0 6px 18px rgba(37,99,235,0.18);
            }

            .login-header h4 { color: var(--text-heading); }
            .login-header p { color: var(--text-body); margin-top:4px }
            .form-control { border-radius: 8px; }
            .invalid-feedback { color: var(--danger); }
            a.text-muted { color: var(--secondary); }
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
