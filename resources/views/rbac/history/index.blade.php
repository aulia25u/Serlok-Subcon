@extends('layouts.app')

@section('title', 'Activity History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Activity History</h3>
                    @if(isset($totalLogs))
                        <div class="card-tools">
                            <small class="text-muted">Total Logs: {{ $totalLogs }}</small>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="start_date">Start Date:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        <div class="col-md-2">
                            <label for="end_date">End Date:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                        <div class="col-md-2">
                            <label for="user_filter">User:</label>
                            <select class="form-control" id="user_filter" name="user_filter">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="action_filter">Action:</label>
                            <select class="form-control" id="action_filter" name="action_filter">
                                <option value="">All Actions</option>
                                <option value="create">Create</option>
                                <option value="update">Update</option>
                                <option value="delete">Delete</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="table_filter">Module:</label>
                            <select class="form-control" id="table_filter" name="table_filter">
                                <option value="">All Modules</option>
                                <option value="user_details">User Data</option>
                                <option value="depts">Department</option>
                                <option value="sections">Section</option>
                                <option value="positions">Position</option>
                                <option value="roles">Role</option>
                            </select>
                        </div>
                        <div class="col-md-2">
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



                    <!-- DataTable -->
                    <table class="table table-bordered table-striped" id="historyTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Record ID</th>
                                <th>Changes</th>
                                <th>IP Address</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#historyTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('rbac.history.data') }}",
            type: 'GET',
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.user_filter = $('#user_filter').val();
                d.action_filter = $('#action_filter').val();
                d.table_filter = $('#table_filter').val();
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX Error:', xhr.responseText);
                alert('Error loading data: ' + xhr.responseText);
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'user_name', name: 'user_name'},
            {data: 'action_badge', name: 'action_badge'},
            {data: 'table_name_formatted', name: 'table_name_formatted'},
            {data: 'record_id', name: 'record_id'},
            {data: 'changes', name: 'changes'},
            {data: 'ip_address', name: 'ip_address'},
            {data: 'timestamp', name: 'timestamp'}
        ],
        pageLength: 25,
        responsive: true,
        order: [[7, 'desc']], // Sort by timestamp descending
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading...'
        },
        initComplete: function() {
            console.log('DataTable initialized');
        }
    });

    // Filter functionality
    $('#filterBtn').click(function() {
        console.log('Filter button clicked');
        table.draw();
    });

    $('#resetBtn').click(function() {
        $('#start_date, #end_date, #user_filter, #action_filter, #table_filter').val('');
        table.draw();
    });

    // Auto-refresh every 30 seconds
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 30000);
});
</script>
@endpush
