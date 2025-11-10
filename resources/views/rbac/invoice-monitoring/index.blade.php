@extends('adminlte::page')

@section('title', 'Invoice Monitoring')

@section('page-title', 'Invoice Monitoring')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Navy Blue Header Theme */
        #invoiceTable thead th,
        #historyTable thead th {
            background-color: #001f3f !important;
            color: white !important;
            font-weight: bold !important;
            border-color: #003366 !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
        }

        /* Striped rows - alternating blue and white */
        #invoiceTable tbody tr:nth-child(odd),
        #historyTable tbody tr:nth-child(odd) {
            background-color: #e3f2fd !important;
        }

        #invoiceTable tbody tr:nth-child(even),
        #historyTable tbody tr:nth-child(even) {
            background-color: white !important;
        }

        /* Hover effect */
        #invoiceTable tbody tr:hover,
        #historyTable tbody tr:hover {
            background-color: #bbdefb !important;
        }

        /* Table borders */
        #invoiceTable,
        #historyTable {
            border-collapse: collapse !important;
        }

        #invoiceTable tbody td,
        #historyTable tbody td {
            border-color: #ddd !important;
            vertical-align: middle !important;
        }

        /* Reduce font size by 20% (from 1rem to 0.8rem) */
        #invoiceTable,
        #historyTable {
            font-size: 0.8rem !important;
        }

        #invoiceTable thead th,
        #historyTable thead th {
            font-size: 0.8rem !important;
            padding: 8px !important;
        }

        #invoiceTable tbody td,
        #historyTable tbody td {
            font-size: 0.8rem !important;
            padding: 6px !important;
        }

        /* Frozen columns for invoice table - 4 columns (No, Tanggal Struk, Supplier, Items) */
        /* Note: Column 2 (Process ID) is hidden, so frozen are columns 1, 3, 4, 5 */
        .table-fixed-header {
            position: relative;
        }

        #invoiceTable thead th:nth-child(1),
        #invoiceTable thead th:nth-child(3),
        #invoiceTable thead th:nth-child(4),
        #invoiceTable thead th:nth-child(5),
        #invoiceTable tbody td:nth-child(1),
        #invoiceTable tbody td:nth-child(3),
        #invoiceTable tbody td:nth-child(4),
        #invoiceTable tbody td:nth-child(5) {
            position: sticky;
            background-color: inherit;
            z-index: 5;
        }

        /* Frozen column positions */
        #invoiceTable thead th:nth-child(1),
        #invoiceTable tbody td:nth-child(1) {
            left: 0;
            min-width: 50px;
            z-index: 6;
        }

        #invoiceTable thead th:nth-child(3),
        #invoiceTable tbody td:nth-child(3) {
            left: 50px;
            min-width: 100px;
            z-index: 6;
        }

        #invoiceTable thead th:nth-child(4),
        #invoiceTable tbody td:nth-child(4) {
            left: 150px;
            min-width: 150px;
            z-index: 6;
        }

        #invoiceTable thead th:nth-child(5),
        #invoiceTable tbody td:nth-child(5) {
            left: 300px;
            min-width: 150px;
            z-index: 6;
        }

        /* Header frozen columns need higher z-index */
        #invoiceTable thead th:nth-child(1),
        #invoiceTable thead th:nth-child(3),
        #invoiceTable thead th:nth-child(4),
        #invoiceTable thead th:nth-child(5) {
            z-index: 11 !important;
        }

        /* Border for frozen columns */
        #invoiceTable thead th:nth-child(5),
        #invoiceTable tbody td:nth-child(5) {
            border-right: 2px solid #001f3f !important;
        }

        /* Hide Process ID column (2nd column) */
        #invoiceTable thead th:nth-child(2),
        #invoiceTable tbody td:nth-child(2) {
            display: none !important;
        }

        /* Make Keterangan column wider (last column - 27th) */
        #invoiceTable thead th:last-child,
        #invoiceTable tbody td:last-child {
            min-width: 300px !important;
            max-width: 450px !important;
        }

        /* Editable field styling */
        .editable-field {
            cursor: pointer;
            position: relative;
            padding-right: 25px;
        }
        
        .editable-field:hover {
            background-color: #90caf9 !important;
        }
        
        .editable-field .edit-icon {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.2s;
            color: #001f3f;
        }
        
        .editable-field:hover .edit-icon {
            opacity: 1;
        }
        
        .editing-input {
            width: 100%;
            padding: 5px;
            border: 2px solid #001f3f;
            border-radius: 3px;
        }

        /* Tab styling to match theme */
        .nav-tabs .nav-link.active {
            background-color: #001f3f !important;
            color: white !important;
            border-color: #001f3f !important;
        }

        .nav-tabs .nav-link {
            color: #001f3f;
        }

        .nav-tabs .nav-link:hover {
            background-color: #e3f2fd;
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

        /* Ensure proper spacing */
        .table {
            margin-bottom: 0;
        }

        /* Fix Select2 height to match other form controls */
        .select2-container .select2-selection--single {
            height: 38px !important;
            padding: 6px 12px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            padding-left: 0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        /* Ensure all form controls have consistent height */
        .form-control,
        .select2-container .select2-selection--single {
            height: 38px !important;
        }

        /* Image Preview Panel - Fixed on right side */
        #imagePreviewPanel {
            position: fixed;
            right: 0;
            top: 80px;
            width: 400px;
            height: calc(100vh - 100px);
            background: white;
            border-left: 3px solid #001f3f;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
            overflow-y: auto;
            padding: 15px;
        }

        #imagePreviewPanel.active {
            display: block;
        }

        #imagePreviewPanel .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #001f3f;
        }

        #imagePreviewPanel .preview-header h4 {
            margin: 0;
            color: #001f3f;
            font-size: 1rem;
        }

        #imagePreviewPanel .close-preview {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        #imagePreviewPanel .close-preview:hover {
            background: #c82333;
        }

        #imagePreviewPanel .preview-info {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 0.85rem;
        }

        #imagePreviewPanel .preview-info p {
            margin: 5px 0;
        }

        #imagePreviewPanel .preview-image {
            width: 100%;
            border: 2px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        #imagePreviewPanel .loading-spinner {
            text-align: center;
            padding: 50px;
            color: #001f3f;
        }

        #imagePreviewPanel .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
        }

        /* Adjust table container when preview is active */
        .table-container-with-preview {
            margin-right: 420px;
            transition: margin-right 0.3s ease;
        }

        /* View button styling */
        .btn-view-image {
            background: #17a2b8;
            color: white;
            border: none;
            padding: 4px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.75rem;
        }

        .btn-view-image:hover {
            background: #138496;
        }

        .btn-view-image.active {
            background: #28a745;
        }

        /* Highlight row when image preview is active */
        #invoiceTable tbody tr.row-preview-active {
            background-color: #c8e6c9 !important;
            border-left: 4px solid #28a745 !important;
        }

        #invoiceTable tbody tr.row-preview-active:hover {
            background-color: #a5d6a7 !important;
        }
    </style>
@stop

@section('content')
    <!-- Image Preview Panel -->
    <div id="imagePreviewPanel">
        <div class="preview-header">
            <h4><i class="fas fa-image"></i> Receipt Preview</h4>
            <button class="close-preview" onclick="closeImagePreview()">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
        <div id="previewContent">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin fa-3x"></i>
                <p>Loading image...</p>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="mainContainer">
        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter Invoice Data</h3>
                        <div class="card-tools">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Use filters below to search and display invoice data
                            </small>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="filterForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="customer_id">Customer</label>
                                        <select class="form-control select2" id="customer_id" name="customer_id">
                                            <option value="">All Customers</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="month">Month</label>
                                        <select class="form-control" id="month" name="month">
                                            <option value="">All Months</option>
                                            <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>January</option>
                                            <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>February</option>
                                            <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>March</option>
                                            <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>April</option>
                                            <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>May</option>
                                            <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>June</option>
                                            <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>July</option>
                                            <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>August</option>
                                            <option value="9" {{ date('n') == 9 ? 'selected' : '' }}>September</option>
                                            <option value="10" {{ date('n') == 10 ? 'selected' : '' }}>October</option>
                                            <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>November</option>
                                            <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>December</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="year">Year</label>
                                        <select class="form-control" id="year" name="year">
                                            <option value="">All Years</option>
                                            @php
                                                $currentYear = date('Y');
                                                $startYear = 2020;
                                            @endphp
                                            @for($y = $currentYear; $y >= $startYear; $y--)
                                                <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="keyword">Keyword</label>
                                        <select class="form-control select2-keyword" id="keyword" name="keyword">
                                            <option value="">Select Field</option>
                                            <option value="process_id">Process ID</option>
                                            <option value="nomor_invoice">Nomor Invoice</option>
                                            <option value="tanggal_struk">Tanggal Struk</option>
                                            <option value="supplier">Supplier</option>
                                            <option value="items">Items</option>
                                            <option value="jumlah">Jumlah</option>
                                            <option value="satuan">Satuan</option>
                                            <option value="harga_satuan">Harga Satuan</option>
                                            <option value="payment">Payment</option>
                                            <option value="keterangan">Keterangan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="keyword_value">Value</label>
                                        <input type="text" class="form-control" id="keyword_value" name="keyword_value" placeholder="Enter value">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs" id="invoiceTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="invoice-data-tab" data-toggle="tab" href="#invoice-data" role="tab">
                                    <i class="fas fa-file-invoice"></i> Invoice Data
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="invoice-history-tab" data-toggle="tab" href="#invoice-history" role="tab">
                                    <i class="fas fa-history"></i> Invoice History
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="invoiceTabContent">
                            <!-- Invoice Data Tab -->
                            <div class="tab-pane fade show active" id="invoice-data" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="per_page">Show entries:</label>
                                        <select class="form-control" id="per_page">
                                            <option value="50" selected>50</option>
                                            <option value="75">75</option>
                                            <option value="100">100</option>
                                            <option value="all">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="table-responsive table-fixed-header">
                                    <table class="table table-bordered table-striped" id="invoiceTable">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Process ID</th>
                                                <th>Tanggal Struk</th>
                                                <th>Supplier</th>
                                                <th>Items</th>
                                                <th>Customer</th>
                                                <th>Tanggal Input</th>
                                                <th>Waktu Input</th>
                                                <th>Nomor Invoice</th>
                                                <th>Jumlah</th>
                                                <th>Satuan</th>
                                                <th>Harga Satuan</th>
                                                <th>Sub Total</th>
                                                <th>Discount</th>
                                                <th>Subtotal After Discount</th>
                                                <th>Refunds</th>
                                                <th>Pajak</th>
                                                <th>Ongkir</th>
                                                <th>Diskon Ongkir</th>
                                                <th>Voucher</th>
                                                <th>Asuransi Pengiriman</th>
                                                <th>Biaya Layanan</th>
                                                <th>Grand Total</th>
                                                <th>Input By</th>
                                                <th>File</th>
                                                <th>Payment</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invoiceTableBody">
                                            <tr>
                                                <td colspan="28" class="text-center">Loading data...</td>
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
                            </div>

                            <!-- Invoice History Tab -->
                            <div class="tab-pane fade" id="invoice-history" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> Invoice history displays records linked to filtered invoice data via ProcessID. Use the filters above (Customer, Month, Year, Keyword) to filter both Invoice Data and History.
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive table-fixed-header">
                                    <table class="table table-bordered table-striped" id="historyTable">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Process ID</th>
                                                <th>No Invoice</th>
                                                <th>Customer</th>
                                                <th>Field Name</th>
                                                <th>Old Value</th>
                                                <th>New Value</th>
                                                <th>Old Total</th>
                                                <th>New Total</th>
                                                <th>Editor</th>
                                                <th>Edited At</th>
                                                <th>IP Address</th>
                                            </tr>
                                        </thead>
                                        <tbody id="historyTableBody">
                                            <tr>
                                                <td colspan="12" class="text-center">Loading history data...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div id="history-pagination-info"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <nav>
                                            <ul class="pagination justify-content-end" id="history-pagination"></ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for customer
            $('.select2').select2({
                placeholder: 'Select a customer',
                allowClear: true
            });

            // Initialize Select2 for keyword
            $('.select2-keyword').select2({
                placeholder: 'Select a field',
                allowClear: true
            });

            let currentPage = 1;
            let currentHistoryPage = 1;
            let hasSearched = false;

            // Don't load data on page load - wait for user to filter
            // Show message to use filters
            $('#invoiceTableBody').html('<tr><td colspan="28" class="text-center text-muted"><i class="fas fa-search fa-2x mb-2"></i><br><strong>No Data Loaded</strong><br><small>Please use the filters above to search for invoice data</small></td></tr>');
            $('#historyTableBody').html('<tr><td colspan="12" class="text-center text-muted"><i class="fas fa-history fa-2x mb-2"></i><br><strong>No History Loaded</strong><br><small>Please use the filters above to search for invoice history</small></td></tr>');

            // Filter form submission - load both tables
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                currentPage = 1;
                currentHistoryPage = 1;
                hasSearched = true;
                loadInvoiceData();
                loadHistoryData();
            });

            // Per page change
            $('#per_page').on('change', function() {
                currentPage = 1;
                loadInvoiceData();
            });

            // Load invoice data function
            function loadInvoiceData(page = 1) {
                // Don't load if user hasn't searched yet
                if (!hasSearched) {
                    return;
                }

                const formData = {
                    customer_id: $('#customer_id').val(),
                    month: $('#month').val(),
                    year: $('#year').val(),
                    keyword: $('#keyword').val(),
                    keyword_value: $('#keyword_value').val(),
                    per_page: $('#per_page').val() || '50',
                    page: page
                };

                $.ajax({
                    url: '{{ route("rbac.invoice-monitoring.data") }}',
                    type: 'GET',
                    data: formData,
                    beforeSend: function() {
                        $('#invoiceTableBody').html('<tr><td colspan="28" class="text-center">Loading data...</td></tr>');
                    },
                    success: function(response) {
                        renderInvoiceTable(response);
                        renderPagination(response, 'invoice');
                        currentPage = response.current_page;
                    },
                    error: function(xhr) {
                        toastr.error('Failed to load invoice data');
                        $('#invoiceTableBody').html('<tr><td colspan="28" class="text-center text-danger">Error loading data</td></tr>');
                    }
                });
            }

            // Load history data function - uses same filters as invoice data
            function loadHistoryData(page = 1) {
                // Don't load if user hasn't searched yet
                if (!hasSearched) {
                    return;
                }

                const formData = {
                    customer_id: $('#customer_id').val(),
                    month: $('#month').val(),
                    year: $('#year').val(),
                    keyword: $('#keyword').val(),
                    keyword_value: $('#keyword_value').val(),
                    per_page: $('#per_page').val() || '50',
                    page: page
                };

                $.ajax({
                    url: '{{ route("rbac.invoice-monitoring.history") }}',
                    type: 'GET',
                    data: formData,
                    beforeSend: function() {
                        $('#historyTableBody').html('<tr><td colspan="12" class="text-center">Loading history data...</td></tr>');
                    },
                    success: function(response) {
                        renderHistoryTable(response);
                        renderPagination(response, 'history');
                        currentHistoryPage = response.current_page;
                    },
                    error: function(xhr) {
                        toastr.error('Failed to load history data');
                        $('#historyTableBody').html('<tr><td colspan="12" class="text-center text-danger">Error loading data</td></tr>');
                    }
                });
            }

            // Render invoice table
            function renderInvoiceTable(response) {
                const tbody = $('#invoiceTableBody');
                tbody.empty();

                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        const row = `
                            <tr>
                                <td>${item.no}</td>
                                <td>${item.process_id || '-'}</td>
                                <td class="editable-field" data-id="${item.id}" data-field="Tanggal_Struk" data-value="${item.tanggal_struk}">
                                    ${item.tanggal_struk}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Toko" data-value="${item.toko}">
                                    ${item.toko}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Items" data-value="${item.items}">
                                    ${item.items}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td>${item.customer_name}</td>
                                <td>${item.tanggal_input}</td>
                                <td>${item.waktu_input}</td>
                                <td class="editable-field" data-id="${item.id}" data-field="Nomor_Invoice" data-value="${item.nomor_invoice}">
                                    ${item.nomor_invoice}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Jumlah" data-value="${item.jumlah_raw}">
                                    ${item.jumlah}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Satuan" data-value="${item.satuan}">
                                    ${item.satuan}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Harga_Satuan" data-value="${item.harga_satuan_raw}">
                                    Rp ${item.harga_satuan}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td>Rp ${item.sub_total}</td>
                                <td class="editable-field" data-id="${item.id}" data-field="Discount" data-value="${item.discount_raw}">
                                    Rp ${item.discount}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td>Rp ${item.sub_after_discount}</td>
                                <td class="editable-field" data-id="${item.id}" data-field="Refunds" data-value="${item.refunds_raw}">
                                    Rp ${item.refunds}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Pajak" data-value="${item.pajak_raw}">
                                    Rp ${item.pajak}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Ongkir" data-value="${item.ongkir_raw}">
                                    Rp ${item.ongkir}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Disc_Ongkir" data-value="${item.disc_ongkir_raw}">
                                    Rp ${item.disc_ongkir}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Voucher" data-value="${item.voucher_raw}">
                                    Rp ${item.voucher}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Asuransi_Pengiriman" data-value="${item.asuransi_pengiriman_raw}">
                                    Rp ${item.asuransi_pengiriman}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Biaya_Layanan" data-value="${item.biaya_layanan_raw}">
                                    Rp ${item.biaya_layanan}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                                <td>Rp ${item.grand_total}</td>
                                <td>${item.input_by}</td>
                                <td>
                                    ${item.file_link ? `<button onclick="openImagePreview('${item.file_link}', '${item.nomor_invoice}', '${item.customer_name}', '${item.tanggal_struk}')" class="btn-view-image" data-file="${item.file_link}">
                                        <i class="fas fa-eye"></i> View
                                    </button>` : '-'}
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Payment" data-value="${item.payment_raw}">
                                    <select class="form-control form-control-sm payment-select" style="width: auto; display: inline-block;">
                                        <option value="0" ${item.payment_raw == 0 ? 'selected' : ''}>Cash</option>
                                        <option value="1" ${item.payment_raw == 1 ? 'selected' : ''}>Tunda</option>
                                    </select>
                                    <i class="fas fa-pencil-alt edit-icon" style="display: none;"></i>
                                </td>
                                <td class="editable-field" data-id="${item.id}" data-field="Keterangan" data-value="${item.keterangan}">
                                    ${item.keterangan}
                                    <i class="fas fa-pencil-alt edit-icon"></i>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });

                    // Attach click event to editable fields
                    attachEditableEvents();
                    
                    // Re-apply row highlight if image preview is still active
                    const activeFileLink = $('.btn-view-image.active').data('file');
                    if (activeFileLink) {
                        $(`.btn-view-image[data-file="${activeFileLink}"]`).closest('tr').addClass('row-preview-active');
                    }
                } else {
                    tbody.html('<tr><td colspan="28" class="text-center">No data available</td></tr>');
                }
            }

            // Render history table
            function renderHistoryTable(response) {
                const tbody = $('#historyTableBody');
                tbody.empty();

                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        const row = `
                            <tr>
                                <td>${item.no}</td>
                                <td>${item.process_id}</td>
                                <td>${item.invoice_number}</td>
                                <td>${item.customer_name}</td>
                                <td>${item.field_name}</td>
                                <td>${item.old_value}</td>
                                <td>${item.new_value}</td>
                                <td>${item.old_total}</td>
                                <td>${item.new_total}</td>
                                <td>${item.editor_name}</td>
                                <td>${item.edited_at}</td>
                                <td>${item.ip_address}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                } else {
                    tbody.html('<tr><td colspan="12" class="text-center">No history data available</td></tr>');
                }
            }

            // Render pagination
            function renderPagination(response, type) {
                const paginationId = type === 'invoice' ? '#pagination' : '#history-pagination';
                const paginationInfoId = type === 'invoice' ? '#pagination-info' : '#history-pagination-info';
                const pagination = $(paginationId);
                const paginationInfo = $(paginationInfoId);
                
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
                            if (type === 'invoice') {
                                loadInvoiceData(page);
                            } else {
                                loadHistoryData(page);
                            }
                        }
                    });
                }
            }

            // Attach editable events
            function attachEditableEvents() {
                $('.editable-field').off('click').on('click', function() {
                    const cell = $(this);
                    if (cell.find('input').length > 0 || cell.find('select').length > 0) return;

                    const id = cell.data('id');
                    const field = cell.data('field');
                    const value = cell.data('value');

                    if (field === 'Payment') {
                        // Handle payment dropdown
                        const select = $('<select>')
                            .addClass('form-control form-control-sm payment-select')
                            .attr('style', 'width: auto; display: inline-block;')
                            .data('id', id)
                            .data('field', field)
                            .data('original', value);

                        select.append(`<option value="0" ${value == 0 ? 'selected' : ''}>Cash</option>`);
                        select.append(`<option value="1" ${value == 1 ? 'selected' : ''}>Tunda</option>`);

                        cell.html(select);
                        select.focus();

                        select.on('blur change', function() {
                            const newValue = $(this).val();
                            const originalValue = $(this).data('original');

                            if (newValue !== originalValue) {
                                saveEdit(id, field, newValue, cell);
                            } else {
                                cancelEdit(cell, originalValue, field);
                            }
                        });

                        select.on('keypress', function(e) {
                            if (e.which === 27) { // Escape key
                                cancelEdit(cell, value, field);
                            }
                        });
                    } else {
                        // Handle text input fields
                        const displayValue = cell.text().trim().replace('Rp ', '').replace(/\./g, '').replace(',', '.');

                        const input = $('<input>')
                            .attr('type', 'text')
                            .addClass('editing-input')
                            .val(displayValue)
                            .data('id', id)
                            .data('field', field)
                            .data('original', value);

                        cell.html(input);
                        input.focus().select();

                        input.on('blur', function() {
                            const newValue = $(this).val();
                            const originalValue = $(this).data('original');

                            if (newValue !== originalValue) {
                                saveEdit(id, field, newValue, cell);
                            } else {
                                cancelEdit(cell, originalValue, field);
                            }
                        });

                        input.on('keypress', function(e) {
                            if (e.which === 13) { // Enter key
                                $(this).blur();
                            } else if (e.which === 27) { // Escape key
                                cancelEdit(cell, value, field);
                            }
                        });
                    }
                });
            }

            // Save edit
            function saveEdit(id, field, value, cell) {
                const originalValue = cell.data('value');
                
                $.ajax({
                    url: `/rbac/invoice-monitoring/${id}`,
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        field: field,
                        value: value
                    },
                    beforeSend: function() {
                        cell.html('<i class="fas fa-spinner fa-spin"></i>');
                    },
                    success: function(response) {
                        if (response.success) {
                            // Format values for display
                            let beforeValue = formatValueForDisplay(field, originalValue);
                            let afterValue = formatValueForDisplay(field, value);
                            
                            // Show success message with before/after details
                            toastr.success(
                                `<strong>Field:</strong> ${field}<br>` +
                                `<strong>Sebelum:</strong> ${beforeValue}<br>` +
                                `<strong>Sesudah:</strong> ${afterValue}`,
                                'Data Updated Successfully',
                                {
                                    timeOut: 5000,
                                    closeButton: true,
                                    progressBar: true,
                                    escapeHtml: false
                                }
                            );
                            
                            // Update cell with new value without page refresh
                            updateCellDisplay(cell, field, value, response.data);
                        } else {
                            toastr.error('Failed to update data');
                            cancelEdit(cell, originalValue, field);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error updating data: ' + (xhr.responseJSON?.error || 'Unknown error'));
                        cancelEdit(cell, originalValue, field);
                    }
                });
            }

            // Format value for display in toastr
            function formatValueForDisplay(field, value) {
                if (field === 'Harga_Satuan') {
                    return 'Rp ' + parseFloat(value).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                } else if (field === 'Jumlah') {
                    return parseFloat(value).toFixed(2);
                } else if (['Discount', 'Refunds', 'Pajak', 'Ongkir', 'Disc_Ongkir', 'Voucher', 'Asuransi_Pengiriman', 'Biaya_Layanan'].includes(field)) {
                    return 'Rp ' + parseFloat(value).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                } else if (field === 'Payment') {
                    return value == 0 ? 'Cash' : (value == 1 ? 'Tunda' : '-');
                } else if (field === 'Tanggal_Struk') {
                    // If value is already in dd-mm-yyyy format, return as is
                    if (value.includes('-')) {
                        return value;
                    }
                    // Otherwise format it
                    return value;
                }
                return value || '-';
            }

            // Update cell display without page refresh
            function updateCellDisplay(cell, field, value, responseData) {
                let displayValue = value;
                
                if (field === 'Harga_Satuan') {
                    displayValue = 'Rp ' + parseFloat(value).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                } else if (field === 'Jumlah') {
                    displayValue = parseFloat(value).toFixed(2);
                } else if (['Discount', 'Refunds', 'Pajak', 'Ongkir', 'Disc_Ongkir', 'Voucher', 'Asuransi_Pengiriman', 'Biaya_Layanan'].includes(field)) {
                    displayValue = 'Rp ' + parseFloat(value).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                } else if (field === 'Payment') {
                    displayValue = `<select class="form-control form-control-sm payment-select" style="width: auto; display: inline-block;">
                        <option value="0" ${value == 0 ? 'selected' : ''}>Cash</option>
                        <option value="1" ${value == 1 ? 'selected' : ''}>Tunda</option>
                    </select>
                    <i class="fas fa-pencil-alt edit-icon" style="display: none;"></i>`;
                    cell.html(displayValue);
                    cell.data('value', value);
                    return;
                }
                
                cell.html(displayValue + ' <i class="fas fa-pencil-alt edit-icon"></i>');
                cell.data('value', value);
                
                // Update related cells if they exist in response
                const row = cell.closest('tr');
                if (responseData) {
                    // Update Sub Total if changed (column index 12)
                    if (responseData.sub_total) {
                        row.find('td:eq(12)').text('Rp ' + responseData.sub_total);
                    }
                    // Update Sub After Discount if changed (column index 14)
                    if (responseData.sub_after_discount) {
                        row.find('td:eq(14)').text('Rp ' + responseData.sub_after_discount);
                    }
                    // Update Grand Total if changed (column index 22)
                    if (responseData.grand_total) {
                        row.find('td:eq(22)').text('Rp ' + responseData.grand_total);
                    }
                }
            }

            // Cancel edit
            function cancelEdit(cell, value, field) {
                let displayValue = value;
                if (field === 'Harga_Satuan') {
                    displayValue = 'Rp ' + parseFloat(value).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                } else if (field === 'Jumlah') {
                    displayValue = parseFloat(value).toFixed(2);
                } else if (['Discount', 'Refunds', 'Pajak', 'Ongkir', 'Disc_Ongkir', 'Voucher', 'Asuransi_Pengiriman', 'Biaya_Layanan'].includes(field)) {
                    displayValue = 'Rp ' + parseFloat(value).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                } else if (field === 'Payment') {
                    displayValue = `<select class="form-control form-control-sm payment-select" style="width: auto; display: inline-block;">
                        <option value="0" ${value == 0 ? 'selected' : ''}>Cash</option>
                        <option value="1" ${value == 1 ? 'selected' : ''}>Tunda</option>
                    </select>
                    <i class="fas fa-pencil-alt edit-icon" style="display: none;"></i>`;
                    cell.html(displayValue);
                    return;
                } else if (['Nomor_Invoice', 'Tanggal_Struk', 'Toko', 'Items', 'Satuan', 'Keterangan'].includes(field)) {
                    // Text fields - display as is
                    displayValue = value || '-';
                }
                cell.html(displayValue + ' <i class="fas fa-pencil-alt edit-icon"></i>');
            }

            // Image Preview Functions
            window.openImagePreview = function(fileLink, invoiceNumber, customerName, tanggalStruk) {
                const panel = $('#imagePreviewPanel');
                const mainContainer = $('#mainContainer');
                const previewContent = $('#previewContent');
                
                // Convert Google Drive download link to viewable image link
                let imageUrl = fileLink;
                const downloadUrl = fileLink; // Keep original for download
                
                // Check if it's a Google Drive link
                if (fileLink.includes('drive.google.com')) {
                    // Extract file ID from various Google Drive URL formats
                    let fileId = null;
                    
                    // Format: https://drive.google.com/uc?id=FILE_ID&export=download
                    // or: https://drive.google.com/uc?id=FILE_ID&amp;export=download
                    const ucMatch = fileLink.match(/[?&]id=([^&]+)/);
                    if (ucMatch) {
                        fileId = ucMatch[1];
                    }
                    
                    // If file ID found, convert to thumbnail/preview URL
                    if (fileId) {
                        // Use Google Drive thumbnail API for better compatibility
                        // Size options: s220, s400, s640, s1024, etc.
                        imageUrl = `https://drive.google.com/thumbnail?id=${fileId}&sz=w1000`;
                    }
                }
                
                // Show panel
                panel.addClass('active');
                mainContainer.addClass('table-container-with-preview');
                
                // Show loading
                previewContent.html(`
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin fa-3x"></i>
                        <p>Loading image...</p>
                    </div>
                `);
                
                // Highlight active button and row
                $('.btn-view-image').removeClass('active');
                $('#invoiceTable tbody tr').removeClass('row-preview-active');
                
                const activeButton = $(`.btn-view-image[data-file="${fileLink}"]`);
                activeButton.addClass('active');
                activeButton.closest('tr').addClass('row-preview-active');
                
                // Load image
                const img = new Image();
                img.onload = function() {
                    previewContent.html(`
                        <div class="preview-info">
                            <p><strong>Invoice:</strong> ${invoiceNumber}</p>
                            <p><strong>Customer:</strong> ${customerName}</p>
                            <p><strong>Date:</strong> ${tanggalStruk}</p>
                        </div>
                        <img src="${imageUrl}" class="preview-image" alt="Receipt Image">
                        <div class="text-center mt-2">
                            <a href="${downloadUrl}" download class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i> Download
                            </a>
                            <a href="${imageUrl}" target="_blank" class="btn btn-sm btn-secondary">
                                <i class="fas fa-external-link-alt"></i> Open in New Tab
                            </a>
                        </div>
                    `);
                };
                
                img.onerror = function() {
                    previewContent.html(`
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p><strong>Failed to load image</strong></p>
                            <p>The image file may not be accessible or the link is invalid.</p>
                            <a href="${downloadUrl}" target="_blank" class="btn btn-sm btn-primary mt-2">
                                <i class="fas fa-external-link-alt"></i> Try Opening in New Tab
                            </a>
                        </div>
                    `);
                };
                
                img.src = imageUrl;
            };

            window.closeImagePreview = function() {
                $('#imagePreviewPanel').removeClass('active');
                $('#mainContainer').removeClass('table-container-with-preview');
                $('.btn-view-image').removeClass('active');
                $('#invoiceTable tbody tr').removeClass('row-preview-active');
            };

            // Close preview when pressing Escape key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#imagePreviewPanel').hasClass('active')) {
                    closeImagePreview();
                }
            });
        });
    </script>
@endpush
