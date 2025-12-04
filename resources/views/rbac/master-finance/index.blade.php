@extends('layouts.app')

@section('title', 'Master Finance')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Master Finance Management</h3>
                        <div class="card-tools d-none">
                            <button type="button" class="btn btn-primary btn-sm" id="addMasterFinanceBtn"
                                data-toggle="modal" data-target="#masterFinanceModal">
                                <i class="fas fa-plus"></i> Add Master Finance
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="master_finance_start_date">Start Date</label>
                                <input type="date" id="master_finance_start_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="master_finance_end_date">End Date</label>
                                <input type="date" id="master_finance_end_date" class="form-control">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="filterBtn" class="btn btn-primary mr-2">Filter</button>
                                <button id="resetBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped" id="masterFinanceTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tenant</th>
                                    <th>Bank Name</th>
                                    <th>Bank Account Name</th>
                                    <th>Bank Account Number</th>
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
    <div class="modal fade" id="masterFinanceModal" tabindex="-1" role="dialog" aria-labelledby="masterFinanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="masterFinanceModalLabel">Add Master Finance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="masterFinanceForm">
                    @csrf
                    <input type="hidden" id="master_finance_id" name="id">
                    <input type="hidden" id="master_finance_form_method" name="_method" value="POST">
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
                            <label for="bank_name">Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control"
                                placeholder="Enter Bank Name" required>
                        </div>
                        <div class="form-group">
                            <label for="bank_account_name">Bank Account Name</label>
                            <input type="text" name="bank_account_name" id="bank_account_name" class="form-control"
                                placeholder="Enter Bank Account Name" required>
                        </div>
                        <div class="form-group">
                            <label for="bank_account_number">Bank Account Number</label>
                            <input type="text" name="bank_account_number" id="bank_account_number" class="form-control"
                                placeholder="Enter Bank Account Number" required>
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
                entity: 'master_finance',
                routes: {
                    index: "{{ route('rbac.master-finance') }}",
                    store: "{{ route('rbac.master-finance.store') }}",
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'tenant_name', name: 'tenant_name' },
                    { data: 'bank_name', name: 'bank_name' },
                    { data: 'bank_account_name', name: 'bank_account_name' },
                    { data: 'bank_account_number', name: 'bank_account_number' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                modalId: '#masterFinanceModal',
                formId: '#masterFinanceForm',
                tableId: '#masterFinanceTable',
                filterBtnId: '#filterBtn',
                resetBtnId: '#resetBtn',
                addBtnId: '#addMasterFinanceBtn',
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
                            text: '<i class="fas fa-plus"></i> Add Master Finance',
                            className: 'btn btn-primary',
                            action: function (e, dt, node, config) {
                                $('#addMasterFinanceBtn').trigger('click');
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
                    $('#bank_name').val(data.bank_name);
                    $('#bank_account_name').val(data.bank_account_name);
                    $('#bank_account_number').val(data.bank_account_number);
                    if ($('#tenant_id').length) {
                        $('#tenant_id').val(data.tenant_id);
                    }
                },
                onAdd: function () {
                    if ($('#tenant_id').length) {
                        $('#tenant_id').val('');
                    }
                }
            });
        });
    </script>
@endpush