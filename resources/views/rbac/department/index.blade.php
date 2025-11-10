@extends('layouts.app')

@section('title', 'Department Management')
@section('page-title', 'Department Management')

{{-- You need to include the necessary CSS for DataTables --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Department Management</h3>
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

                    <table class="table table-bordered table-striped" id="departmentTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Department Name</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle">Add Department</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="dept_name">Department Name:</label>
                        <input type="text" class="form-control" id="dept_name" name="dept_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_dept_name">Department Name:</label>
                        <input type="text" class="form-control" id="edit_dept_name" name="dept_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    {{-- You need to include the necessary JavaScript files --}}
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
    $(document).ready(function() {
        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#departmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('rbac.department') }}",
                data: function(d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                {data: 'no', name: 'no', orderable: false, searchable: false},
                {data: 'dept_name', name: 'dept_name'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        });

        // Filter functionality
        $('#filterBtn').click(function() {
            table.draw();
        });

        $('#resetBtn').click(function() {
            $('#start_date').val('');
            $('#end_date').val('');
            table.draw();
        });

        // Add form submission
        $('#addForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('rbac.department.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#addModal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    table.draw();
                    toastr.success(response.success);
                    $('#addForm')[0].reset();
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                }
            });
        });

        // Edit button click
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var editUrl = "{{ route('rbac.department.edit', ':id') }}".replace(':id', id);

            $.ajax({
                url: editUrl,
                type: 'GET',
                success: function(response) {
                    $('#edit_id').val(response.id);
                    $('#edit_dept_name').val(response.dept_name);
                    $('#editModal').modal('show');
                }
            });
        });

        // Edit form submission
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#edit_id').val();
            var updateUrl = "{{ route('rbac.department.update', ':id') }}".replace(':id', id);

            $.ajax({
                url: updateUrl,
                type: 'PUT',
                data: $(this).serialize(),
                success: function(response) {
                    $('#editModal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    table.draw();
                    toastr.success(response.success);
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                }
            });
        });

        // Delete button click
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            var deleteUrl = "{{ route('rbac.department.destroy', ':id') }}".replace(':id', id);

            if (confirm('Are you sure you want to delete this department?')) {
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
@endsection