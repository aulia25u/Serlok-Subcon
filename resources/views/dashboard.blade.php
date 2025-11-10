@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Dashboard</h1>
        <h5 class="mb-0">
            <i class="fas fa-user-circle mr-2"></i>
            Hello, {{ Auth::user()->userDetail->employee_name ?? Auth::user()->name }} - 
            <span class="badge badge-primary">{{ Auth::user()->userDetail->role->role_name ?? 'User' }}</span>
        </h5>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="total-customers">0</h3>
                    <p>Total Customers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('rbac.customer') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>



        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="upcoming-events">0</h3>
                    <p>Upcoming Events</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="{{ route('rbac.calendar-pitching.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="active-subscriptions">0</h3>
                    <p>Active Subscriptions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <a href="{{ route('rbac.subscription.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="total-income">0</h3>
                    <p>Total Income</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ route('rbac.subscription.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left col -->
        <section class="col-lg-7 connectedSortable">

            <!-- Customer Process Activity Chart -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-1"></i>
                        Customer Process Activity (Last 24 Hours)
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="customerActivityChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </section>
        <!-- /.Left col -->

        <!-- right col -->
        <section class="col-lg-5 connectedSortable">
            <!-- Filter -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </h3>
                </div>
                <div class="card-body">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-success active">
                            <input type="radio" name="options" id="calendar-filter" autocomplete="off" checked> Calendar
                        </label>
                        <label class="btn btn-light">
                            <input type="radio" name="options" id="subscription-filter" autocomplete="off"> Subscription
                        </label>
                    </div>
                </div>
            </div>

            <!-- Subscription Status -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card mr-1"></i>
                        Subscription Status
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active</span>
                                    <span class="info-box-number" id="active-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Penagihan</span>
                                    <span class="info-box-number" id="penagihan-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-secondary"><i class="fas fa-times-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Non-Active</span>
                                    <span class="info-box-number" id="non-active-count">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penagihan Subscriptions -->
            <div class="card" id="penagihan-subscriptions-card" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice-dollar mr-1"></i>
                        Penagihan Subscriptions
                    </h3>
                </div>
                <div class="card-body" id="penagihan-subscriptions-list">
                    <p class="text-muted">Loading penagihan subscriptions...</p>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="card" id="upcoming-events-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Upcoming Events
                    </h3>
                </div>
                <div class="card-body" id="upcoming-events-list">
                    <p class="text-muted">Loading upcoming events...</p>
                </div>
            </div>
        </section>
        <!-- /.right col -->
    </div>
@stop
{{-- This form is needed for the logout functionality --}}
<form id="logout-form" action="{{ route('logout') }}" method="GET" style="display: none;">
    @csrf
</form>
@push('js')
    <script>
        document.getElementById('logout-link').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('logout-form').submit();
        });

        $(document).ready(function() {
            // Load dashboard statistics
            loadDashboardStats();

            // Load customer activity chart
            loadCustomerActivityChart();

            // Add hover effects to small boxes
            $('.small-box').hover(
                function() {
                    $(this).addClass('shadow-lg');
                },
                function() {
                    $(this).removeClass('shadow-lg');
                }
            );
        });

        function loadDashboardStats() {
            // Load total customers
            $.get("{{ route('rbac.customer') }}?ajax=1&stats=1")
                .done(function(data) {
                    $('#total-customers').text(data.total || 0);
                });



            // Load upcoming events
            $.get("{{ route('rbac.calendar-pitching.data') }}?upcoming=1")
                .done(function(data) {
                    $('#upcoming-events').text(data.total || 0);
                    loadUpcomingEventsList(data.events || []);
                });

            // Load subscription stats
            $.get("{{ route('rbac.subscription.data') }}?stats=1")
                .done(function(data) {
                    $('#active-subscriptions').text(data.active || 0);
                    $('#active-count').text(data.active || 0);
                    $('#non-active-count').text(data.non_active || 0);
                    $('#penagihan-count').text(data.penagihan || 0);
                    $('#total-income').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.total_income || 0));
                });

            // Load penagihan subscriptions
            $.get("{{ route('rbac.subscription.data') }}?penagihan=1")
                .done(function(data) {
                    loadPenagihanSubscriptions(data.data || []);
                });
        }

        function loadPenagihanSubscriptions(subscriptions) {
            let html = '';
            if (subscriptions.length > 0) {
                subscriptions.slice(0, 5).forEach(function(subscription) {
                    html += '<div class="d-flex justify-content-between align-items-center mb-2">';
                    html += '<div>';
                    html += '<strong>' + subscription.customer_name + '</strong><br>';
                    html += '<small class="text-muted">Valid until: ' + subscription.valid_until_formatted + '</small>';
                    html += '</div>';
                    html += '<span class="badge badge-warning">Penagihan</span>';
                    html += '</div>';
                });
            } else {
                html = '<p class="text-muted">No penagihan subscriptions</p>';
            }
            $('#penagihan-subscriptions-list').html(html);
        }

        function loadUpcomingEventsList(events) {
            let html = '';
            if (events.length > 0) {
                events.slice(0, 5).forEach(function(event) {
                    html += '<div class="d-flex justify-content-between align-items-center mb-2">';
                    html += '<div>';
                    html += '<strong>' + event.title + '</strong><br>';
                    html += '<small class="text-muted">' + event.customer_name + ' - ' + event.scheduled_date_formatted + '</small>';
                    html += '</div>';
                    html += '<span class="badge badge-warning">' + event.status + '</span>';
                    html += '</div>';
                });
            } else {
                html = '<p class="text-muted">No upcoming events</p>';
            }
            $('#upcoming-events-list').html(html);
        }

        // Filter logic
        $('#calendar-filter').on('change', function() {
            if ($(this).is(':checked')) {
                $('#upcoming-events-card').show();
                $('#penagihan-subscriptions-card').hide();
                $('#calendar-filter').parent().removeClass('btn-light').addClass('btn-success');
                $('#subscription-filter').parent().removeClass('btn-info').addClass('btn-light');
            }
        });

        $('#subscription-filter').on('change', function() {
            if ($(this).is(':checked')) {
                $('#upcoming-events-card').hide();
                $('#penagihan-subscriptions-card').show();
                $('#subscription-filter').parent().removeClass('btn-light').addClass('btn-info');
                $('#calendar-filter').parent().removeClass('btn-success').addClass('btn-light');
            }
        });

        function loadCustomerActivityChart() {
            $.get("{{ route('rbac.monitoring.dashboard-chart-data') }}")
                .done(function(data) {
                    var ctx = document.getElementById('customerActivityChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: data.datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Total Processes'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Hour'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            }
                        }
                    });
                })
                .fail(function() {
                    console.error('Failed to load customer activity chart data');
                });
        }
    </script>
@endpush
