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
                                        <th>Option</th>
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
                                            <td>
                                                <button class="btn btn-xs btn-info view-log-details" data-id="{{ $log->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
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
                <!-- Row 1 -->
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-building"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Plants</span>
                            <span class="info-box-number">{{ $data['total_plants'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Employees</span>
                            <span class="info-box-number">{{ $data['total_employees'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cubes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Items</span>
                            <span class="info-box-number">{{ $data['total_items'] }}</span>
                        </div>
                    </div>
                </div>
                <!-- Row 2 -->
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-download"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Receiving</span>
                            <span class="info-box-number">{{ $data['total_receiving'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-warehouse"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Outstanding</span>
                            <span class="info-box-number">{{ $data['total_outstanding'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-upload"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Outgoing</span>
                            <span class="info-box-number">{{ $data['total_outgoing'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Inventory Chart (Incoming vs Outgoing) -->
                <div class="col-md-6">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Inventory Flow</h3>
                            <div class="card-tools d-flex align-items-center">
                                <select class="form-control form-control-sm mr-2" id="inventoryPeriod" style="width: auto;">
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                        class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart" style="position: relative; height: 250px;">
                                <canvas id="inventoryChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Production Quality Chart -->
                <div class="col-md-6">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Production Quality</h3>
                            <div class="card-tools d-flex align-items-center">
                                <select class="form-control form-control-sm mr-2" id="productionPeriod" style="width: auto;">
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                        class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart" style="position: relative; height: 250px;">
                                <canvas id="productionChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>



        <!-- Top Defects & OK Row -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-outline card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Top 5 Defect Items</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="topDefectChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title">Top 5 OK Items</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="topOkChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Performance Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-purple">
                    <div class="card-header">
                        <h3 class="card-title">Employee Performance (Assigned Items)</h3>
                        <div class="card-tools">
                            <div class="form-inline">
                                <label class="mr-2">Limit:</label>
                                <select id="performanceLimit" class="form-control form-control-sm mr-3">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                        class="fas fa-minus"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-2 row">
                            <div class="col-md-4">
                                <input type="date" id="perfStartDate" class="form-control form-control-sm"
                                    placeholder="Start Date">
                            </div>
                            <div class="col-md-4">
                                <input type="date" id="perfEndDate" class="form-control form-control-sm" placeholder="End Date">
                            </div>
                            <div class="col-md-2">
                                <button id="btnFilterPerf" class="btn btn-sm btn-primary btn-block">Filter</button>
                            </div>
                            <div class="col-md-2">
                                <button id="btnExportPerf" class="btn btn-sm btn-success btn-block"><i
                                        class="fas fa-file-csv"></i> Export</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover m-0">
                                <thead>
                                    <tr>
                                        <th>Employee Name</th>
                                        <th class="text-center">Assigned (Qty)</th>
                                        <th class="text-center">Executed (Qty)</th>
                                        <th class="text-center">OK</th>
                                        <th class="text-center">NG</th>
                                        <th class="text-center">Completion %</th>
                                    </tr>
                                </thead>
                                <tbody id="employeePerformanceBody">
                                    <tr>
                                        <td colspan="6" class="text-center">Loading data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Distribution Charts -->
        <div class="row">
            <div class="col-md-3">
                <div class="card card-outline card-teal">
                    <div class="card-header">
                        <h3 class="card-title">Department</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="deptChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-outline card-indigo">
                    <div class="card-header">
                        <h3 class="card-title">Section</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="sectionChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-outline card-olive">
                    <div class="card-header">
                        <h3 class="card-title">Position</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="positionChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-outline card-maroon">
                    <div class="card-header">
                        <h3 class="card-title">Gender</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="genderChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Row for Tenant Owner -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Recent Activities</h3>
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
                                        <th>Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data['recent_activities'] as $log)
                                        <tr>
                                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $log->user->name ?? 'System/Guest' }}</td>
                                            <td>{!! $log->action_badge !!}</td>
                                            <td>{{ $log->table_name_formatted }} (ID: {{ $log->record_id }})</td>
                                            <td>{{ $log->ip_address }}</td>
                                            <td>
                                                <button class="btn btn-xs btn-info view-log-details" data-id="{{ $log->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No recent activities found.</td>
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

    @else
    <!-- Tenant Staff View -->
    <div class="row mb-3">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clipboard-list"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending Tasks</span>
                    <span class="info-box-number">{{ $data['pending_tasks'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-double"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Jobs Done</span>
                    <span class="info-box-number">{{ $data['total_jobs_done'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-percentage"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Quality Score</span>
                    <span class="info-box-number">{{ $data['quality_score'] }}%</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-thumbs-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total OK Qty</span>
                    <span class="info-box-number">{{ $data['total_qty_ok'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart & Notifications Row -->
    <div class="row mb-3">
        <div class="col-md-8">
            <div class="card card-outline card-primary h-100">
                <div class="card-header">
                    <h3 class="card-title">My Production Trend (Last 7 Days)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart" style="position: relative; height: 300px;">
                        <canvas id="personalTrendChart"
                            style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-outline card-warning h-100">
                <div class="card-header">
                    <h3 class="card-title">Recent Notifications</h3>
                </div>
                <div class="card-body p-0 d-flex flex-column">
                    <ul class="products-list product-list-in-card pl-2 pr-2 flex-grow-1">
                        @forelse($data['recent_notifications'] as $notif)
                            <li class="item d-flex align-items-center">
                                <div class="product-img mr-3 ml-2">
                                    <i
                                        class="{{ $notif->type == 'warning' ? 'fas fa-exclamation-circle text-warning' : ($notif->type == 'success' ? 'fas fa-check-circle text-success' : 'fas fa-info-circle text-info') }} fa-2x"></i>
                                </div>
                                <div class="product-info flex-grow-1 ml-0">
                                    <a href="{{ $notif->link ?? '#' }}" class="product-title">{{ $notif->title }}
                                        <span
                                            class="badge badge-light float-right">{{ $notif->created_at->diffForHumans() }}</span>
                                    </a>
                                    <span class="product-description">
                                        {!! Str::limit(strip_tags($notif->message), 50) !!}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="item text-center">
                                <div class="product-info ml-2">
                                    <span class="product-description">No new notifications.</span>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="/notifications" class="uppercase">View All Notifications</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Recent Activity Log</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover table-striped text-sm">
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
<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalTitle">Performance History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date Assign</th>
                                <th>Item Name</th>
                                <th class="text-center">Assign QTY</th>
                                <th class="text-center">QTY OK</th>
                                <th class="text-center">QTY NG</th>
                                <th class="text-center">Completion %</th>
                                <th class="text-center">Duration</th>
                            </tr>
                        </thead>
                        <tbody id="historyModalBody">
                            <!-- Ajax Content -->
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
<!-- Activity Log Detail Modal -->
<div class="modal fade" id="activityLogModal" tabindex="-1" role="dialog" aria-labelledby="activityLogModalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activityLogModalTitle">Activity Log Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th>Time:</th>
                                <td id="logDate">-</td>
                            </tr>
                            <tr>
                                <th>User:</th>
                                <td id="logUser">-</td>
                            </tr>
                            <tr>
                                <th>IP Address:</th>
                                <td id="logIp">-</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th>Action:</th>
                                <td id="logAction">-</td>
                            </tr>
                            <tr>
                                <th>Target:</th>
                                <td id="logTarget">-</td>
                            </tr>
                            <tr>
                                <th>User Agent:</th>
                                <td id="logUserAgent" style="word-break: break-all; font-size: 0.8em;">-</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Old Values</h6>
                        <pre id="logOldValues" class="bg-light p-2"
                            style="max-height: 300px; overflow-y: auto; font-size: 0.8em;">-</pre>
                    </div>
                    <div class="col-md-6">
                        <h6>New Values</h6>
                        <pre id="logNewValues" class="bg-light p-2"
                            style="max-height: 300px; overflow-y: auto; font-size: 0.8em;">-</pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    // Register the plugin globally
    Chart.register(ChartDataLabels);

    $(document).ready(function () {
        let productionChart = null;
        let inventoryChart = null;

        @if(isset($data['chart_data']))
            // Production Chart
            var ctx = document.getElementById('productionChart').getContext('2d');
            var chartData = @json($data['chart_data']);

            productionChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'OK Quantity',
                            backgroundColor: 'rgba(40, 167, 69, 0.9)',
                            borderColor: 'rgba(40, 167, 69, 0.8)',
                            data: chartData.ok,
                            datalabels: { display: false } // Disable for bar chart if not wanted
                        },
                        {
                            label: 'NG Quantity',
                            backgroundColor: 'rgba(220, 53, 69, 0.9)',
                            borderColor: 'rgba(220, 53, 69, 0.8)',
                            data: chartData.ng,
                            datalabels: { display: false } // Disable for bar chart if not wanted
                        }
                    ]
                },
                options: {
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        datalabels: {
                            display: false // Disable globally for this chart
                        }
                    }
                }
            });
        @endif

            @if(isset($data['inventory_chart_data']))
                // Inventory Flow Chart
                var ctxInventory = document.getElementById('inventoryChart').getContext('2d');
                var inventoryData = @json($data['inventory_chart_data']);

                inventoryChart = new Chart(ctxInventory, {
                    type: 'bar', // or line
                    data: {
                        labels: inventoryData.labels,
                        datasets: [
                            {
                                label: 'Incoming',
                                backgroundColor: 'rgba(23, 162, 184, 0.9)',
                                borderColor: 'rgba(23, 162, 184, 0.8)',
                                data: inventoryData.incoming,
                                datalabels: { display: false }
                            },
                            {
                                label: 'Outgoing',
                                backgroundColor: 'rgba(255, 193, 7, 0.9)',
                                borderColor: 'rgba(255, 193, 7, 0.8)',
                                data: inventoryData.outgoing,
                                datalabels: { display: false }
                            }
                        ]
                    },
                    options: {
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            datalabels: {
                                display: false
                            }
                        }
                    }
                });
            @endif

        // AJAX Chart Updates
        $('#inventoryPeriod').change(function () {
            var period = $(this).val();
            fetchChartData('inventory', period);
        });

        $('#productionPeriod').change(function () {
            var period = $(this).val();
            fetchChartData('production', period);
        });

        function fetchChartData(type, period) {
            $.ajax({
                url: '{{ route("dashboard.chart-data") }}',
                method: 'GET',
                data: { type: type, period: period },
                success: function (response) {
                    if (type === 'inventory' && inventoryChart) {
                        inventoryChart.data.labels = response.labels;
                        inventoryChart.data.datasets[0].data = response.incoming;
                        inventoryChart.data.datasets[1].data = response.outgoing;
                        inventoryChart.update();
                    } else if (type === 'production' && productionChart) {
                        productionChart.data.labels = response.labels;
                        productionChart.data.datasets[0].data = response.ok;
                        productionChart.data.datasets[1].data = response.ng;
                        productionChart.update();
                    }
                },
                error: function (xhr) {
                    console.error('Failed to fetch chart data');
                    // Optional: Show toast error
                }
            });
        }

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
                            data: growthData.active,
                            datalabels: { display: false }
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
                            data: growthData.inactive,
                            datalabels: { display: false }
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
                    },
                    plugins: {
                        datalabels: {
                            display: false
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
                            },
                            datalabels: {
                                display: false
                            }
                        }
                    }
                });
            @endif

            @if(isset($data['user_distribution']))
                // Helper to create pie chart
                function createPieChart(ctxId, dataObj, colors) {
                    var ctx = document.getElementById(ctxId).getContext('2d');
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(dataObj),
                            datasets: [{
                                data: Object.values(dataObj),
                                backgroundColor: colors,
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: {
                                        weight: 'bold'
                                    },
                                    formatter: (value, ctx) => {
                                        let sum = 0;
                                        let dataArr = ctx.chart.data.datasets[0].data;
                                        dataArr.map(data => {
                                            sum += data;
                                        });
                                        let percentage = (value * 100 / sum).toFixed(1) + "%";
                                        return percentage;
                                    }
                                }
                            }
                        }
                    });
                }

                // Department
                if (Object.keys(@json($data['user_distribution']['department'])).length > 0) {
                    createPieChart('deptChart', @json($data['user_distribution']['department']), ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de']);
                }

                // Section
                if (Object.keys(@json($data['user_distribution']['section'])).length > 0) {
                    createPieChart('sectionChart', @json($data['user_distribution']['section']), ['#00c0ef', '#3c8dbc', '#d2d6de', '#f56954', '#00a65a', '#f39c12']);
                }

                // Position
                if (Object.keys(@json($data['user_distribution']['position'])).length > 0) {
                    createPieChart('positionChart', @json($data['user_distribution']['position']), ['#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', '#f56954']);
                }

                // Gender
                if (Object.keys(@json($data['user_distribution']['gender'])).length > 0) {
                    createPieChart('genderChart', @json($data['user_distribution']['gender']), ['#3c8dbc', '#f56954']);
                }
            @endif

        // Employee Performance Logic
        fetchEmployeePerformance(10); // Initial load

        // Top Defects Logic
        fetchTopDefects();

        // Top OK Logic
        fetchTopOkItems();

        // ... handlers ...

        function fetchTopDefects() {
            var canvas = document.getElementById('topDefectChart');
            if (!canvas) return;

            var ctx = canvas.getContext('2d');

            $.ajax({
                url: '{{ route("dashboard.chart-data") }}',
                method: 'GET',
                data: { type: 'top_defects' },
                success: function (response) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                label: 'NG Quantity',
                                data: response.data,
                                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                                borderColor: 'rgba(220, 53, 69, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y', // Horizontal bar
                            interaction: {
                                mode: 'index',
                                intersect: false,
                                axis: 'y'
                            },
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: { beginAtZero: true }
                            },
                            plugins: {
                                legend: { display: false },
                                title: {
                                    display: true,
                                    text: 'Top 5 Most Rejected Items'
                                },
                                datalabels: {
                                    anchor: 'end',
                                    align: 'end',
                                    color: '#000',
                                    formatter: Math.round
                                }
                            }
                        }
                    });
                },
                error: function () {
                    console.error('Failed to load top defects');
                }
            });
        }

        function fetchTopOkItems() {
            var canvas = document.getElementById('topOkChart');
            if (!canvas) return;

            var ctx = canvas.getContext('2d');

            $.ajax({
                url: '{{ route("dashboard.chart-data") }}',
                method: 'GET',
                data: { type: 'top_ok' },
                success: function (response) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                label: 'OK Quantity',
                                data: response.data,
                                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y', // Horizontal bar
                            interaction: {
                                mode: 'index',
                                intersect: false,
                                axis: 'y'
                            },
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: { beginAtZero: true }
                            },
                            plugins: {
                                legend: { display: false },
                                title: {
                                    display: true,
                                    text: 'Top 5 Highest Production'
                                },
                                datalabels: {
                                    anchor: 'end',
                                    align: 'end',
                                    color: '#000',
                                    formatter: Math.round
                                }
                            }
                        }
                    });
                },
                error: function () {
                    console.error('Failed to load top OK items');
                }
            });
        }


        $('#performanceLimit').change(function () {
            fetchEmployeePerformance();
        });

        $('#btnFilterPerf').click(function () {
            fetchEmployeePerformance();
        });

        $('#btnExportPerf').click(function () {
            var startDate = $('#perfStartDate').val();
            var endDate = $('#perfEndDate').val();
            // Optional: Limit can be ignored for export or default to high
            var url = '{{ route("dashboard.export-performance") }}' + '?start_date=' + startDate + '&end_date=' + endDate;
            window.location.href = url;
        });

        function fetchEmployeePerformance(limitOverride) {
            var limit = limitOverride || $('#performanceLimit').val();
            var startDate = $('#perfStartDate').val();
            var endDate = $('#perfEndDate').val();

            $.ajax({
                url: '{{ route("dashboard.chart-data") }}',
                method: 'GET',
                data: {
                    type: 'employee_performance',
                    limit: limit,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function (response) {
                    var tbody = $('#employeePerformanceBody');
                    tbody.empty();

                    if (response.length === 0) {
                        tbody.append('<tr><td colspan="6" class="text-center">No data found.</td></tr>');
                        return;
                    }

                    $.each(response, function (index, item) {
                        var completion = (item.assigned > 0) ? ((item.executed / item.assigned) * 100).toFixed(1) : '0.0';
                        var completionColor = 'bg-danger';
                        if (completion >= 50 && completion < 80) completionColor = 'bg-warning';
                        if (completion >= 80) completionColor = 'bg-success';
                        if (item.executed > item.assigned) completionColor = 'bg-primary'; // Overachiever?

                        var row = `<tr>
                                <td>
                                    <a href="#" class="view-history" data-id="${item.user_id}" style="font-weight:bold; text-decoration: underline;">
                                        ${item.name}
                                    </a> 
                                    <br><small class="text-muted">${item.role}</small></td>
                                <td class="text-center"><span class="badge bg-purple">${item.assigned}</span></td>
                                <td class="text-center"><span class="badge bg-info">${item.executed}</span></td>
                                <td class="text-center"><span class="badge bg-success">${item.qty_ok}</span></td>
                                <td class="text-center"><span class="badge bg-danger">${item.qty_ng}</span></td>
                                <td class="text-center">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar ${completionColor}" style="width: ${completion <= 100 ? completion : 100}%"></div>
                                    </div>
                                    <small>${completion}%</small>
                                </td>
                            </tr>`;
                        tbody.append(row);
                    });
                },
                error: function () {
                    $('#employeePerformanceBody').html('<tr><td colspan="6" class="text-center text-danger">Failed to load data.</td></tr>');
                }
            });
        }

        // History Modal Logic
        $(document).on('click', '.view-history', function (e) {
            e.preventDefault();
            var userId = $(this).data('id');
            var modal = $('#historyModal');

            $('#historyModalTitle').text('Loading...');
            $('#historyModalBody').html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');
            modal.modal('show');

            $.ajax({
                url: '/dashboard/employee-history/' + userId,
                method: 'GET',
                success: function (response) {
                    $('#historyModalTitle').text('Performance History: ' + response.user);
                    var tbody = $('#historyModalBody');
                    tbody.empty();

                    if (response.history.length === 0) {
                        tbody.append('<tr><td colspan="7" class="text-center">No recent history found.</td></tr>');
                    } else {
                        $.each(response.history, function (i, job) {
                            tbody.append(`
                                    <tr>
                                        <td>${job.date_assign}</td>
                                        <td>${job.item}</td>
                                        <td class="text-center"><span class="badge bg-purple">${job.assign_qty}</span></td>
                                        <td class="text-success text-center">${job.qty_ok}</td>
                                        <td class="text-danger text-center">${job.qty_ng}</td>
                                        <td class="text-center">${job.completion}</td>
                                        <td class="text-center">${job.duration}</td>
                                    </tr>
                                `);
                        });
                    }
                },
                error: function () {
                    $('#historyModalTitle').text('Error');
                    $('#historyModalBody').html('<tr><td colspan="7" class="text-center text-danger">Failed to fetch history</td></tr>');
                }
            });
        });

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
                        },
                        datalabels: {
                            display: false
                        }
                    }
                }
            });
        @endif
        // Activity Log Detail Logic
        $(document).on('click', '.view-log-details', function (e) {
            e.preventDefault();
            var logId = $(this).data('id');
            var modal = $('#activityLogModal');

            // Reset/Loading state
            $('#logDate').text('Loading...');
            $('#logUser').text('Loading...');
            $('#logAction').text('-');
            $('#logTarget').text('-');
            $('#logIp').text('-');
            $('#logUserAgent').text('-');
            $('#logOldValues').text('-');
            $('#logNewValues').text('-');
            modal.modal('show');

            $.ajax({
                url: '/dashboard/activity-log/' + logId,
                method: 'GET',
                success: function (response) {
                    $('#logDate').text(response.created_at);
                    $('#logUser').text(response.user);
                    $('#logAction').text(response.action);
                    $('#logTarget').text(response.target);
                    $('#logIp').text(response.ip_address);
                    $('#logUserAgent').text(response.user_agent || '-');
                    
                    $('#logOldValues').text(JSON.stringify(response.old_values, null, 2));
                    $('#logNewValues').text(JSON.stringify(response.new_values, null, 2));
                },
                error: function () {
                    $('#logDate').text('Error fetching data');
                }
            });
        });
        });
</script>
@if($viewType == 'tenant_staff')
    <script>
        $(function () {
            // Personal Trend Chart
            var trendChartCanvas = $('#personalTrendChart').get(0).getContext('2d');
            var trendChartData = {
                labels: {!! json_encode($data['personal_chart']['labels']) !!},
                datasets: [
                    {
                        label: 'OK',
                        backgroundColor: 'rgba(40, 167, 69, 0.9)',
                        borderColor: 'rgba(40, 167, 69, 0.8)',
                        pointRadius: false,
                        pointColor: '#28a745',
                        pointStrokeColor: 'rgba(40, 167, 69, 1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(40, 167, 69, 1)',
                        data: {!! json_encode($data['personal_chart']['ok']) !!}
                    },
                    {
                        label: 'NG',
                        backgroundColor: 'rgba(220, 53, 69, 0.9)',
                        borderColor: 'rgba(220, 53, 69, 0.8)',
                        pointRadius: false,
                        pointColor: '#dc3545',
                        pointStrokeColor: 'rgba(220, 53, 69, 1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(220, 53, 69, 1)',
                        data: {!! json_encode($data['personal_chart']['ng']) !!}
                    },
                ]
            };

            var trendChartOptions = {
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    display: true
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false,
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false,
                        }
                    }]
                }
            }

            new Chart(trendChartCanvas, {
                type: 'bar', // or 'line'
                data: trendChartData,
                options: trendChartOptions
            });
        });

    </script>
@endif
@stop