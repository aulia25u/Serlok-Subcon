@extends('layouts.app')

@section('title', 'Master Item')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Master Item Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('master-item.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Master Item
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="masterItemTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tenant</th>
                                <th>Item Name</th>
                                <th>Item Code</th>
                                <th>Description</th>
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

    var table = $('#masterItemTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('master-item.index') }}",
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'tenant_name', name: 'tenant_name'},
            {data: 'item_name', name: 'item_name'},
            {data: 'item_code', name: 'item_code'},
            {data: 'description', name: 'description'},
            {data: 'created_at', name: 'created_at'},
            {data: 'updated_at', name: 'updated_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        responsive: true,
        pageLength: 10,
    });

    $('body').on('click', '.delete', function () {
        var itemId = $(this).data('id');
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: "{{ route('master-item.index') }}" + '/' + itemId,
                type: 'DELETE',
                success: function () {
                    table.draw();
                    toastr.success('Master Item deleted successfully');
                },
                error: function () {
                    toastr.error('Failed to delete the Master Item');
                }
            });
        }
    });
});
</script>
@endpush
