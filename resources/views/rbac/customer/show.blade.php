@extends('adminlte::page')

@section('title', 'Customer Detail')

@section('page-title', 'Customer Detail')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer Information</h3>
                    <div class="card-tools">
                        <a href="{{ route('rbac.customer') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>ID:</strong></label>
                                <p id="customer-id"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Nama Customer:</strong></label>
                                <p id="customer-name"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Kategory Bisnis:</strong></label>
                                <p id="customer-business-category"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Sub-Category Bisnis:</strong></label>
                                <p id="customer-sub-business-category"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Join Date:</strong></label>
                                <p id="customer-join-date"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Telegram ChatID:</strong></label>
                                <p id="customer-telegram-chat-id"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Created At:</strong></label>
                                <p id="customer-created-at"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Updated At:</strong></label>
                                <p id="customer-updated-at"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Get customer ID from URL
    var urlParams = new URLSearchParams(window.location.search);
    var customerId = urlParams.get('id');

    if (customerId) {
        // Fetch customer data
        $.ajax({
            url: "{{ url('rbac/customer') }}/" + customerId,
            type: 'GET',
            success: function(response) {
                $('#customer-id').text(response.id);
                $('#customer-name').text(response.name);
                $('#customer-business-category').text(response.business_category);
                $('#customer-sub-business-category').text(response.sub_business_category);
                $('#customer-join-date').text(response.join_date);
                $('#customer-telegram-chat-id').text(response.telegram_chat_id || '-');
                $('#customer-created-at').text(response.created_at);
                $('#customer-updated-at').text(response.updated_at);
            },
            error: function(xhr) {
                toastr.error('Failed to load customer data.');
            }
        });
    } else {
        toastr.error('Customer ID not found.');
    }
});
</script>
@stop
