@extends('layouts.app')

@section('title', 'Inventory Capture')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Inventory Capture History</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="runCaptureBtn">
                                <i class="fas fa-camera"></i> Run Capture
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="filter_month">Month</label>
                                <select id="filter_month" class="form-control">
                                    <option value="">All Months</option>
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="filter_year">Year</label>
                                <select id="filter_year" class="form-control">
                                    <option value="">All Years</option>
                                    @foreach(range(date('Y'), date('Y') - 5) as $y)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="resetBtn" class="btn btn-secondary btn-block">Reset</button>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped" id="inventoryCaptureTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Item Name</th>
                                    <th>Item Code</th>
                                    <th>Quantity</th>
                                    <th>Capture Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .dt-layout-cell.dt-start {
            display: flex !important;
            align-items: center;
            gap: 10px;
        }

        .dt-layout-cell.dt-start select {
            width: auto !important;
            min-width: 60px;
            padding-right: 30px !important;
        }

        .dt-search {
            position: relative;
        }

        .dt-search input {
            padding-left: 30px !important;
            border-radius: 3px !important;
        }

        .dt-search::before {
            content: "\f002";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            z-index: 1;
        }
    </style>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#inventoryCaptureTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.inventory.capture') }}",
                    data: function (d) {
                        d.month = $('#filter_month').val();
                        d.year = $('#filter_year').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'item_name', name: 'item_name' },
                    { data: 'item_code', name: 'item_code' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'captured_at', name: 'captured_at' },
                ],
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
                    lengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search"
                }
            });

            $('#filter_month, #filter_year').change(function () {
                table.draw();
            });

            $('#resetBtn').click(function () {
                $('#filter_month').val('');
                $('#filter_year').val('');
                table.draw();
            });

            $('#runCaptureBtn').click(function () {
                if (confirm('Are you sure you want to capture current stock?')) {
                    $.ajax({
                        url: "{{ route('rbac.inventory.capture.store') }}",
                        type: 'POST',
                        success: function (response) {
                            toastr.success(response.success);
                            table.draw();
                        },
                        error: function (xhr) {
                            toastr.error(xhr.responseJSON.error || 'Failed to capture stock');
                        }
                    });
                }
            });
        });
    </script>
@endpush