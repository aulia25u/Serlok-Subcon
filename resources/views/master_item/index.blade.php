@extends('layouts.app')

@section('title', 'Master Item')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Master Item Management</h3>
                        <div class="card-tools">
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
                                    <th>No</th>
                                    <th>Tenant</th>
                                    <th>Item Name</th>
                                    <th>Item Code</th>
                                    <th>Description</th>
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
                            <label for="item_name">Item Name</label>
                            <input type="text" name="item_name" id="item_name" class="form-control"
                                placeholder="Enter Item Name" required>
                        </div>
                        <div class="form-group">
                            <label for="item_code">Item Code</label>
                            <input type="text" name="item_code" id="item_code" class="form-control"
                                placeholder="Enter Item Code" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control"
                                placeholder="Enter description"></textarea>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@push('scripts')
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
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'tenant_name', name: 'tenant_name' },
                    { data: 'item_name', name: 'item_name' },
                    { data: 'item_code', name: 'item_code' },
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
                onEdit: function (data) {
                    $('#item_name').val(data.item_name);
                    $('#item_code').val(data.item_code);
                    $('#description').val(data.description);
                    if ($('#tenant_id').length) {
                        $('#tenant_id').val(data.tenant_id);
                    }
                },
                onAdd: function () {
                    // Reset logic handled by CrudManager, but specific fields can be cleared here if needed
                    if ($('#tenant_id').length) {
                        $('#tenant_id').val('');
                    }
                }
            });
        });
    </script>
@endpush