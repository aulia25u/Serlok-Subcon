@extends('layouts.app')

@section('title', 'Edit Master Item')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Master Item</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('master-item.update', $masterItem->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="tenant_id">Tenant Owner</label>
                            <select name="tenant_id" id="tenant_id" class="form-control @error('tenant_id') is-invalid @enderror">
                                <option value="">Select Tenant Owner</option>
                                @foreach($tenantOwners as $tenantOwner)
                                    <option value="{{ $tenantOwner->id }}" {{ old('tenant_id', $masterItem->tenant_id) == $tenantOwner->id ? 'selected' : '' }}>{{ $tenantOwner->name }}</option>
                                @endforeach
                            </select>
                            @error('tenant_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="item_name">Item Name</label>
                            <input type="text" name="item_name" id="item_name" value="{{ old('item_name', $masterItem->item_name) }}"
                                   class="form-control @error('item_name') is-invalid @enderror" placeholder="Enter Item Name">
                            @error('item_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="item_code">Item Code</label>
                            <input type="text" name="item_code" id="item_code" value="{{ old('item_code', $masterItem->item_code) }}"
                                   class="form-control @error('item_code') is-invalid @enderror" placeholder="Enter Item Code">
                            @error('item_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="form-control @error('description') is-invalid @enderror" placeholder="Enter description">{{ old('description', $masterItem->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('master-item.index') }}" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
