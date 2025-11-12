@extends('layouts.app')

@section('title', 'Edit Master Customer')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Update Master Customer</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('rbac.master-customer.update', $masterCustomer->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="tenant_id">Tenant Owner</label>
                            <select name="tenant_id" id="tenant_id" class="form-control @error('tenant_id') is-invalid @enderror">
                                <option value="">Select Tenant Owner (Optional for Internal)</option>
                                @foreach($tenantOwners as $tenantOwner)
                                    <option value="{{ $tenantOwner->id }}" {{ old('tenant_id', $masterCustomer->tenant_id) == $tenantOwner->id ? 'selected' : '' }}>{{ $tenantOwner->name }}</option>
                                @endforeach
                            </select>
                            @error('tenant_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="customer_name">Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', $masterCustomer->customer_name) }}" placeholder="Enter Customer Name">
                            @error('customer_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="customer_code">Customer Code</label>
                            <input type="text" name="customer_code" id="customer_code" class="form-control @error('customer_code') is-invalid @enderror" value="{{ old('customer_code', $masterCustomer->customer_code) }}" placeholder="Enter Customer Code">
                            @error('customer_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Enter Address">{{ old('address', $masterCustomer->address) }}</textarea>
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="npwp">NPWP</label>
                            <input type="text" name="npwp" id="npwp" class="form-control @error('npwp') is-invalid @enderror" value="{{ old('npwp', $masterCustomer->npwp) }}" placeholder="Enter NPWP">
                            @error('npwp')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('rbac.master-customer') }}" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
