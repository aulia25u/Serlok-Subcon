@extends('layouts.app')

@section('title', 'Surat Jalan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Surat Jalan Management</h3>
                        <div class="card-tools d-none">
                            <button type="button" class="btn btn-primary btn-sm" id="addSuratJalanBtn" data-toggle="modal"
                                data-target="#suratJalanModal">
                                <i class="fas fa-plus"></i> Add Surat Jalan
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="date_filter">Date Range</label>
                                <input type="text" id="date_filter" class="form-control" placeholder="Select Date Range">
                            </div>
                            <div class="col-md-3">
                                <label for="status_filter">Status</label>
                                <select id="status_filter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Printed">Printed</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="customer_filter">Customer / Delivery To</label>
                                <select id="customer_filter" class="form-control">
                                    <option value="">All Customers</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="resetBtn" class="btn btn-secondary btn-block">Reset</button>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped" id="suratJalanTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Document Number</th>
                                    <th>Customer</th>
                                    <th>Outgoing ID</th>
                                    <th>Made By</th>
                                    <th>Inspect By</th>
                                    <th>Known By</th>
                                    <th>Delivery To</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="suratJalanModal" tabindex="-1" role="dialog" aria-labelledby="suratJalanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="suratJalanModalLabel">Add Surat Jalan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="suratJalanForm">
                    @csrf
                    <input type="hidden" id="surat-jalan_id" name="id">
                    <input type="hidden" id="surat-jalan_form_method" name="_method" value="POST">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            Document Number will be auto-generated upon saving based on the Master Variable format.
                        </div>
                        <div class="form-group">
                            <label for="employee_job_id">Select Employee Job</label>
                            <select name="employee_job_id" id="employee_job_id" class="form-control" required
                                style="width: 100%">
                                <option value="">Select Job</option>
                                @foreach($employeeJobs as $job)
                                    <option value="{{ $job->id }}">
                                        Job ID: {{ $job->id }} - Item: {{ $job->outgoing->masterItem->item_name ?? 'N/A' }}
                                        (Qty OK: {{ $job->qty_ok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="customer_id">Select Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control" required style="width: 100%">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="known_by">Known By</label>
                            <select name="known_by" id="known_by" class="form-control" style="width: 100%">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->name }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="Draft">Draft</option>
                                <option value="Printed">Printed</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save & Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="suratJalanDetailModal" tabindex="-1" role="dialog"
        aria-labelledby="suratJalanDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="suratJalanDetailModalLabel">
                        <i class="fas fa-info-circle text-info mr-2"></i> Detail Surat Jalan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Document Number</th>
                            <td id="detail_document_number"></td>
                        </tr>
                        <tr>
                            <th>ID Employee Jobs</th>
                            <td id="detail_id_employee_jobs"></td>
                        </tr>
                        <tr>
                            <th>Item Information</th>
                            <td id="detail_item_information"></td>
                        </tr>
                        <tr>
                            <th>Qty Delivery</th>
                            <td id="detail_qty_delivery"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

            // Auto-submit for filters
            $('#status_filter, #customer_filter').on('change', function () {
                crudManager.table.draw();
            });

            // Custom Reset Logic
            $('#resetBtn').click(function () {
                $('#date_filter').val('');
                $('#status_filter').val('');
                $('#customer_filter').val('');
                crudManager.table.draw();
            });

            const crudManager = new CrudManager({
                entity: 'surat-jalan',
                routes: {
                    index: "{{ route('rbac.surat-jalan.index') }}",
                    store: "{{ route('rbac.surat-jalan.store') }}",
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'document_number', name: 'document_number' },
                    { data: 'customer_name', name: 'customer.customer_name', title: 'Customer' },
                    { data: 'warehouse_outgoing_id', name: 'employeeJob.outgoing_id', title: 'Warehouse Outgoing ID' },
                    { data: 'made_by', name: 'employeeJob.user.name', title: 'Made By' },
                    { data: 'inspect_by', name: 'employeeJob.inspector.name', title: 'Inspect By' },
                    { data: 'known_by', name: 'known_by', title: 'Known By' },
                    { data: 'delivery_to', name: 'customer.customer_name', title: 'Delivery To' },
                    { data: 'surat_jalan_date', name: 'surat_jalan_date' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                modalId: '#suratJalanModal',
                formId: '#suratJalanForm',
                tableId: '#suratJalanTable',
                addBtnId: '#addSuratJalanBtn',
                resetBtnId: '#resetBtn',
                dateFilters: false, // Using manual configuration
                ajaxData: function (d) {
                    d.date_range = $('#date_filter').val();
                    d.status = $('#status_filter').val();
                    d.customer_id = $('#customer_filter').val();
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
                            text: '<i class="fas fa-plus"></i> Add Surat Jalan',
                            className: 'btn btn-primary',
                            action: function (e, dt, node, config) {
                                $('#addSuratJalanBtn').trigger('click');
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
                    $('#status').val('Draft');
                    $('#employee_job_id').val('');
                    $('#customer_id').val('');
                    $('#known_by').val('');
                },
                onEdit: function (response) {
                    $('#status').val(response.status);
                    $('#employee_job_id').val(response.employee_job_id);
                    $('#customer_id').val(response.customer_id);
                    $('#known_by').val(response.known_by);
                }
            });

            // Detail Button Logic
            $(document).on('click', '.surat-jalan-detail-btn', function () {
                const id = $(this).data('id');
                const url = "{{ route('rbac.surat-jalan.index') }}/" + id; // Using show method locally or resourceful

                $.get(url, function (response) {
                    $('#detail_document_number').text(response.document_number);
                    $('#detail_id_employee_jobs').text(response.id_employee_jobs);
                    $('#detail_item_information').text(response.item_information);
                    $('#detail_qty_delivery').text(response.qty_delivery);
                    $('#suratJalanDetailModal').modal('show');
                }).fail(function () {
                    toastr.error('Failed to load details.');
                });
            });
        });
    </script>
@endpush