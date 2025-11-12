@extends('layouts.app')

@section('title', 'Tenant Users')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tenant Users (Owners)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ownerModal">
                            <i class="fas fa-plus"></i> Assign Owner
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="start_date">Start Date:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date">End Date:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-info" id="filterBtn">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <button type="button" class="btn btn-secondary" id="resetBtn">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="ownerTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Owner</th>
                                <th>Email</th>
                                <th>Tenant</th>
                                <th>Status</th>
                                <th>Assigned At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ownerModal" tabindex="-1" role="dialog" aria-labelledby="ownerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ownerModalLabel">Assign Tenant Owner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="ownerForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="ownerId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="owner_user_id">Owner User</label>
                        <select class="form-control" id="owner_user_id" name="user_id" required>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username }} ({{ $user->email ?? 'no email' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="owner_customer_id">Tenant</label>
                        <select class="form-control" id="owner_customer_id" name="customer_id" required>
                            <option value="">Select Tenant</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="owner_active" checked>
                        <label class="form-check-label" for="owner_active">Active</label>
                    </div>
                    <input type="hidden" name="is_active" id="owner_active_value" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var table = $('#ownerTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('rbac.tenant-owner') }}",
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'owner_name', name: 'owner_name'},
            {data: 'owner_email', name: 'owner_email'},
            {data: 'tenant', name: 'tenant'},
            {data: 'status', name: 'status', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        pageLength: 10,
        responsive: true
    });

    $('#filterBtn').click(function() {
        table.draw();
    });

    $('#resetBtn').click(function() {
        $('#start_date, #end_date').val('');
        table.draw();
    });

    $('#ownerModal').on('hidden.bs.modal', function() {
        $('#ownerForm')[0].reset();
        $('#ownerId').val('');
        $('#formMethod').val('POST');
        $('#owner_active').prop('checked', true);
        $('#owner_active_value').val(1);
    });

    $('#owner_active').on('change', function() {
        $('#owner_active_value').val($(this).is(':checked') ? 1 : 0);
    });

    $('#ownerForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#ownerId').val();
        let url = id ? `/rbac/tenant-owner/${id}` : "{{ route('rbac.tenant-owner.store') }}";
        let method = id ? 'PUT' : 'POST';
        $('#owner_active_value').val($('#owner_active').is(':checked') ? 1 : 0);

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#ownerModal').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                table.draw();
                toastr.success(response.success);
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    toastr.error(xhr.responseJSON.error);
                } else {
                    toastr.error('Failed to save tenant owner.');
                }
            }
        });
    });

    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ route('rbac.tenant-owner.edit', ':id') }}".replace(':id', id),
            type: 'GET',
            success: function(response) {
                $('#ownerModalLabel').text('Edit Tenant Owner');
                $('#ownerId').val(response.id);
                $('#owner_user_id').val(response.user_id);
                $('#owner_customer_id').val(response.customer_id);
                $('#owner_active').prop('checked', response.is_active);
                $('#owner_active_value').val(response.is_active ? 1 : 0);
                $('#formMethod').val('PUT');
                $('#ownerModal').modal('show');
            },
            error: function() {
                toastr.error('Failed to load tenant owner data.');
            }
        });
    });

    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to remove this tenant owner?')) {
            $.ajax({
                url: "{{ route('rbac.tenant-owner.destroy', ':id') }}".replace(':id', id),
                type: 'DELETE',
                success: function(response) {
                    table.draw();
                    toastr.success(response.success);
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        toastr.error(xhr.responseJSON.error);
                    } else {
                        toastr.error('Failed to delete tenant owner.');
                    }
                }
            });
        }
    });
});
</script>
@endpush
