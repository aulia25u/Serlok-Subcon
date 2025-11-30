@extends('layouts.app')

@section('title', 'Master Customer')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Master Customer</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#addModal">
                                <i class="fas fa-plus"></i> Add Master Customer
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="masterCustomerTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Customer</th>
                                    <th>Customer Name</th>
                                    <th>Customer Code</th>
                                    <th>Address</th>
                                    <th>NPWP</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add New Master Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="customer_id">Customer</label>
                            @if($isInternal)
                                <select name="customer_id" id="customer_id"
                                    class="form-control @error('customer_id') is-invalid @enderror" required>
                                    <option value="">Select Customer</option>
                                </select>
                            @else
                                <select name="customer_id" id="customer_id" class="form-control" disabled>
                                    <option value="{{ $currentCustomerId ?? '' }}">
                                        {{ optional($customers->first())->customer_name ?? 'My Customer' }}</option>
                                </select>
                                <input type="hidden" name="customer_id" value="{{ $currentCustomerId }}">
                            @endif
                            @error('customer_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="customer_name">Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name"
                                class="form-control @error('customer_name') is-invalid @enderror" required>
                            @error('customer_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="customer_code">Customer Code</label>
                            <input type="text" name="customer_code" id="customer_code"
                                class="form-control @error('customer_code') is-invalid @enderror" required>
                            @error('customer_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address"
                                class="form-control @error('address') is-invalid @enderror" rows="3" required></textarea>
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="npwp">NPWP</label>
                            <input type="text" name="npwp" id="npwp"
                                class="form-control @error('npwp') is-invalid @enderror" required>
                            @error('npwp')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Master Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_customer_id">Customer</label>
                            @if($isInternal)
                                <select name="customer_id" id="edit_customer_id" class="form-control" required>
                                    <option value="">Select Customer</option>
                                </select>
                            @else
                                <select name="customer_id" id="edit_customer_id" class="form-control" disabled>
                                    <option value="{{ $currentCustomerId ?? '' }}">
                                        {{ optional($customers->first())->customer_name ?? 'My Customer' }}</option>
                                </select>
                                <input type="hidden" name="customer_id" value="{{ $currentCustomerId }}">
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="edit_customer_name">Customer Name</label>
                            <input type="text" name="customer_name" id="edit_customer_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_customer_code">Customer Code</label>
                            <input type="text" name="customer_code" id="edit_customer_code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_address">Address</label>
                            <textarea name="address" id="edit_address" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_npwp">NPWP</label>
                            <input type="text" name="npwp" id="edit_npwp" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#masterCustomerTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.master-customer') }}",
                    cache: false
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'customer_code', name: 'customer_code' },
                    { data: 'address', name: 'address' },
                    { data: 'npwp', name: 'npwp' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                responsive: true,
                pageLength: 10,
            });

            const isInternalUser = @json($isInternal);
            const customerEndpoints = {
                all: '{{ route("rbac.customer.get") }}'
            };

            // Load customers for add modal
            const loadCustomers = function (target, selectedId = null) {
                if (!isInternalUser) {
                    return; // Skip loading for non-internal users
                }
                const endpoint = customerEndpoints.all;
                $.get(endpoint, function (response) {
                    let options = '<option value="">Select Customer</option>';
                    response.data.forEach(function (customer) {
                        const selected = selectedId && selectedId == customer.id ? 'selected' : '';
                        options += `<option value="${customer.id}" ${selected}>${customer.customer_name}</option>`;
                    });
                    target.html(options);
                });
            };

            $('#addModal').on('shown.bs.modal', function () {
                if (isInternalUser) {
                    loadCustomers($('#customer_id'));
                }
            });

            // Add form submission
            $('#addForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('rbac.master-customer.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        $('#addModal').modal('hide');
                        $('#addForm')[0].reset();
                        table.draw();
                        toastr.success('Master Customer created successfully');
                    },
                    error: function (xhr) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            toastr.error(value[0]);
                        });
                    }
                });
            });

            // Edit button click
            $('body').on('click', '.edit', function () {
                var customerId = $(this).data('id');
                $.ajax({
                    url: "{{ route('rbac.master-customer.edit', ':id') }}".replace(':id', customerId),
                    type: 'GET',
                    success: function (response) {
                        $('#edit_id').val(response.id);
                        $('#edit_customer_name').val(response.customer_name);
                        $('#edit_customer_code').val(response.customer_code);
                        $('#edit_address').val(response.address);
                        $('#edit_npwp').val(response.npwp);
                        if (isInternalUser) {
                            loadCustomers($('#edit_customer_id'), response.customer_id);
                        }
                        $('#editModal').modal('show');
                    }
                });
            });

            // Edit form submission
            $('#editForm').on('submit', function (e) {
                e.preventDefault();
                var id = $('#edit_id').val();
                $.ajax({
                    url: "{{ route('rbac.master-customer.update', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: $(this).serialize() + '&_method=PUT',
                    success: function (response) {
                        $('#editModal').modal('hide');
                        table.draw();
                        toastr.success('Master Customer updated successfully');
                    },
                    error: function (xhr) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            toastr.error(value[0]);
                        });
                    }
                });
            });

            // Delete button click
            $('body').on('click', '.delete', function () {
                var customerId = $(this).data('id');
                if (confirm('Are you sure you want to delete this customer?')) {
                    $.ajax({
                        url: "{{ route('rbac.master-customer.destroy', ':id') }}".replace(':id', customerId),
                        type: 'POST',
                        data: {
                            _method: 'DELETE'
                        },
                        success: function () {
                            table.ajax.reload();
                            toastr.success('Master Customer deleted successfully');
                        },
                        error: function () {
                            toastr.error('Failed to delete the Master Customer');
                        }
                    });
                }
            });
        });
    </script>
@endpush