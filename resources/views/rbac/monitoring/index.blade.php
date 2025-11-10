@extends('adminlte::page')

@section('title', 'Monitoring Pesan')

@section('page-title', 'Monitoring Pesan')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css"/>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Monitoring Pesan</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="monitoringTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Customer</th>
                                    <th>Total Report</th>
                                    <th>Total OCR</th>
                                    <th>Total Manual</th>
                                    <th>Total Finish</th>
                                    <th>Total Processing</th>
                                    <th>Total Failed</th>
                                    <th>Last Input</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->customer_name }}</td>
                                        <td>{{ $row->total_task_report }}</td>
                                        <td>{{ $row->total_task_ocr }}</td>
                                        <td>{{ $row->total_task_manual }}</td>
                                        <td>{{ $row->total_finish }}</td>
                                        <td>{{ $row->total_processing }}</td>
                                        <td>{{ $row->total_failed }}</td>
                                        <td>{{ $row->last_data_input }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-info view-btn" data-customer="{{ $row->customer_name }}" title="View Chart">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning process-btn" data-customer="{{ $row->customer_name }}" title="View Processing Tasks">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger failed-btn" data-customer="{{ $row->customer_name }}" title="View Failed Tasks">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </button>
                                            </div>
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

    <div class="modal fade" id="chartModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chart for <span id="chart-customer-name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="chart-filter">Filter by:</label>
                                <select class="form-control" id="chart-filter">
                                    <option value="hour">Hour</option>
                                    <option value="day">Day</option>
                                    <option value="week">Week</option>
                                    <option value="month">Month</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <canvas id="monitoringChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="processModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Processing Tasks for <span id="process-customer-name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped" id="processTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Task</th>
                                <th>Content</th>
                                <th>Status</th>
                                <th>DateTime</th>
                            </tr>
                        </thead>
                        <tbody id="process-tasks-body">
                            <tr>
                                <td colspan="5" class="text-center">No processing tasks found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="failedModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Failed Tasks for <span id="failed-customer-name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped" id="failedTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Task</th>
                                <th>Image</th>
                                <th>Detail</th>
                                <th>DateTime</th>
                            </tr>
                        </thead>
                        <tbody id="failed-tasks-body">
                            <tr>
                                <td colspan="5" class="text-center">No failed tasks found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            $('#monitoringTable').DataTable({
                columnDefs: [
                    {
                        targets: 9, // Action column (0-indexed, so column 10 is index 9)
                        width: '120px',
                        orderable: false,
                        className: 'text-center'
                    }
                ],
                autoWidth: false
            });

            var chart;
            var customerName;

            $(document).on('click', '.view-btn', function() {
                customerName = $(this).data('customer');
                $('#chart-customer-name').text(customerName);
                updateChart();
                $('#chartModal').modal('show');
            });

            $('#chart-filter').on('change', function() {
                updateChart();
            });

            $(document).on('click', '.process-btn', function() {
                var customerName = $(this).data('customer');
                $('#process-customer-name').text(customerName);

                $.ajax({
                    url: '{{ route("rbac.monitoring.process-tasks") }}',
                    type: 'GET',
                    data: {
                        customer_name: customerName
                    },
                    success: function(response) {
                        var html = '';
                        if (response.length > 0) {
                                response.forEach(function(task, index) {
                                    html += '<tr>';
                                    html += '<td>' + (index + 1) + '</td>';
                                    html += '<td>' + task.Task_Type + '</td>';
                                    var payload = JSON.parse(task.Payload);
                                    if (task.image && task.image.File_Base64) {
                                        html += '<td><img src="data:image/jpeg;base64,' + task.image.File_Base64 + '" width="100"></td>';
                                    } else {
                                        html += '<td>' + payload.text + '</td>';
                                    }
                                    html += '<td>' + task.Status + '</td>';
                                    html += '<td>' + task.Created_At + '</td>';
                                    html += '</tr>';
                            });
                        } else {
                            html = '<tr><td colspan="5" class="text-center">No processing tasks found.</td></tr>';
                        }
                        $('#process-tasks-body').html(html);
                        $('#processModal').modal('show');
                    }
                });
            });

            $(document).on('click', '.failed-btn', function() {
                var customerName = $(this).data('customer');
                $('#failed-customer-name').text(customerName);

                $.ajax({
                    url: '{{ route("rbac.monitoring.failed-tasks") }}',
                    type: 'GET',
                    data: {
                        customer_name: customerName
                    },
                    success: function(response) {
                        var html = '';
                        if (response.length > 0) {
                            response.forEach(function(task, index) {
                                html += '<tr>';
                                html += '<td>' + (index + 1) + '</td>';
                                html += '<td>' + task.TaskType + '</td>';
                                html += '<td><img src="data:image/jpeg;base64,' + task.image.File_Base64 + '" width="100"></td>';
                                html += '<td>' + task.Detail + '</td>';
                                html += '<td>' + task.Finish_At + '</td>';
                                html += '</tr>';
                            });
                        } else {
                            html = '<tr><td colspan="5" class="text-center">No failed tasks found.</td></tr>';
                        }
                        $('#failed-tasks-body').html(html);
                        $('#failedModal').modal('show');
                    }
                });
            });

            function updateChart() {
                var timeFrame = $('#chart-filter').val();

                $.ajax({
                    url: '{{ route("rbac.monitoring.chart-data") }}',
                    type: 'GET',
                    data: {
                        customer_name: customerName,
                        time_frame: timeFrame
                    },
                    success: function(response) {
                        var chartData = [
                            response.total_task_report,
                            response.total_task_ocr,
                            response.total_task_manual,
                            response.total_finish,
                            response.total_processing,
                            response.total_failed
                        ];

                        if (chart) {
                            chart.destroy();
                        }

                        var ctx = document.getElementById('monitoringChart').getContext('2d');
                        chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Total Report', 'Total OCR', 'Total Manual', 'Total Finish', 'Total Processing', 'Total Failed'],
                                datasets: [{
                                    label: 'Total',
                                    data: chartData,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    }
                });
            }
        });
    </script>
@endpush
