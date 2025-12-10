<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Cloudflare Turnstile -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <style>
        body {
            background: #1a1a1a !important;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            width: 400px;
        }

        .login-logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #fff;
        }

        .login-logo img {
            max-width: 100px;
            margin-bottom: 10px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
        }

        .card-body {
            padding: 2.5rem;
            border-radius: 20px;
        }

        .login-box-msg {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            color: #6c757d;
        }

        .form-control {
            height: calc(2.25rem + 10px);
            padding: .75rem 1.25rem;
            border-radius: 10px;
        }

        .btn-primary {
            padding: .75rem;
            border-radius: 10px;
            font-weight: 600;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            border: none;
        }

        .dark-mode-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <!-- Dark Mode Toggle -->
    <div class="dark-mode-toggle">
        <button class="btn btn-outline-secondary" id="darkModeToggle">
            <i class="fas fa-moon"></i>
        </button>
    </div>

    <div class="login-box">
        <div class="login-logo">
            <img src="{{ asset('icon.png') }}" alt="Serlok Subcon Logo">
            <br>
            <b>Serlok Subcon</b>
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username"
                            value="{{ old('username') }}" placeholder="Username" required autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Turnstile Widget -->
                    <div class="mb-3 d-flex justify-content-center">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.key') }}"
                            data-theme="light"></div>
                    </div>
                    @error('cf-turnstile-response')
                        <div class="text-danger text-center mb-3 text-sm">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;">
                        <span class="swal2-x-mark">
                            <span class="swal2-x-mark-line-left"></span>
                            <span class="swal2-x-mark-line-right"></span>
                        </span>
                    </div>
                    <h4 class="mt-3 text-danger">Access Denied</h4>
                    <p class="mt-2" id="errorModalMessage"></p>
                    <button type="button" class="btn btn-danger mt-3 px-4" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script>
        // Check for error session
        @if(session('error_modal'))
            $(document).ready(function () {
                $('#errorModalMessage').text("{{ session('error_modal') }}");
                $('#errorModal').modal('show');
            });
        @endif

        // Dark mode toggle
        document.getElementById('darkModeToggle').addEventListener('click', function () {
            document.body.classList.toggle('dark-mode');
            const icon = this.querySelector('i');
            if (document.body.classList.contains('dark-mode')) {
                icon.className = 'fas fa-sun';
                localStorage.setItem('darkMode', 'enabled');
            } else {
                icon.className = 'fas fa-moon';
                localStorage.setItem('darkMode', 'disabled');
            }
        });

        // Load dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            document.getElementById('darkModeToggle').querySelector('i').className = 'fas fa-sun';
        }

        // Add dark mode styles
        const darkModeStyles = `
            <style>
                .dark-mode {
                    background-color: #1a1a1a !important;
                    color: #ffffff !important;
                }
                .dark-mode .card {
                    background-color: #2d3748 !important;
                    color: #ffffff !important;
                }
                .dark-mode .form-control {
                    background-color: #4a5568 !important;
                    border-color: #718096 !important;
                    color: #ffffff !important;
                }
                .dark-mode .form-control:focus {
                    background-color: #4a5568 !important;
                    border-color: #63b3ed !important;
                    color: #ffffff !important;
                }
                .dark-mode .input-group-text {
                    background-color: #4a5568 !important;
                    border-color: #718096 !important;
                    color: #ffffff !important;
                }
                .dark-mode .text-muted {
                    color: #a0aec0 !important;
                }
                .dark-mode .login-logo {
                    color: #ffffff !important;
                }
                
                /* SweetAlert2 Style Error Icon */
                .swal2-icon {
                    width: 5em;
                    height: 5em;
                    border-width: .25em;
                    border-style: solid;
                    border-radius: 50%;
                    border-color: #facea8;
                    margin: 0 auto;
                    position: relative;
                    box-sizing: content-box;
                    cursor: default;
                    user-select: none;
                }
                .swal2-icon.swal2-error {
                    border-color: #f27474;
                    color: #f27474;
                }
                .swal2-icon.swal2-error .swal2-x-mark {
                    display: flex;
                    position: relative;
                    flex-grow: 1;
                }
                .swal2-icon.swal2-error [class^=swal2-x-mark-line] {
                    display: block;
                    position: absolute;
                    top: 2.3125em;
                    width: 2.9375em;
                    height: .3125em;
                    border-radius: .125em;
                    background-color: #f27474;
                }
                .swal2-icon.swal2-error .swal2-x-mark-line-left {
                    left: 1.0625em;
                    transform: rotate(45deg);
                }
                .swal2-icon.swal2-error .swal2-x-mark-line-right {
                    right: 1em;
                    transform: rotate(-45deg);
                }
                .swal2-animate-error-icon {
                    animation: swal2-animate-error-icon .5s;
                }
                @keyframes swal2-animate-error-icon {
                    0% { transform: rotateX(100deg); opacity: 0; }
                    100% { transform: rotateX(0deg); opacity: 1; }
                }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', darkModeStyles);
    </script>
</body>

</html>