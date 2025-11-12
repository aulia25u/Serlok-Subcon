@extends('adminlte::page')

@section('title', 'Access Denied')

@section('content_header')
    <h1>Access Denied (403)</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-ban icon-shake"></i>
                            Unauthorized Access
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <p class="lead">Unauthorized access to this menu.</p>
                        <p>You do not have permission to access this page. Please contact your administrator if you believe this is an error.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('css')
        <style>
            .icon-shake {
                animation: shake 0.5s ease-in-out infinite;
            }

            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }

            .card-body p.lead {
                animation: fadeIn 1s ease-in;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    @endpush
@stop
