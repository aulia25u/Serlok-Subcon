@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Inventory Management</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Item Name</th>
                                    <th>Item Code</th>
                                    <th>Quantity</th>
                                    <th>Updated At</th>
                                    <th>History</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel">Inventory History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-sm" id="historyTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Change Reason</th>
                                <th>Old Qty</th>
                                <th>New Qty</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css" />
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
            color: #aaa;
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
    <script>
        $(document).ready(function () {
            $('#inventoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('rbac.inventory') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'item_name', name: 'item_name' },
                    { data: 'item_code', name: 'item_code' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'history', name: 'history', orderable: false, searchable: false },
                    { data: 'min_stock', name: 'masterItem.min_stock', visible: false, searchable: false },
                ],
                createdRow: function (row, data, dataIndex) {
                    var qty = parseFloat(data.quantity); // Extract number if format is "10 PCS"
                    var minStock = parseFloat(data.min_stock) || 0;

                    if (minStock > 0 && qty <= minStock) {
                        $(row).addClass('table-danger');
                        $(row).find('td:eq(3)').append(' <span class="badge badge-danger">Low Stock</span>');
                    }
                },
                layout: {
                    topStart: 'search',
                    topEnd: 'buttons',
                    bottomStart: ['pageLength', 'info'],
                    bottomEnd: 'paging'
                },
                buttons: [
                    {
                        text: '<i class="fas fa-camera"></i> Captured Stock',
                        className: 'btn btn-warning',
                        action: function (e, dt, node, config) {
                            window.location.href = "{{ route('rbac.inventory.capture') }}";
                        }
                    },
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

            // History Modal handling
            $('#inventoryTable').on('click', '.btn-history', function () {
                var btn = $(this);
                var id = btn.data('id');
                var modal = $('#historyModal');
                var tbody = $('#historyTableBody');

                tbody.html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
                modal.modal('show');

                $.ajax({
                    url: "/rbac/inventory/history/" + id,
                    type: "GET",
                    success: function (logs) {
                        tbody.empty();
                        if (logs.length === 0) {
                            tbody.html('<tr><td colspan="5" class="text-center">No history found.</td></tr>');
                            return;
                        }

                        logs.forEach(function (log) {
                            var row = '<tr>' +
                                '<td>' + log.date + '</td>' +
                                '<td>' + log.user + '</td>' +
                                '<td>' + log.reason + '</td>' +
                                '<td>' + log.old_qty + '</td>' +
                                '<td>' + log.new_qty + '</td>' +
                                '</tr>';
                            tbody.append(row);
                        });
                    },
                    error: function () {
                        tbody.html('<tr><td colspan="5" class="text-center text-danger">Failed to load history.</td></tr>');
                    }
                });
            });
        });


    </script>
@endpush