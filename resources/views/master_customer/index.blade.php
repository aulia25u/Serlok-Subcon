@extends('layouts.app')

@section('title', 'Master Customer')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
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

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Master Customer</h3>
                        <div class="card-tools d-none">
                            <button type="button" class="btn btn-primary btn-sm" id="addMasterCustomerBtn"
                                data-toggle="modal" data-target="#masterCustomerModal">
                                <i class="fas fa-plus"></i> Add Master Customer
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="master_customer_start_date">Start Date</label>
                                <input type="date" id="master_customer_start_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="master_customer_end_date">End Date</label>
                                <input type="date" id="master_customer_end_date" class="form-control">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="filterBtn" class="btn btn-primary mr-2">Filter</button>
                                <button id="resetBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped" id="masterCustomerTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tenant</th>
                                    <th>Customer Name</th>
                                    <th>Customer Code</th>
                                    <th>Address</th>
                                    <th>NPWP</th>
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
    <div class="modal fade" id="masterCustomerModal" tabindex="-1" role="dialog" aria-labelledby="masterCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="masterCustomerModalLabel">Add Master Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="masterCustomerForm">
                    @csrf
                    <input type="hidden" id="master_customer_id" name="id">
                    <input type="hidden" id="master_customer_form_method" name="_method" value="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="customer_id">Tenant</label>
                            @if($isInternal)
                                <select name="customer_id" id="customer_id" class="form-control" required>
                                    <option value="">Select Tenant</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select name="customer_id" id="customer_id" class="form-control" disabled>
                                    <option value="{{ $currentCustomerId ?? '' }}">
                                        {{ optional($customers->first())->customer_name ?? 'My Tenant' }}
                                    </option>
                                </select>
                                <input type="hidden" name="customer_id" value="{{ $currentCustomerId }}">
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="customer_name">Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_code">Customer Code</label>
                            <input type="text" name="customer_code" id="customer_code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="npwp">NPWP</label>
                            <input type="text" name="npwp" id="npwp" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                entity: 'master_customer',
                routes: {
                    index: "{{ route('rbac.master-customer') }}",
                    store: "{{ route('rbac.master-customer.store') }}",
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'tenant_name', name: 'tenant_name' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'customer_code', name: 'customer_code' },
                    { data: 'address', name: 'address' },
                    { data: 'npwp', name: 'npwp' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                modalId: '#masterCustomerModal',
                formId: '#masterCustomerForm',
                tableId: '#masterCustomerTable',
                filterBtnId: '#filterBtn',
                resetBtnId: '#resetBtn',
                addBtnId: '#addMasterCustomerBtn',
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
                            text: '<i class="fas fa-plus"></i> Add Master Customer',
                            className: 'btn btn-primary',
                            action: function (e, dt, node, config) {
                                $('#addMasterCustomerBtn').trigger('click');
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
                    $('#customer_name').val(data.customer_name);
                    $('#customer_code').val(data.customer_code);
                    $('#address').val(data.address);
                    $('#npwp').val(data.npwp);
                    if ($('#customer_id').length && !$('#customer_id').prop('disabled')) {
                        $('#customer_id').val(data.customer_id);
                    }
                },
                onAdd: function () {
                    if ($('#customer_id').length && !$('#customer_id').prop('disabled')) {
                        $('#customer_id').val('');
                    }
                }
            });
        });
    </script>
@endpush