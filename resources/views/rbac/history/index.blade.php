@extends('layouts.app')

@section('title', 'Activity History')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    /* Force side-by-side display for bottom start section */
    .dt-layout-cell.dt-start {
        display: flex !important;
        align-items: center;
        gap: 10px;
    }

    .dt-layout-cell.dt-start select {
        width: auto !important;
        min-width: 60px;
        padding-right: 30px !important;
        /* Ensure space for arrow */
    }

    /* Custom Search Input Style */
    .dt-search {
        position: relative;
    }

    .dt-search input {
        padding-left: 30px !important;
        /* Space for the icon */
        border-radius: 3px !important;
        /* Rounded corners */
    }

    .dt-search::before {
        content: "\f002";
        /* FontAwesome magnifying glass */
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        pointer-events: none;
        /* Let clicks pass through */
        z-index: 1;
    }
</style>
@stop

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
                                    <th>Tenant</th>
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
    <script type="text/javascript" src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize DataTable
            var table = $('#historyTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.history.data') }}",
                    type: 'GET',
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.user_filter = $('#user_filter').val();
                        d.action_filter = $('#action_filter').val();
                        d.table_filter = $('#table_filter').val();
                    },
                    error: function (xhr, error, thrown) {
                        console.error('DataTable AJAX Error:', xhr.responseText);
                        alert('Error loading data: ' + xhr.responseText);
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'tenant_name', name: 'tenant_name' },
                    { data: 'user_name', name: 'user_name' },
                    { data: 'action_badge', name: 'action_badge' },
                    { data: 'table_name_formatted', name: 'table_name_formatted' },
                    { data: 'record_id', name: 'record_id' },
                    { data: 'changes', name: 'changes' },
                    { data: 'ip_address', name: 'ip_address' },
                    { data: 'timestamp', name: 'timestamp' }
                ],
                pageLength: 25,
                responsive: true,
                order: [[8, 'desc']], // Sort by timestamp descending
                layout: {
                    topStart: 'search',
                    topEnd: 'buttons',
                    bottomStart: ['pageLength', 'info'],
                    bottomEnd: 'paging'
                },
                    buttons: [
                        {
                            extend: 'csv',
                            text: '<i class="fas fa-file-csv"></i> Export CSV',
                            className: 'btn btn-success'
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print"></i> Print',
                            className: 'btn btn-info'
                        }
                    ],
                    language: {
                        processing: '<i class="fas fa-spinner fa-spin"></i> Loading...',
                        lengthMenu: "_MENU_",
                        search: "",
                        searchPlaceholder: "Search"
                    },
                    initComplete: function () {
                        console.log('DataTable initialized');
                    }
                });

            // Filter functionality
            $('#filterBtn').click(function () {
                console.log('Filter button clicked');
                table.draw();
            });

            $('#resetBtn').click(function () {
                $('#start_date, #end_date, #user_filter, #action_filter, #table_filter').val('');
                table.draw();
            });

            // Auto-refresh every 30 seconds
            setInterval(function () {
                table.ajax.reload(null, false);
            }, 30000);
        });
    </script>
@endpush