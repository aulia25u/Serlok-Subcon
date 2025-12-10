@extends('layouts.app')

@section('title', 'Employee Jobs')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Employee Jobs Management</h3>
                        <div class="card-tools d-none">
                            <button type="button" class="btn btn-primary btn-sm" id="addJobBtn" data-toggle="modal"
                                data-target="#jobModal">
                                <i class="fas fa-plus"></i> Add Job
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="date_filter">Created Date Range</label>
                                <input type="text" id="date_filter" class="form-control" placeholder="Select Date Range">
                            </div>
                            <div class="col-md-4">
                                <label for="status_filter">Surat Jalan Status</label>
                                <select id="status_filter" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Printed">Printed</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="resetBtn" class="btn btn-secondary btn-block">Reset</button>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped" id="jobTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Outgoing Item</th>
                                    <th>Employee</th>
                                    <th>Created Date</th>
                                    <th>Start Date</th>
                                    <th>Finished Date</th>
                                    <th>QTY Outgoing</th>
                                    <th>QTY OK</th>
                                    <th>QTY NG</th>
                                    <th>QTY NG Cust</th>
                                    <th>Inspect By</th>
                                    <th>SJ Status</th>
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
    <div class="modal fade" id="jobModal" tabindex="-1" role="dialog" aria-labelledby="jobModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jobModalLabel">Add Employee Job</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="jobForm">
                    @csrf
                    <input type="hidden" id="employee-job_id" name="id">
                    <input type="hidden" id="employee-job_form_method" name="_method" value="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="outgoing_id">Outgoing (Item - Employee - Date)</label>
                                    <select name="outgoing_id" id="outgoing_id" class="form-control" required>
                                        <option value="">Select Outgoing</option>
                                        @foreach($outgoings as $outgoing)
                                            <option value="{{ $outgoing->id }}"
                                                data-employee="{{ $outgoing->assignedUser ? $outgoing->assignedUser->name : 'N/A' }}"
                                                data-qty="{{ $outgoing->quantity ?? '0' }}"
                                                data-date="{{ $outgoing->outgoing_date ? $outgoing->outgoing_date->format('Y-m-d H:i') : '' }}">
                                                {{ $outgoing->masterItem->item_name }} ({{ $outgoing->masterItem->item_code }})
                                                - {{ $outgoing->assignedUser->name }} -
                                                {{ $outgoing->outgoing_date->format('Y-m-d') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Employee (Auto-filled)</label>
                                    <input type="text" id="display_employee" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Created Date (Auto-filled)</label>
                                    <input type="text" id="display_date" class="form-control" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Outgoing QTY (Auto-filled)</label>
                                    <input type="text" id="display_qty" class="form-control" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_datetime">Start Date & Time</label>
                                    <input type="datetime-local" name="start_datetime" id="start_datetime"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="finished_datetime">Finished Date & Time</label>
                                    <input type="datetime-local" name="finished_datetime" id="finished_datetime"
                                        class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty_ok">QTY OK</label>
                                    <input type="number" name="qty_ok" id="qty_ok" class="form-control" required min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty_ng">QTY NG</label>
                                    <input type="number" name="qty_ng" id="qty_ng" class="form-control" required min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qty_ng_customer">QTY NG Customer</label>
                                    <input type="number" name="qty_ng_customer" id="qty_ng_customer" class="form-control"
                                        required min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="inspector_id">Inspect By</label>
                                    <select name="inspector_id" id="inspector_id" class="form-control" required>
                                        <option value="">Select Inspector</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="surat_jalan_status">Surat Jalan Status</label>
                                    <input type="text" name="surat_jalan_status" id="surat_jalan_status"
                                        class="form-control">
                                </div>
                            </div>
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
            $('#date_filter').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#date_filter').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                crudManager.table.draw();
            });

            $('#date_filter').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                crudManager.table.draw();
            });

            // Auto-submit for status filter
            $('#status_filter').on('change', function () {
                crudManager.table.draw();
            });

            // Custom Reset Logic
            $('#resetBtn').click(function () {
                $('#date_filter').val('');
                $('#status_filter').val('');
                crudManager.table.draw();
            });

            // Show outgoing details on change
            $('#outgoing_id').change(function () {
                var selected = $(this).find('option:selected');
                var employee = selected.data('employee');
                var date = selected.data('date');
                var qty = selected.data('qty');
                $('#display_employee').val(employee);
                $('#display_date').val(date);
                $('#display_qty').val(qty);
            });

            const crudManager = new CrudManager({
                entity: 'employee-job',
                routes: {
                    index: "{{ route('rbac.employee-jobs.index') }}",
                    store: "{{ route('rbac.employee-jobs.store') }}",
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'outgoing_item', name: 'outgoing.masterItem.item_name' },
                    { data: 'employee_name', name: 'user.name' },
                    { data: 'created_datetime', name: 'created_datetime' },
                    { data: 'start_datetime', name: 'start_datetime' },

                    { data: 'finished_datetime', name: 'finished_datetime' },
                    { data: 'outgoing_qty', name: 'outgoing.quantity' },
                    { data: 'qty_ok', name: 'qty_ok' },
                    { data: 'qty_ng', name: 'qty_ng' },
                    { data: 'qty_ng_customer', name: 'qty_ng_customer' },
                    { data: 'inspector_name', name: 'inspector.name' },
                    { data: 'surat_jalan_status', name: 'surat_jalan_status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                modalId: '#jobModal',
                formId: '#jobForm',
                tableId: '#jobTable',
                resetBtnId: '#resetBtn',
                addBtnId: '#addJobBtn',
                dateFilters: false, // We hand-rolled this
                ajaxData: function (d) {
                    d.date_range = $('#date_filter').val();
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
                            text: '<i class="fas fa-plus"></i> Add Job',
                            className: 'btn btn-primary',
                            action: function (e, dt, node, config) {
                                $('#addJobBtn').trigger('click');
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
                    $('#outgoing_id').val('').trigger('change');
                    $('#start_datetime').val('');
                    $('#finished_datetime').val('');
                    $('#qty_ok').val('');
                    $('#qty_ng').val('');
                    $('#qty_ng_customer').val('');
                    $('#inspector_id').val('');
                    $('#surat_jalan_status').val('');
                    $('#display_employee').val('');
                    $('#display_date').val('');
                    $('#display_qty').val('');
                },
                onEdit: function (response) {
                    $('#outgoing_id').val(response.outgoing_id).trigger('change');
                    $('#start_datetime').val(response.start_datetime ? response.start_datetime.replace(' ', 'T') : '');
                    $('#finished_datetime').val(response.finished_datetime ? response.finished_datetime.replace(' ', 'T') : '');
                    $('#qty_ok').val(response.qty_ok);
                    $('#qty_ng').val(response.qty_ng);
                    $('#qty_ng_customer').val(response.qty_ng_customer);
                    $('#inspector_id').val(response.inspector_id);
                    $('#surat_jalan_status').val(response.surat_jalan_status);

                    setTimeout(() => {
                        var selected = $('#outgoing_id').find('option:selected');
                        $('#display_employee').val(selected.data('employee'));
                        $('#display_date').val(selected.data('date'));
                        $('#display_qty').val(selected.data('qty'));
                    }, 100);
                }
            });
        });
    </script>
@endpush