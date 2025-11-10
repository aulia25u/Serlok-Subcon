<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
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
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
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
            <img src="{{ asset('icon.png') }}" alt="Fixo Consultant Logo">
            <br>
            <b>Fixo Consultant</b>
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
                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                               name="username" value="{{ old('username') }}" 
                               placeholder="Username" required autofocus>
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script>
        // Dark mode toggle
        document.getElementById('darkModeToggle').addEventListener('click', function() {
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
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', darkModeStyles);
    </script>
</body>
</html>