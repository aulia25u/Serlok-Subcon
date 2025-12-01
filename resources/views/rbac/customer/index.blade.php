@extends('layouts.app')

@section('title', 'Customer Management')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                        <h3 class="card-title">Customer Management</h3>
                        <div class="card-tools d-none">
                            <button type="button" class="btn btn-primary" id="addBtn" data-toggle="modal"
                                data-target="#customerModal">
                                <i class="fas fa-plus"></i> Add New
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="customer_start_date">Start Date:</label>
                                <input type="date" class="form-control" id="customer_start_date" name="start_date">
                            </div>
                            <div class="col-md-3">
                                <label for="customer_end_date">End Date:</label>
                                <input type="date" class="form-control" id="customer_end_date" name="end_date">
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

                        <table class="table table-bordered table-striped" id="customerTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Add New Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="customerForm">
                    @csrf
                    <input type="hidden" name="_method" id="customer_form_method" value="POST">
                    <input type="hidden" name="id" id="customer_id">
                    <input type="hidden" name="is_active" id="is_active_value" value="1">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="customer_name">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_code">Customer Code</label>
                            <input type="text" class="form-control" id="customer_code" name="customer_code" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_person">Contact Person</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active_toggle" checked>
                            <label class="form-check-label" for="is_active_toggle">Active</label>
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

    <script src="{{ asset('js/crud-manager.js') }}"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            new CrudManager({
                entity: 'customer',
                routes: {
                    index: "{{ route('rbac.customer') }}",
                    store: "{{ route('rbac.customer.store') }}"
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'customer_code', name: 'customer_code' },
                    { data: 'contact_person', name: 'contact_person' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'is_active_label', name: 'is_active', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                modalId: '#customerModal',
                formId: '#customerForm',
                tableId: '#customerTable',
                filterBtnId: '#filterBtn',
                resetBtnId: '#resetBtn',
                addBtnId: '#addBtn', // Using selector for the existing add button
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
                            text: '<i class="fas fa-plus"></i> Add New',
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
                    $('#customer_name').val(response.customer_name);
                    $('#customer_code').val(response.customer_code);
                    $('#contact_person').val(response.contact_person);
                    $('#email').val(response.email);
                    $('#phone').val(response.phone);
                    $('#address').val(response.address);
                    $('#is_active_toggle').prop('checked', response.is_active);
                    $('#is_active_value').val(response.is_active ? 1 : 0);
                },
                onAdd: function () {
                    $('#is_active_toggle').prop('checked', true);
                    $('#is_active_value').val(1);
                },
                onModalHidden: function () {
                    $('#is_active_toggle').prop('checked', true);
                    $('#is_active_value').val(1);
                }
            });

            // Custom logic for is_active toggle which is specific to this form
            $('#is_active_toggle').on('change', function () {
                $('#is_active_value').val($(this).is(':checked') ? 1 : 0);
            });
        });
    </script>
@endpush