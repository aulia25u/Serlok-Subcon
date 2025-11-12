@extends('layouts.app')

@section('title', 'View Master Customer')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Master Customer Details</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <strong>Tenant:</strong>
                        {{ $masterCustomer->tenantOwner ? $masterCustomer->tenantOwner->name : 'N/A' }}
                    </div>
                    <div class="form-group">
                        <strong>Customer Name:</strong>
                        {{ $masterCustomer->customer_name }}
                    </div>
                    <div class="form-group">
                        <strong>Customer Code:</strong>
                        {{ $masterCustomer->customer_code }}
                    </div>
                    <div class="form-group">
                        <strong>Address:</strong>
                        {{ $masterCustomer->address }}
                    </div>
                    <div class="form-group">
                        <strong>NPWP:</strong>
                        {{ $masterCustomer->npwp }}
                    </div>
                    <div class="form-group">
                        <strong>Created At:</strong>
                        {{ $masterCustomer->created_at }}
                    </div>
                    <div class="form-group">
                        <strong>Updated At:</strong>
                        {{ $masterCustomer->updated_at }}
                    </div>
                    <a href="{{ route('rbac.master-customer') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
