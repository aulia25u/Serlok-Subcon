@extends('layouts.app')

@section('title', 'Receiving')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Receiving Management</h3>
                        <div class="card-tools d-none">
                            <button type="button" class="btn btn-primary btn-sm" id="addReceivingBtn" data-toggle="modal"
                                data-target="#receivingModal">
                                <i class="fas fa-plus"></i> Add Receiving
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="delivery_date_filter">Delivery Date</label>
                                <input type="date" id="delivery_date_filter" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="incoming_date_filter">Incoming Date</label>
                                <input type="text" id="incoming_date_filter" class="form-control"
                                    placeholder="Select Date Range">
                            </div>
                            <div class="col-md-2">
                                <label for="status_filter">Status</label>
                                <select id="status_filter" class="form-control">
                                    <option value="">All</option>
                                    <option value="Waiting">Waiting</option>
                                    <option value="Verified">Verified</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="ng_status_filter">NG Status</label>
                                <select id="ng_status_filter" class="form-control">
                                    <option value="">All</option>
                                    <option value="OK">OK</option>
                                    <option value="NG">NG</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button id="resetBtn" class="btn btn-secondary btn-block">Reset</button>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped" id="receivingTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Item Name</th>
                                    <th>Doc No Internal</th>
                                    <th>Doc No Customer</th>
                                    <th>Status</th>
                                    <th>Delivery Date</th>
                                    <th>Incoming Date</th>
                                    <th>Received By</th>
                                    <th>Qty Pack</th>
                                    <th>Qty/Pack</th>
                                    <th>NG Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="receivingModal" tabindex="-1" role="dialog" aria-labelledby="receivingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="receivingModalLabel">Add Receiving</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="receivingForm">
                    @csrf
                    <input type="hidden" id="receiving_id" name="id">
                    <input type="hidden" id="receiving_form_method" name="_method" value="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="master_item_id">Item</label>
                                    <select name="master_item_id" id="master_item_id" class="form-control" required>
                                        <option value="">Select Item</option>
                                        @foreach($masterItems as $item)
                                            <option value="{{ $item->id }}">{{ $item->item_name }} ({{ $item->item_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_status">Product Status</label>
                                    <select name="product_status" id="product_status" class="form-control" required>
                                        <option value="Waiting">Waiting</option>
                                        <option value="Verified">Verified</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="doc_number_internal">Doc Number Internal</label>
                                    <input type="text" name="doc_number_internal" id="doc_number_internal"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="doc_number_customer">Doc Number Customer</label>
                                    <input type="text" name="doc_number_customer" id="doc_number_customer"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="qrcode_customer">QR Code Customer</label>
                                    <input type="text" name="qrcode_customer" id="qrcode_customer" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="delivery_date_customer">Delivery Date Customer</label>
                                    <input type="date" name="delivery_date_customer" id="delivery_date_customer"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="delivery_by">Delivery By</label>
                                    <input type="text" name="delivery_by" id="delivery_by" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qty_pack">Qty Pack</label>
                                    <input type="number" step="0.01" name="qty_pack" id="qty_pack" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qty_per_pack">Qty Per Pack</label>
                                    <input type="number" step="0.01" name="qty_per_pack" id="qty_per_pack"
                                        class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ng_customer">NG Customer</label>
                                    <select name="ng_customer" id="ng_customer" class="form-control" required>
                                        <option value="OK">OK</option>
                                        <option value="NG">NG</option>
                                    </select>
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
            $('#incoming_date_filter').daterangepicker({

                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#incoming_date_filter').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                crudManager.table.draw();
            });

            $('#incoming_date_filter').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                crudManager.table.draw();
            });

            // Auto-submit for other filters
            $('#delivery_date_filter, #status_filter, #ng_status_filter').on('change', function () {
                crudManager.table.draw();
            });

            const crudManager = new CrudManager({

                entity: 'receiving',
                routes: {
                    index: "{{ route('rbac.receiving') }}",
                    store: "{{ route('rbac.receiving.store') }}",
                }

                ,
                columns: [{
                    data: 'id', name: 'id'
                }

                    ,
                {
                    data: 'item_name', name: 'item_name'
                }

                    ,
                {
                    data: 'doc_number_internal', name: 'doc_number_internal'
                }

                    ,
                {
                    data: 'doc_number_customer', name: 'doc_number_customer'
                }

                    ,
                {
                    data: 'product_status', name: 'product_status'
                }

                    ,
                {
                    data: 'delivery_date_customer', name: 'delivery_date_customer'
                }

                    ,
                {
                    data: 'incoming_date', name: 'incoming_date'
                }

                    ,
                {
                    data: 'receiver_name', name: 'receiver_name'
                }

                    ,
                {
                    data: 'qty_pack', name: 'qty_pack'
                }

                    ,
                {
                    data: 'qty_per_pack', name: 'qty_per_pack'
                }

                    ,
                {
                    data: 'ng_customer', name: 'ng_customer'
                }

                    ,
                ],
                modalId: '#receivingModal',
                formId: '#receivingForm',
                tableId: '#receivingTable',
                filterBtnId: '#filterBtn',
                resetBtnId: '#resetBtn',
                addBtnId: '#addReceivingBtn',
                dateFilters: false, // Disable default date filters

                ajaxData: function (data) {
                    data.incoming_date = $('#incoming_date_filter').val();
                    data.status = $('#status_filter').val();
                    data.ng_status = $('#ng_status_filter').val();
                    data.delivery_date = $('#delivery_date_filter').val();
                }

                ,
                options: {
                    layout: {
                        topStart: 'search',
                        topEnd: 'buttons',
                        bottomStart: ['pageLength', 'info'],
                        bottomEnd: 'paging'
                    }

                    ,
                    buttons: [{

                        text: '<i class="fas fa-plus"></i> Add Receiving',
                        className: 'btn btn-primary',
                        action: function (e, dt, node, config) {
                            $('#addReceivingBtn').trigger('click');
                        }
                    }

                        ,
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> Export CSV',
                        className: 'btn btn-success'
                    }

                        ,
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
                }

                ,
                onEdit: function (data) {
                    $('#master_item_id').val(data.master_item_id);
                    $('#doc_number_internal').val(data.doc_number_internal);
                    $('#doc_number_customer').val(data.doc_number_customer);
                    $('#qrcode_customer').val(data.qrcode_customer);
                    $('#product_status').val(data.product_status);
                    $('#delivery_date_customer').val(data.delivery_date_customer ? data.delivery_date_customer.split('T')[0] : '');
                    $('#qty_pack').val(data.qty_pack);
                    $('#qty_per_pack').val(data.qty_per_pack);
                    $('#delivery_by').val(data.delivery_by);
                    $('#ng_customer').val(data.ng_customer);
                }

                ,
                onAdd: function () {
                    $('#master_item_id').val('');
                    $('#product_status').val('Waiting');
                    $('#ng_customer').val('OK');
                }
            });

            // Custom Reset Logic
            $('#resetBtn').on('click', function () {
                $('#incoming_date_filter').val('');
                $('#status_filter').val('');
                $('#ng_status_filter').val('');
                $('#delivery_date_filter').val('');
                crudManager.table.draw();
            });
        });
</script>@endpush