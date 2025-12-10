@extends('layouts.app')

@section('title', 'Outgoing')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Outgoing Management</h3>
                        <div class="card-tools d-none">
                            <button type="button" class="btn btn-primary btn-sm" id="addOutgoingBtn" data-toggle="modal"
                                data-target="#outgoingModal">
                                <i class="fas fa-plus"></i> Add Outgoing
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="outgoing_date_filter">Date Outgoing</label>
                                <input type="text" id="outgoing_date_filter" class="form-control"
                                    placeholder="Select Date Range">
                            </div>
                            <div class="col-md-4">
                                <label for="status_filter">Status</label>
                                <select id="status_filter" class="form-control">
                                    <option value="">All</option>
                                    <option value="un-verified">Un-verified</option>
                                    <option value="verified">Verified</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="resetBtn" class="btn btn-secondary btn-block">Reset</button>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped" id="outgoingTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Item Name</th>
                                    <th>Item Code</th>
                                    <th>Assigned To</th>
                                    <th>Assign By</th>
                                    <th>Quantity</th>
                                    <th>Date Outgoing</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="outgoingModal" tabindex="-1" role="dialog" aria-labelledby="outgoingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="outgoingModalLabel">Add Outgoing</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="outgoingForm">
                    @csrf
                    <input type="hidden" id="outgoing_id" name="id">
                    <input type="hidden" id="outgoing_form_method" name="_method" value="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="master_item_id">Item</label>
                            <select name="master_item_id" id="master_item_id" class="form-control" required>
                                <option value="">Select Item</option>
                                @foreach($masterItems as $item)
                                    <option value="{{ $item->id }}">{{ $item->item_name }} ({{ $item->item_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="user_id">Assigned To</label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" step="0.01" name="quantity" id="quantity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="outgoing_date">Date & Time</label>
                            <input type="datetime-local" name="outgoing_date" id="outgoing_date" class="form-control"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
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
            color: #aaa;
            pointer-events: none;
            z-index: 1;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@push('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('js/crud-manager.js') }}"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DateRangePicker
            $('#outgoing_date_filter').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#outgoing_date_filter').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                crudManager.table.draw();
            });

            $('#outgoing_date_filter').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                crudManager.table.draw();
            });

            // Auto-submit for status filter
            $('#status_filter').change(function () {
                crudManager.table.draw();
            });

            // Custom Reset Logic
            $('#resetBtn').click(function () {
                $('#outgoing_date_filter').val('');
                $('#status_filter').val('');
                crudManager.table.draw();
            });

            const crudManager = new CrudManager({
                entity: 'outgoing',
                routes: {
                    index: "{{ route('rbac.outgoing.index') }}",
                    store: "{{ route('rbac.outgoing.store') }}",
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'item_name', name: 'item_name' },
                    { data: 'item_code', name: 'item_code' },
                    { data: 'assigned_to', name: 'assigned_to' },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'outgoing_date', name: 'outgoing_date' },
                    { data: 'notes', name: 'notes' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                modalId: '#outgoingModal',
                formId: '#outgoingForm',
                tableId: '#outgoingTable',
                resetBtnId: '#resetBtn',
                addBtnId: '#addOutgoingBtn',
                dateFilters: false,
                ajaxData: function (d) {
                    d.outgoing_date = $('#outgoing_date_filter').val();
                    d.status = $('#status_filter').val();
                },
                options: {
                    layout: {
                        topStart: 'search',
                        topEnd: 'buttons',
                        bottomStart: ['pageLength', 'info'],
                        bottomEnd: 'paging'
                    },
                    buttons: [
                        {
                            text: '<i class="fas fa-plus"></i> Add Outgoing',
                            className: 'btn btn-primary',
                            action: function (e, dt, node, config) {
                                $('#addOutgoingBtn').trigger('click');
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
                },
                onAdd: function () {
                    $('#master_item_id').val('');
                    $('#user_id').val('');
                    $('#quantity').val('');
                    $('#outgoing_date').val('');
                    $('#notes').val('');
                }
            });

            // Verify Button Logic
            $(document).on('click', '.outgoing-verify-btn', function () {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to verify this record?')) {
                    $.ajax({
                        url: "/rbac/outgoing/" + id + "/verify",
                        type: 'POST',
                        success: function (response) {
                            toastr.success(response.success);
                            crudManager.table.draw();
                        },
                        error: function (xhr) {
                            toastr.error(xhr.responseJSON.error || 'Failed to verify record');
                        }
                    });
                }
            });
        });
    </script>
@endpush