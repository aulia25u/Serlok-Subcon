@extends('adminlte::page')

@section('title', 'POS Monitoring')

@section('page-title', 'POS Monitoring')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@stop

@section('content')
    <div class="container-fluid">
        <!-- Sync Data Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Schedule POS Data Sync</h3>
                    </div>
                    <div class="card-body">
                        <form id="posSyncForm" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="customer_id">Nama Customer</label>
                                        <select class="form-control" id="customer_id" name="customer_id" required>
                                            <option value="">Pilih Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">
                                                    {{ $customer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="schedule_type">Schedule Run</label>
                                        <select class="form-control" id="schedule_type" name="schedule_type" required>
                                            <option value="daily">Daily</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="schedule_time">Time Running</label>
                                        <input type="time" class="form-control" id="schedule_time" name="schedule_time" value="23:00" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block" id="submitBtn">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <!-- POS Monitoring Queue Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">POS Monitoring Queue</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="posMonitoringTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Customer</th>
                                    <th>Schedule Type</th>
                                    <th>Schedule Time</th>
                                    <th>Next Run</th>
                                    <th>Last Run</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $scheduledQueues = $posQueueData->where('is_scheduled', true)->sortByDesc('created_at');
                                @endphp
                                @foreach($scheduledQueues as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->customer_name }}</td>
                                        <td>{{ $row->is_scheduled ? 'Daily' : 'Manual' }}</td>
                                        <td>{{ $row->schedule_time ?? '-' }}</td>
                                        <td>
                                            @if($row->is_scheduled && $row->schedule_time)
                                                @php
                                                    $nextRun = \Carbon\Carbon::today()->setTimeFromTimeString($row->schedule_time);
                                                    if ($nextRun->isPast()) {
                                                        $nextRun = $nextRun->addDay();
                                                    }
                                                @endphp
                                                {{ $nextRun->format('d-m-Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $row->last_run ? $row->last_run->format('d-m-Y H:i') : '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $row->status == 'completed' ? 'success' : ($row->status == 'processing' ? 'warning' : ($row->status == 'failed' ? 'danger' : ($row->status == 'scheduled' ? 'info' : 'secondary'))) }}">
                                                {{ ucfirst($row->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info view-data-btn" data-id="{{ $row->id }}" data-customer="{{ $row->customer_name }}">Lihat Data</button>
                                            <button type="button" class="btn btn-sm btn-danger delete-queue-btn" data-id="{{ $row->id }}" data-customer="{{ $row->customer_name }}" data-type="schedule">Delete Schedule</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for viewing POS data -->
    <div class="modal fade" id="posDataModal" tabindex="-1" role="dialog" aria-labelledby="posDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="posDataModalLabel">Data POS - <span id="modalCustomerName"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="posDataTableModal" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Customer Name</th>
                                    <th>Start DateTime</th>
                                    <th>End DateTime</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="posDataTableBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <script>
            $(document).ready(function() {
                toastr.success('{{ session('success') }}');
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            $(document).ready(function() {
                toastr.error('{{ session('error') }}');
            });
        </script>
    @endif
@stop

@push('js')
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#posDataTable').DataTable();
            $('#posMonitoringTable').DataTable();

            $('#posSyncForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var submitBtn = $('#submitBtn');
                var originalText = submitBtn.text();

                // Disable button and show loading
                submitBtn.prop('disabled', true).text('Processing...');

                $.ajax({
                    url: '{{ route("rbac.pos-monitoring.store") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.success);
                            // Reload page after success
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'An error occurred while processing your request.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        // Re-enable button
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });



            // Handle View Data button clicks
            $('.view-data-btn').on('click', function() {
                var posQueueId = $(this).data('id');
                var customerName = $(this).data('customer');

                $('#modalCustomerName').text(customerName);
                $('#posDataModal').modal('show');

                // Load POS data via AJAX
                $.ajax({
                    url: '{{ route("rbac.pos-monitoring.get-pos-data") }}',
                    type: 'GET',
                    data: {
                        id: posQueueId
                    },
                    success: function(response) {
                        var tbody = $('#posDataTableBody');
                        tbody.empty();

                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(item) {
                                var statusBadge = '';
                                switch(item.status.toLowerCase()) {
                                    case 'completed':
                                        statusBadge = '<span class="badge badge-success">Completed</span>';
                                        break;
                                    case 'processing':
                                        statusBadge = '<span class="badge badge-warning">Processing</span>';
                                        break;
                                    case 'failed':
                                        statusBadge = '<span class="badge badge-danger">Failed</span>';
                                        break;
                                    case 'pending':
                                        statusBadge = '<span class="badge badge-secondary">Pending</span>';
                                        break;
                                    default:
                                        statusBadge = '<span class="badge badge-secondary">' + item.status + '</span>';
                                }

                                var row = '<tr>' +
                                    '<td>' + item.no + '</td>' +
                                    '<td>' + item.customer_name + '</td>' +
                                    '<td>' + item.start_datetime + '</td>' +
                                    '<td>' + item.end_datetime + '</td>' +
                                    '<td>' + statusBadge + '</td>' +
                                    '<td><a href="' + item.report_url + '" class="btn btn-sm btn-info" target="_blank">Lihat Data</a></td>' +
                                    '</tr>';
                                tbody.append(row);
                            });
                        } else {
                            tbody.append('<tr><td colspan="6" class="text-center">No data available</td></tr>');
                        }
                    },
                    error: function(xhr) {
                        var tbody = $('#posDataTableBody');
                        tbody.empty();
                        tbody.append('<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>');
                        console.error('Error loading POS data:', xhr);
                    }
                });
            });

            // Handle Delete Queue button clicks
            $('.delete-queue-btn').on('click', function() {
                var posQueueId = $(this).data('id');
                var customerName = $(this).data('customer');
                var type = $(this).data('type');
                var confirmMessage = type === 'schedule'
                    ? 'Are you sure you want to delete the schedule for ' + customerName + '?'
                    : 'Are you sure you want to delete this POS queue entry for ' + customerName + '?';

                if (confirm(confirmMessage)) {
                    $.ajax({
                        url: '{{ route("rbac.pos-monitoring.delete-queue") }}',
                        type: 'DELETE',
                        data: {
                            id: posQueueId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.success);
                                // Reload page after success
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            }
                        },
                        error: function(xhr) {
                            var errorMessage = 'An error occurred while deleting the queue entry.';
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            }
                            toastr.error(errorMessage);
                        }
                    });
                }
            });


        });
    </script>


@endpush
