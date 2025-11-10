@extends('adminlte::page')

@section('title', 'POS Report - ' . $posQueue->customer->name)
@section('page-title', 'POS Report - ' . $posQueue->customer->name)

@section('css')
<style>
    /* Navy Blue Header Theme */
    #posDataTable thead th {
        background-color: #001f3f !important;
        color: white !important;
        font-weight: bold !important;
        border-color: #003366 !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    /* Striped rows - alternating blue and white */
    #posDataTable tbody tr:nth-child(odd) {
        background-color: #e3f2fd !important;
    }

    #posDataTable tbody tr:nth-child(even) {
        background-color: white !important;
    }

    /* Hover effect */
    #posDataTable tbody tr:hover {
        background-color: #bbdefb !important;
    }

    /* Table borders */
    #posDataTable {
        border-collapse: collapse !important;
        font-size: 0.8rem !important; /* Reduced by 20% from default 1rem */
    }

    #posDataTable tbody td {
        border-color: #ddd !important;
        vertical-align: middle !important;
        padding: 0.4rem !important; /* Reduced padding for compact view */
    }

    #posDataTable thead th {
        padding: 0.5rem !important; /* Reduced padding for header */
    }

    /* Pagination styling */
    .pagination .page-item.active .page-link {
        background-color: #001f3f !important;
        border-color: #001f3f !important;
        color: white !important;
    }

    .pagination .page-link {
        color: #001f3f;
    }

    .pagination .page-link:hover {
        background-color: #e3f2fd;
        border-color: #001f3f;
    }

    /* Table responsive wrapper with fixed header */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Fixed header table container */
    .table-fixed-header {
        max-height: 600px;
        overflow-y: auto;
        overflow-x: auto;
    }

    /* Sticky header */
    .table-fixed-header thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #001f3f !important;
    }

    /* Freeze first 4 columns (No, Date, Time, Items) */
    #posDataTable thead th:nth-child(-n+4),
    #posDataTable tbody td:nth-child(-n+4) {
        position: sticky;
        background-color: inherit;
        z-index: 5;
    }

    /* Header frozen columns need higher z-index */
    #posDataTable thead th:nth-child(-n+4) {
        z-index: 15;
    }

    /* Set left positions for frozen columns */
    #posDataTable thead th:nth-child(1),
    #posDataTable tbody td:nth-child(1) {
        left: 0;
        min-width: 50px;
    }

    #posDataTable thead th:nth-child(2),
    #posDataTable tbody td:nth-child(2) {
        left: 50px;
        min-width: 120px;
    }

    #posDataTable thead th:nth-child(3),
    #posDataTable tbody td:nth-child(3) {
        left: 170px;
        min-width: 80px;
    }

    #posDataTable thead th:nth-child(4),
    #posDataTable tbody td:nth-child(4) {
        left: 250px;
        min-width: 200px;
    }

    /* Add shadow to last frozen column */
    #posDataTable thead th:nth-child(4),
    #posDataTable tbody td:nth-child(4) {
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    /* Ensure proper spacing */
    .table {
        margin-bottom: 0;
    }

    /* Ensure frozen columns have proper background on odd rows */
    #posDataTable tbody tr:nth-child(odd) td:nth-child(-n+4) {
        background-color: #e3f2fd !important;
    }

    /* Ensure frozen columns have proper background on even rows */
    #posDataTable tbody tr:nth-child(even) td:nth-child(-n+4) {
        background-color: white !important;
    }

    /* Hover effect for frozen columns */
    #posDataTable tbody tr:hover td:nth-child(-n+4) {
        background-color: #bbdefb !important;
    }
</style>
@stop

@section('content')
<div class="container-fluid">
    <!-- Report Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Report Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Customer:</strong><br>
                            {{ $posQueue->customer->name }}
                        </div>
                        <div class="col-md-3">
                            <strong>Start Date:</strong><br>
                            {{ date('d-m-Y', (int)$posQueue->start_date) }}
                        </div>
                        <div class="col-md-3">
                            <strong>End Date:</strong><br>
                            {{ date('d-m-Y', (int)$posQueue->end_date) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong><br>
                            <span class="badge badge-{{ $posQueue->status == 'completed' ? 'success' : ($posQueue->status == 'processing' ? 'warning' : ($posQueue->status == 'failed' ? 'danger' : 'secondary')) }}">
                                {{ ucfirst($posQueue->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    @if($posQueue->status == 'completed' && $posData->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Summary Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ([
                            ['icon'=>'fas fa-receipt','color'=>'text-info','label'=>'Total Transactions','value'=>$summary['total_transactions'],'prefix'=>'','format'=>'integer'],
                            ['icon'=>'fas fa-dollar-sign','color'=>'text-success','label'=>'Gross Sales','value'=>$summary['total_gross_sales'],'prefix'=>'Rp ','format'=>'decimal'],
                            ['icon'=>'fas fa-tags','color'=>'text-warning','label'=>'Discounts','value'=>$summary['total_discounts'],'prefix'=>'Rp ','format'=>'decimal'],
                            ['icon'=>'fas fa-undo','color'=>'text-danger','label'=>'Refunds','value'=>$summary['total_refunds'],'prefix'=>'Rp ','format'=>'decimal'],
                            ['icon'=>'fas fa-chart-line','color'=>'text-primary','label'=>'Net Sales','value'=>$summary['total_net_sales'],'prefix'=>'Rp ','format'=>'decimal'],
                            ['icon'=>'fas fa-calculator','color'=>'text-secondary','label'=>'Tax','value'=>$summary['total_tax'],'prefix'=>'Rp ','format'=>'decimal']
                        ] as $card)
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <i class="{{ $card['icon'] }} fa-2x {{ $card['color'] }}"></i>
                                        <h6 class="mb-0 mt-1">{{ $card['label'] }}</h6>
                                    </div>
                                    <hr class="my-2">
                                    <h5 class="mb-0" style="font-size: 0.95rem;">
                                        {{ $card['prefix'] }}{{ $card['format'] === 'integer' ? number_format($card['value'], 0) : number_format($card['value'], 2) }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Report Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">POS Data Report</h3>
                </div>
                <div class="card-body">
                    @if($posQueue->status == 'completed')
                        @if($posData->count() > 0)
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="per_page">Show entries:</label>
                                    <select class="form-control" id="per_page">
                                        <option value="25">25</option>
                                        <option value="30">30</option>
                                        <option value="100">100</option>
                                        <option value="all">All</option>
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive table-fixed-header">
                                <table class="table table-bordered table-striped" id="posDataTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Items</th>
                                            <th>Receipt Number</th>
                                            <th>Category</th>
                                            <th>Brand</th>
                                            <th>Variant</th>
                                            <th>SKU</th>
                                            <th>Quantity</th>
                                            <th>Modifier Applied</th>
                                            <th>Discount Applied</th>
                                            <th>Gross Sales</th>
                                            <th>Discounts</th>
                                            <th>Refunds</th>
                                            <th>Net Sales</th>
                                            <th>Gratuity</th>
                                            <th>Tax</th>
                                            <th>Sales Type</th>
                                            <th>Collected By</th>
                                            <th>Served By</th>
                                            <th>Customer</th>
                                            <th>Payment Method</th>
                                            <th>Event Type</th>
                                            <th>Reason of Refund</th>
                                        </tr>
                                    </thead>
                                    <tbody id="posTableBody">
                                        <tr>
                                            <td colspan="25" class="text-center">Loading data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div id="pagination-info"></div>
                                </div>
                                <div class="col-md-6">
                                    <nav>
                                        <ul class="pagination justify-content-end" id="pagination"></ul>
                                    </nav>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No POS data found for the selected date range.
                            </div>
                        @endif
                    @elseif($posQueue->status == 'processing')
                        <div class="alert alert-warning">
                            <i class="fas fa-spinner fa-spin"></i> Report is being processed. Please check back later.
                        </div>
                    @elseif($posQueue->status == 'failed')
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Report generation failed. Please try again.
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-clock"></i> Report is queued for processing.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
$(document).ready(function() {
    @if($posQueue->status == 'completed')
    let currentPage = 1;

    // Load data on page load
    loadPosData();

    // Per page change
    $('#per_page').on('change', function() {
        currentPage = 1;
        loadPosData();
    });

    // Load POS data function
    function loadPosData(page = 1) {
        const perPage = $('#per_page').val();

        $.ajax({
            url: "{{ route('rbac.pos-monitoring.report.data', $posQueue->id) }}",
            type: 'GET',
            data: {
                per_page: perPage,
                page: page
            },
            beforeSend: function() {
                $('#posTableBody').html('<tr><td colspan="25" class="text-center">Loading data...</td></tr>');
            },
            success: function(response) {
                renderTable(response);
                renderPagination(response);
                currentPage = response.current_page;
            },
            error: function(xhr) {
                console.error('Ajax Error:', xhr.responseText);
                $('#posTableBody').html('<tr><td colspan="25" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }

    // Render table
    function renderTable(response) {
        const tbody = $('#posTableBody');
        tbody.empty();

        if (response.data && response.data.length > 0) {
            response.data.forEach(function(item) {
                const row = `
                    <tr>
                        <td>${item[0]}</td>
                        <td>${item[2]}</td>
                        <td>${item[3]}</td>
                        <td>${item[6]}</td>
                        <td>${item[1]}</td>
                        <td>${item[4]}</td>
                        <td>${item[5]}</td>
                        <td>${item[7]}</td>
                        <td>${item[8]}</td>
                        <td>${item[9]}</td>
                        <td>${item[10]}</td>
                        <td>${item[11]}</td>
                        <td>${item[12]}</td>
                        <td>${item[13]}</td>
                        <td>${item[14]}</td>
                        <td>${item[15]}</td>
                        <td>${item[16]}</td>
                        <td>${item[17]}</td>
                        <td>${item[18]}</td>
                        <td>${item[19]}</td>
                        <td>${item[20]}</td>
                        <td>${item[21]}</td>
                        <td>${item[22]}</td>
                        <td>${item[23]}</td>
                        <td>${item[24]}</td>
                    </tr>
                `;
                tbody.append(row);
            });
        } else {
            tbody.html('<tr><td colspan="25" class="text-center">No data available</td></tr>');
        }
    }

    // Render pagination
    function renderPagination(response) {
        const pagination = $('#pagination');
        const paginationInfo = $('#pagination-info');
        
        pagination.empty();
        paginationInfo.empty();

        if (response.per_page === 'all') {
            paginationInfo.html(`Showing all ${response.total} entries`);
            return;
        }

        const start = (response.current_page - 1) * response.per_page + 1;
        const end = Math.min(response.current_page * response.per_page, response.total);
        paginationInfo.html(`Showing ${start} to ${end} of ${response.total} entries`);

        if (response.last_page > 1) {
            // Previous button
            pagination.append(`
                <li class="page-item ${response.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${response.current_page - 1}">Previous</a>
                </li>
            `);

            // Page numbers
            for (let i = 1; i <= response.last_page; i++) {
                if (i === 1 || i === response.last_page || (i >= response.current_page - 2 && i <= response.current_page + 2)) {
                    pagination.append(`
                        <li class="page-item ${i === response.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                } else if (i === response.current_page - 3 || i === response.current_page + 3) {
                    pagination.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
            }

            // Next button
            pagination.append(`
                <li class="page-item ${response.current_page === response.last_page ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${response.current_page + 1}">Next</a>
                </li>
            `);

            // Attach click events
            pagination.find('a.page-link').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page && page !== response.current_page) {
                    loadPosData(page);
                }
            });
        }
    }
    @endif
});
</script>
@endpush
