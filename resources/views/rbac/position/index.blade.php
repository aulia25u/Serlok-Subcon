@extends('layouts.app')

@section('title', 'Position')

@section('css')
    {{-- Include the necessary CSS files --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
@php
    $isInternal = is_null($currentCustomerId ?? null);
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Position Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                            <i class="fas fa-plus"></i> Add New
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

                    <table class="table table-bordered table-striped" id="positionTable">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Position Name</th>
                            <th>Section</th>
                            <th>Tenant</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add New Position</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="positionForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="positionId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="position_name">Position Name</label>
                        <input type="text" class="form-control" id="position_name" name="position_name" required>
                    </div>
                    <div class="form-group">
                        <label for="position_customer_id">Tenant</label>
                        @if($isInternal)
                            <select class="form-control" id="position_customer_id" name="customer_id">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="form-control" id="position_customer_id" name="customer_id" disabled>
                                <option value="{{ $currentCustomerId ?? '' }}">{{ optional($customers->first())->customer_name ?? 'My Customer' }}</option>
                            </select>
                            <input type="hidden" name="customer_id" value="{{ $currentCustomerId }}">
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="section_id">Section</label>
                        <select class="form-control" id="section_id" name="section_id" required>
                            <option value="">Select Section</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
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
{{-- Ensure these are loaded after jQuery and before your custom script --}}
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

    // Initialize DataTable
    var table = $('#positionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('rbac.position') }}",
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            {data: 'no', name: 'no', orderable: false, searchable: false},
            {data: 'position_name', name: 'position_name'},
            {data: 'section_name', name: 'section_name'},
            {data: 'customer', name: 'customer'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        pageLength: 10,
        responsive: true
    });

    function loadSections(customerId, selectedId = null) {
        if (!customerId) {
            $('#section_id').html('<option value="">Select Section</option>');
            return;
        }

        const url = "{{ route('rbac.sections.by-customer', ['customer_id' => ':customer']) }}".replace(':customer', customerId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                let options = '<option value="">Select Section</option>';
                response.forEach(function(section) {
                    const selected = selectedId && selectedId == section.id ? 'selected' : '';
                    options += `<option value="${section.id}" ${selected}>${section.section_name}</option>`;
                });
                $('#section_id').html(options);
            }
        });
    }

    $('#position_customer_id').on('change', function() {
        loadSections($(this).val());
    });

    if ($('#position_customer_id').val()) {
        loadSections($('#position_customer_id').val());
    }

    // Filter functionality
    $('#filterBtn').click(function() {
        table.draw();
    });

    $('#resetBtn').click(function() {
        $('#start_date, #end_date').val('');
        table.draw();
    });

    // Reset modal title when modal is hidden
    $('#addModal').on('hidden.bs.modal', function() {
        $('#addModalLabel').text('Add New Position');
    });

    // Handle form submission for add/edit
    $('#positionForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#positionId').val();
        let url = id ? `/rbac/position/${id}` : "{{ route('rbac.position.store') }}";
        let type = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: type,
            data: $(this).serialize(),
            success: function(response) {
                $('#addModal').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                table.draw();
                toastr.success(response.success);
                $('#positionForm')[0].reset();
                $('#positionId').val('');
                $('#formMethod').val('POST');
                loadSections($('#position_customer_id').val());
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    toastr.error(value[0]);
                });
            }
        });
    });

    // Handle "Edit" button click
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        let editUrl = `/rbac/position/${id}/edit`; // Changed URL to be more RESTful

        $.ajax({
            url: editUrl,
            type: 'GET',
            success: function(response) {
                $('#addModalLabel').text('Edit Position');
                $('#positionId').val(response.id);
                $('#position_name').val(response.position_name);
                $('#position_customer_id').val(response.customer_id);
                loadSections(response.customer_id, response.section_id);
                $('#description').val(response.description);
                $('#formMethod').val('PUT');
                $('#addModal').modal('show');
            },
            error: function(xhr) {
                toastr.error('Failed to load position data for editing.');
            }
        });
    });

    // Reset modal when opening for add
    $('#addBtn').on('click', function() {
        $('#addModalLabel').text('Add New Position');
        $('#position_name').val('');
        $('#section_id').val('');
        $('#positionId').val('');
        $('#formMethod').val('POST');
    });

    // Handle "Delete" button click
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        let deleteUrl = `/rbac/position/${id}`;

        if (confirm('Are you sure you want to delete this position?')) {
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                success: function(response) {
                    table.draw();
                    toastr.success(response.success);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.error || 'Something went wrong.');
                }
            });
        }
    });
});
</script>
@endpush
