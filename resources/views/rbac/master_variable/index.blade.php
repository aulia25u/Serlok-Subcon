@extends('layouts.app')

@section('title', 'Master Variable')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Master Variable Management</h3>
                        <div class="card-tools d-none">
                            <button type="button" class="btn btn-primary btn-sm" id="addMasterVariableBtn"
                                data-toggle="modal" data-target="#variableModal">
                                <i class="fas fa-plus"></i> Add Variable
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="master_variable_start_date">Start Date</label>
                                <input type="date" id="master_variable_start_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="master_variable_end_date">End Date</label>
                                <input type="date" id="master_variable_end_date" class="form-control">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="filterBtn" class="btn btn-primary mr-2">Filter</button>
                                <button id="resetBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped" id="variableTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Value</th>
                                    <th>Description</th>
                                    <th>Last Updated</th>
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
    <div class="modal fade" id="variableModal" tabindex="-1" role="dialog" aria-labelledby="variableModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="variableModalLabel">Add Master Variable</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="variableForm">
                    @csrf
                    <input type="hidden" id="master-variable_id" name="id">
                    <input type="hidden" id="master-variable_form_method" name="_method" value="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="variable_code">Code</label>
                            <input type="text" name="variable_code" id="variable_code" class="form-control" required
                                placeholder="e.g., DATE_FORMAT">
                        </div>
                        <div class="form-group">
                            <label for="variable_name">Name</label>
                            <input type="text" name="variable_name" id="variable_name" class="form-control" required
                                placeholder="e.g., Date Format">
                        </div>
                        <div class="form-group">
                            <label for="variable_value">Value</label>
                            <input type="text" name="variable_value" id="variable_value" class="form-control" required
                                placeholder="e.g., YYYY-MM-DD">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
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
                entity: 'master_variable',
                routes: {
                    index: "{{ route('rbac.master-variable.index') }}",
                    store: "{{ route('rbac.master-variable.store') }}",
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'variable_code', name: 'variable_code' },
                    { data: 'variable_name', name: 'variable_name' },
                    { data: 'variable_value', name: 'variable_value' },
                    { data: 'description', name: 'description' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                modalId: '#variableModal',
                formId: '#variableForm',
                tableId: '#variableTable',
                addBtnId: '#addMasterVariableBtn',
                filterBtnId: '#filterBtn',
                resetBtnId: '#resetBtn',
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
                            text: '<i class="fas fa-plus"></i> Add Variable',
                            className: 'btn btn-primary',
                            action: function (e, dt, node, config) {
                                $('#addMasterVariableBtn').trigger('click');
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
                    $('#variable_code').val('');
                    $('#variable_name').val('');
                    $('#variable_value').val('');
                    $('#description').val('');
                },
                onEdit: function (response) {
                    $('#variable_code').val(response.variable_code);
                    $('#variable_name').val(response.variable_name);
                    $('#variable_value').val(response.variable_value);
                    $('#description').val(response.description);
                }
            });
        });
    </script>
@endpush