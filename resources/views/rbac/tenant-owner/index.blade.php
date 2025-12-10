@extends('layouts.app')

@section('title', 'Tenant Users')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
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
                        <h3 class="card-title">Tenant Users (Owners)</h3>
                        <div class="card-tools d-none">
                            <button type="button" class="btn btn-primary" id="addBtn">
                                <i class="fas fa-plus"></i> Assign Owner
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="owner_start_date">Start Date:</label>
                                <input type="date" class="form-control" id="owner_start_date" name="start_date">
                            </div>
                            <div class="col-md-3">
                                <label for="owner_end_date">End Date:</label>
                                <input type="date" class="form-control" id="owner_end_date" name="end_date">
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-info" id="filterBtn">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="resetBtn">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped" id="ownerTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Owner</th>
                                    <th>Email</th>
                                    <th>Tenant</th>
                                    <th>Status</th>
                                    <th>Assigned At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ownerModal" tabindex="-1" role="dialog" aria-labelledby="ownerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ownerModalLabel">Assign Tenant Owner</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="ownerForm">
                    @csrf
                    <input type="hidden" name="_method" id="owner_form_method" value="POST">
                    <input type="hidden" name="id" id="owner_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="owner_user_id">Owner User</label>
                            <select class="form-control select2" id="owner_user_id" name="user_id" style="width: 100%;"
                                required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }} ({{ $user->email ?? 'no email' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="owner_customer_id">Tenant</label>
                            <select class="form-control select2" id="owner_customer_id" name="customer_id"
                                style="width: 100%;" required>
                                <option value="">Select Tenant</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="owner_active" checked>
                            <label class="form-check-label" for="owner_active">Active</label>
                        </div>
                        <input type="hidden" name="is_active" id="owner_active_value" value="1">
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('js/crud-manager.js') }}"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            new CrudManager({
                entity: 'owner',
                routes: {
                    index: "{{ route('rbac.tenant-owner') }}",
                    store: "{{ route('rbac.tenant-owner.store') }}"
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'owner_name', name: 'owner_name' },
                    { data: 'owner_email', name: 'owner_email' },
                    { data: 'tenant', name: 'tenant' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                modalId: '#ownerModal',
                formId: '#ownerForm',
                tableId: '#ownerTable',
                filterBtnId: '#filterBtn',
                resetBtnId: '#resetBtn',
                addBtnId: '#addBtn',
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
                            text: '<i class="fas fa-plus"></i> Assign Owner',
                            className: 'btn btn-primary',
                            action: function (e, dt, node, config) {
                                $('#addBtn').trigger('click');
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
                onEdit: function (response) {
                    $('#owner_user_id').val(response.user_id);
                    $('#owner_customer_id').val(response.customer_id);
                    $('#owner_active').prop('checked', response.is_active);
                    $('#owner_active_value').val(response.is_active ? 1 : 0);
                },
                onAdd: function () {
                    $('#owner_active').prop('checked', true);
                    $('#owner_active_value').val(1);
                },
                onModalHidden: function () {
                    $('#owner_active').prop('checked', true);
                    $('#owner_active_value').val(1);
                }
            });

            // Custom logic for is_active toggle which is specific to this form
            $('#owner_active').on('change', function () {
                $('#owner_active_value').val($(this).is(':checked') ? 1 : 0);
            });

            // Fallback: Initialize Select2 when modal is shown (covers cases missed by CrudManager)
            $('#ownerModal').on('shown.bs.modal', function () {
                $(this).find('select.select2').each(function () {
                    // Check if already initialized to avoid double init
                    if (!$(this).hasClass("select2-hidden-accessible")) {
                        $(this).select2({
                            theme: 'bootstrap4',
                            dropdownParent: $('#ownerModal'),
                            width: '100%'
                        });
                    }
                });
            });
        });
    </script>
@endpush