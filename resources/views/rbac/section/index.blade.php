@extends('layouts.app')

@section('title', 'Section')
@section('page-title', 'Section')

{{-- Add the CSS links for DataTables and Bootstrap --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Section Management</h3>
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

                    <table class="table table-bordered table-striped" id="sectionTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Section Name</th>
                                <th>Department</th>
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
                <h5 class="modal-title" id="addModalLabel">Add New Section</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="sectionForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="sectionId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="section_name">Section Name</label>
                        <input type="text" class="form-control" id="section_name" name="section_name" required>
                    </div>
                    <div class="form-group">
                        <label for="dept_id">Department</label>
                        <select class="form-control" id="dept_id" name="dept_id" required>
                            <option value="">Select Department</option>
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
    var table = $('#sectionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('rbac.section') }}",
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            {data: 'no', name: 'no', orderable: false, searchable: false},
            {data: 'section_name', name: 'section_name'},
            {data: 'dept_name', name: 'dept_name'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        pageLength: 10,
        responsive: true
    });

    // Populate departments dropdown
    function populateDepartments() {
        $.ajax({
            url: "{{ route('rbac.departments.all') }}", // This route needs to be defined
            method: 'GET',
            success: function(response) {
                let options = '<option value="">Select Department</option>';
                response.forEach(function(dept) {
                    options += `<option value="${dept.id}">${dept.dept_name}</option>`;
                });
                $('#dept_id').html(options);
            }
        });
    }

    populateDepartments();

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
        $('#addModalLabel').text('Add New Section');
    });

    // Handle form submission for add/edit
    $('#sectionForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#sectionId').val();
        let url = id ? `/rbac/section/${id}` : "{{ route('rbac.section.store') }}";
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
                $('#sectionForm')[0].reset();
                $('#sectionId').val('');
                $('#formMethod').val('POST');
                $('#dept_id').val('');
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
        let editUrl = `/rbac/section/${id}/edit`;

        $.ajax({
            url: editUrl,
            type: 'GET',
            success: function(response) {
                $('#addModalLabel').text('Edit Section');
                $('#sectionId').val(response.id);
                $('#section_name').val(response.section_name);
                $('#dept_id').val(response.dept_id);
                $('#description').val(response.description);
                $('#formMethod').val('PUT');
                $('#addModal').modal('show');
            },
            error: function(xhr) {
                toastr.error('Failed to load section data for editing.');
            }
        });
    });

    // Reset modal when opening for add
    $('#addBtn').on('click', function() {
        $('#addModalLabel').text('Add New Section');
        $('#section_name').val('');
        $('#dept_id').val('');
        $('#sectionId').val('');
        $('#formMethod').val('POST');
    });

    // Handle "Delete" button click
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        let deleteUrl = `/rbac/section/${id}`;

        if (confirm('Are you sure you want to delete this section?')) {
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