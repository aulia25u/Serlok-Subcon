@extends('layouts.app')

@section('title', 'Plant')
@section('page-title', 'Plant')

{{-- Add the CSS links for DataTables and Bootstrap --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Plant Management</h3>
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

                    <table class="table table-bordered table-striped" id="plantTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Plant Name</th>
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
                <h5 class="modal-title" id="addModalLabel">Add New Plant</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="plantForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="plantId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="plant_name">Plant Name</label>
                        <input type="text" class="form-control" id="plant_name" name="plant_name" required>
                    </div>
                    <div class="form-group">
                        <label for="plant_code">Plant Code</label>
                        <input type="text" class="form-control" id="plant_code" name="plant_code" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
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

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable
    var table = $('#plantTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('rbac.plant') }}", // Corrected route name
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'plant_name', name: 'plant_name'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        pageLength: 10,
        responsive: true
    });

    // Filter functionality
    $('#filterBtn').click(function() {
        table.draw();
    });

    $('#resetBtn').click(function() {
        $('#start_date, #end_date').val('');
        table.draw();
    });

    // Handle "Add New" button click
    $('#addModal').on('show.bs.modal', function() {
        $('#plantForm').trigger('reset');
        $('#formMethod').val('POST');
        $('#addModalLabel').text('Add New Plant');
    });

    // Handle form submission for add/edit
    $('#plantForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#plantId').val();
        let url = id ? `/rbac/plant/${id}` : "{{ route('rbac.plant.store') }}";
        let type = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: type,
            data: $(this).serialize(),
            success: function(response) {
                $('#addModal').modal('hide');
                table.draw();
                alert(response.success);
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    alert(value[0]);
                });
            }
        });
    });

    // Handle "Edit" button click
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        let editUrl = `/rbac/plant/${id}/edit`; // Corrected URL

        $.ajax({
            url: editUrl,
            type: 'GET',
            success: function(response) {
                $('#addModalLabel').text('Edit Plant');
                $('#plantId').val(response.id);
                $('#plant_name').val(response.plant_name);
                $('#plant_code').val(response.plant_code);
                $('#location').val(response.location);
                $('#description').val(response.description);
                $('#formMethod').val('PUT');
                $('#addModal').modal('show');
            }
        });
    });

    // Handle "Delete" button click
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        let deleteUrl = `/rbac/plant/${id}`;

        if (confirm('Are you sure you want to delete this plant?')) {
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                success: function(response) {
                    table.draw();
                    alert(response.success);
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.error || 'Something went wrong.');
                }
            });
        }
    });
});
</script>
@endpush