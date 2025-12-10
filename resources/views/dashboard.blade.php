@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Dashboard</h1>
@stop

@section('content')
<div class="container-fluid">
    @if($viewType == 'internal_admin')
        <!-- Internal Admin View -->
        <!-- Top Metrics: 4 Columns -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $data['total_tenants'] }}</h3>
                        <p>Total Tenants</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $data['total_users'] }}</h3>
                        <p>Total Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $data['active_outgoings_30d'] }}</h3>
                        <p>Active Outgoings (30d)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dolly"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $data['total_jobs_all_time'] }}</h3>
                        <p>Total Production Jobs</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-industry"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Production & System Info -->
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="card card-outline card-primary h-100">
                    <div class="card-header">
                        <h3 class="card-title">System Production Overview (7 Days)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="productionChart"
                            style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-outline card-info h-100">
                    <div class="card-header">
                        <h3 class="card-title">Server Information</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover">
                            <tr>
                                <th>PHP Version</th>
                                <td>{{ $data['server_info']['php_version'] }}</td>
                            </tr>
                            <tr>
                                <th>Laravel</th>
                                <td>{{ $data['server_info']['laravel_version'] }}</td>
                            </tr>
                            <tr>
                                <th>OS</th>
                                <td>{{ $data['server_info']['server_os'] }}</td>
                            </tr>
                            <tr>
                                <th>Database</th>
                                <td>{{ ucfirst($data['server_info']['database_connection']) }}</td>
                            </tr>
                            <tr>
                                <th>System Load</th>
                                <td>{{ $data['server_info']['load_average'] }}</td>
                            </tr>
                            <tr>
                                <th>Server Uptime</th>
                                <td>{{ $data['server_info']['server_uptime'] }}</td>
                            </tr>
                            <tr>
                                <th>Disk Usage</th>
                                <td>{{ $data['server_info']['disk_usage'] }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Growth & Environment -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title">Tenant Growth (12 Months)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="tenantGrowthChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h3 class="card-title">Client OS</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="clientStatsChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-outline card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Client Browser</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="clientBrowserChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 4: Productivity Insights -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Top 5 Active Tenants (Jobs - 30d)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped text-sm">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Tenant Name</th>
                                    <th style="width: 100px">Jobs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['top_tenants'] as $index => $tenant)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $tenant->customer_name }}</td>
                                        <td><span class="badge bg-primary">{{ $tenant->total_jobs }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title">Top 5 Performing Users (Jobs - 30d)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped text-sm">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>User Name</th>
                                    <th style="width: 100px">Jobs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['active_users'] as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td><span class="badge bg-success">{{ $user->total_jobs }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 5: Login Activity -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Top Login IP</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['top_ips'] as $ip)
                                    <tr>
                                        <td>{{ $ip->ip_address }}</td>
                                        <td><span class="badge bg-info">{{ $ip->total }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Top Login Users</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Logins</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['user_logins'] as $ul)
                                    <tr>
                                        <td>{{ $ul->name }}</td>
                                        <td><span class="badge bg-info">{{ $ul->total_logins }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Top Login Tenants</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Tenant</th>
                                    <th>Logins</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['tenant_logins'] as $tl)
                                    <tr>
                                        <td>{{ $tl->customer_name }}</td>
                                        <td><span class="badge bg-info">{{ $tl->total_logins }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 6: Recent Audit Logs -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Recent System Activity Logs</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="maximize"><i
                                    class="fas fa-expand"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0 table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Target</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data['recent_audit_logs'] as $log)
                                        <tr>
                                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $log->user->name ?? 'System/Guest' }}</td>
                                            <td>{!! $log->action_badge !!}</td>
                                            <td>{{ $log->table_name_formatted }} (ID: {{ $log->record_id }})</td>
                                            <td>{{ $log->ip_address }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No logs found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <a href="/rbac/history" class="btn btn-sm btn-secondary float-right">View All Activities</a>
                    </div>
                </div>
            </div>
        </div>

    @elseif($viewType == 'tenant_owner')
        <!-- Tenant Owner View (Refined) -->
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cubes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Items</span>
                        <span class="info-box-number">{{ $data['total_items'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box shadow-sm mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-tasks"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Jobs (This Month)</span>
                        <span class="info-box-number">{{ $data['monthly_jobs'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box shadow-sm mb-3">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-truck-loading"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending SJ (Draft)</span>
                        <span class="info-box-number">{{ $data['pending_surat_jalan'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box shadow-sm mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Low Stock Items</span>
                        <span class="info-box-number">{{ $data['low_stock_count'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Production Quality (Last 7 Days)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="productionChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Tenant Staff View (Unchanged) -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Welcome, {{ Auth::user()->name }}!</h3>
                    </div>
                    <div class="card-body">
                        <p class="lead">Here is your recent activity.</p>

                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Item</th>
                                    <th>Qty OK</th>
                                    <th>Qty NG</th>
                                    <th>Inspector</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['my_recent_jobs'] as $job)
                                    <tr>
                                        <td>{{ $job->created_datetime->format('Y-m-d') }}</td>
                                        <td>{{ $job->outgoing->masterItem->item_name ?? '-' }}</td>
                                        <td>{{ $job->qty_ok }}</td>
                                        <td>{{ $job->qty_ng }}</td>
                                        <td>{{ $job->inspector->name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No recent activity found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function () {
        @if(isset($data['chart_data']))
            // Production Chart
            var ctx = document.getElementById('productionChart').getContext('2d');
            var chartData = @json($data['chart_data']);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'OK Quantity',
                            backgroundColor: 'rgba(40, 167, 69, 0.9)',
                            borderColor: 'rgba(40, 167, 69, 0.8)',
                            data: chartData.ok
                        },
                        {
                            label: 'NG Quantity',
                            backgroundColor: 'rgba(220, 53, 69, 0.9)',
                            borderColor: 'rgba(220, 53, 69, 0.8)',
                            data: chartData.ng
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        @endif

            @if(isset($data['tenant_growth']))
                // Tenant Growth Chart
                var ctxGrowth = document.getElementById('tenantGrowthChart').getContext('2d');
                var growthData = @json($data['tenant_growth']);

                new Chart(ctxGrowth, {
                    type: 'line',
                    data: {
                        labels: growthData.labels,
                        datasets: [
                            {
                                label: 'Active',
                                backgroundColor: 'rgba(40, 167, 69, 0.9)',
                                borderColor: 'rgba(40, 167, 69, 0.8)',
                                pointRadius: false,
                                pointColor: '#3b8bba',
                                pointStrokeColor: 'rgba(60,141,188,1)',
                                pointHighlightFill: '#fff',
                                pointHighlightStroke: 'rgba(60,141,188,1)',
                                data: growthData.active
                            },
                            {
                                label: 'Inactive',
                                backgroundColor: 'rgba(210, 214, 222, 1)',
                                borderColor: 'rgba(210, 214, 222, 1)',
                                pointRadius: false,
                                pointColor: 'rgba(210, 214, 222, 1)',
                                pointStrokeColor: '#c1c7d1',
                                pointHighlightFill: '#fff',
                                pointHighlightStroke: 'rgba(220,220,220,1)',
                                data: growthData.inactive
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            @endif

            @if(isset($data['client_stats']))
                // Client Stats Chart
                var ctxClient = document.getElementById('clientStatsChart').getContext('2d');
                var clientData = @json($data['client_stats']);

                new Chart(ctxClient, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(clientData),
                        datasets: [{
                            data: Object.values(clientData),
                            backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right',
                            }
                        }
                    }
                });
            @endif

            @if(isset($data['client_browser_stats']))
                // Client Browser Chart
                var ctxBrowser = document.getElementById('clientBrowserChart').getContext('2d');
                var browserData = @json($data['client_browser_stats']);

                new Chart(ctxBrowser, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(browserData),
                        datasets: [{
                            data: Object.values(browserData),
                            backgroundColor: ['#00c0ef', '#3c8dbc', '#d2d6de', '#f56954', '#00a65a', '#f39c12'],
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right',
                            }
                        }
                    }
                });
            @endif
        });
</script>
@stop