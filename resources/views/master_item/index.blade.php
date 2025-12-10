@extends('layouts.app')

@section('title', 'Master Item')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Master Item Management</h3>
                        <div class="card-tools d-none">
                            <button type="button" class="btn btn-primary btn-sm" id="addMasterItemBtn" data-toggle="modal"
                                data-target="#masterItemModal">
                                <i class="fas fa-plus"></i> Add Master Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="master_item_start_date">Start Date</label>
                                <input type="date" id="master_item_start_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="master_item_end_date">End Date</label>
                                <input type="date" id="master_item_end_date" class="form-control">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="filterBtn" class="btn btn-primary mr-2">Filter</button>
                                <button id="resetBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped" id="masterItemTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tenant</th>
                                    <th>Customer Name</th>
                                    <th>Product Status</th>
                                    <th>Code Product</th>
                                    <th>Part Number</th>
                                    <th>Part Name</th>
                                    <th>Model</th>
                                    <th>Unit</th>
                                    <th>Note</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
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
    <div class="modal fade" id="masterItemModal" tabindex="-1" role="dialog" aria-labelledby="masterItemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="masterItemModalLabel">Add Master Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="masterItemForm">
                    @csrf
                    <input type="hidden" id="master_item_id" name="id">
                    <input type="hidden" id="master_item_form_method" name="_method" value="POST">
                    <div class="modal-body">
                        @if(empty(auth()->user()->userDetail->customer_id))
                            <div class="form-group" id="tenant_id_group">
                                <label for="tenant_id">Tenant Owner</label>
                                <select name="tenant_id" id="tenant_id" class="form-control">
                                    <option value="">Select Tenant Owner</option>
                                    @foreach($tenantOwners as $tenantOwner)
                                        <option value="{{ $tenantOwner->id }}">{{ $tenantOwner->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="master_customer_id">Customer Name</label>
                            <select name="master_customer_id" id="master_customer_id" class="form-control">
                                <option value="">Select Customer</option>
                                @foreach($masterCustomers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="product_status">Product Status</label>
                            <select name="product_status" id="product_status" class="form-control">
                                <option value="">Select Status</option>
                                <option value="Continue">Continue</option>
                                <option value="Not Continue">Not Continue</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="item_code">Code Product</label>
                            <input type="text" name="item_code" id="item_code" class="form-control"
                                placeholder="Enter Code Product" required>
                        </div>
                        <div class="form-group">
                            <label for="part_number">Part Number</label>
                            <input type="text" name="part_number" id="part_number" class="form-control"
                                placeholder="Enter Part Number">
                        </div>
                        <div class="form-group">
                            <label for="item_name">Part Name</label>
                            <input type="text" name="item_name" id="item_name" class="form-control"
                                placeholder="Enter Part Name" required>
                        </div>
                        <div class="form-group">
                            <label for="model">Model</label>
                            <input type="text" name="model" id="model" class="form-control" placeholder="Enter Model">
                        </div>
                        <div class="form-group">
                            <label for="unit">Unit</label>
                            <select name="unit" id="unit" class="form-control">
                                <option value="">Select Unit</option>
                                <option value="PCS">PCS</option>
                                <option value="KG">KG</option>
                                <option value="ROLL">ROLL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Note</label>
                            <textarea name="description" id="description" rows="3" class="form-control"
                                placeholder="Enter Note"></textarea>
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
        /* Force side-by-side display for bottom start section */
        .dt-layout-cell.dt-start {
            display: flex !important;
            align-items: center;
            gap: 10px;
        }

        .dt-layout-cell.dt-start select {
            width: auto !important;
            min-width: 60px;
            padding-right: 30px !important;
            /* Ensure space for arrow */
        }

        /* Custom Search Input Style */
        .dt-search {
            position: relative;
        }

        .dt-search input {
            padding-left: 30px !important;
            /* Space for the icon */
            border-radius: 3px !important;
            /* Rounded corners */
        }

        .dt-search::before {
            content: "\f002";
            /* FontAwesome magnifying glass */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            pointer-events: none;
            /* Let clicks pass through */
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
    <script src="{{ asset('js/crud-manager.js') }}"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const crudManager = new CrudManager({
                entity: 'master_item',
                routes: {
                    index: "{{ route('rbac.master-item') }}",
                    store: "{{ route('rbac.master-item.store') }}",
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'tenant_name', name: 'tenant_name' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'product_status', name: 'product_status' },
                    { data: 'item_code', name: 'item_code' },
                    { data: 'part_number', name: 'part_number' },
                    { data: 'item_name', name: 'item_name' },
                    { data: 'model', name: 'model' },
                    { data: 'unit', name: 'unit' },
                    { data: 'description', name: 'description' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                modalId: '#masterItemModal',
                formId: '#masterItemForm',
                tableId: '#masterItemTable',
                filterBtnId: '#filterBtn',
                resetBtnId: '#resetBtn',
                addBtnId: '#addMasterItemBtn',
                dateFilters: true,
                options: {
                    layout: {
                        topStart: 'search',
                        topEnd: 'buttons',
                        bottomStart: ['pageLength', 'info'],
                        bottomEnd: 'paging'
                    },
                    buttons: [
                        {
                            text: '<i class="fas fa-plus"></i> Add Master Item',
                            className: 'btn btn-primary',
                            action: function (e, dt, node, config) {
                                $('#addMasterItemBtn').trigger('click');
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
                onEdit: function (data) {
                    $('#item_name').val(data.item_name);
                    $('#item_code').val(data.item_code);
                    $('#description').val(data.description);
                    $('#master_customer_id').val(data.master_customer_id);
                    $('#product_status').val(data.product_status);
                    $('#part_number').val(data.part_number);
                    $('#model').val(data.model);
                    $('#unit').val(data.unit);
                    if ($('#tenant_id').length) {
                        $('#tenant_id').val(data.tenant_id);
                    }
                },
                onAdd: function () {
                    // Reset logic handled by CrudManager, but specific fields can be cleared here if needed
                    if ($('#tenant_id').length) {
                        $('#tenant_id').val('');
                    }
                    $('#master_customer_id').val('');
                    $('#product_status').val('');
                    $('#unit').val('');
                }
            });
        });
    </script>
@endpush