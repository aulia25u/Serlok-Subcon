@extends('layouts.app')

@section('title', 'View Master Item')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Master Item Details</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <strong>Tenant:</strong>
                        {{ $masterItem->tenantOwner ? $masterItem->tenantOwner->name : 'N/A' }}
                    </div>
                    <div class="form-group">
                        <strong>Item Name:</strong>
                        {{ $masterItem->item_name }}
                    </div>
                    <div class="form-group">
                        <strong>Item Code:</strong>
                        {{ $masterItem->item_code }}
                    </div>
                    <div class="form-group">
                        <strong>Description:</strong>
                        {{ $masterItem->description }}
                    </div>
                    <div class="form-group">
                        <strong>Created At:</strong>
                        {{ $masterItem->created_at }}
                    </div>
                    <div class="form-group">
                        <strong>Updated At:</strong>
                        {{ $masterItem->updated_at }}
                    </div>
                    <a href="{{ route('master-item.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
