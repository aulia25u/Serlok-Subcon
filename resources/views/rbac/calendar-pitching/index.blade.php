@extends('adminlte::page')

@section('title', 'Calendar Pitching')
@section('page-title', 'Calendar Pitching')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"/>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Calendar Pitching Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="start_date">Start Date:</label>
                            <input type="date" class="form-control" id="start_date">
                        </div>
                        <div class="col-md-2">
                            <label for="end_date">End Date:</label>
                            <input type="date" class="form-control" id="end_date">
                        </div>
                        <div class="col-md-2">
                            <label for="status_filter">Status:</label>
                            <select class="form-control" id="status_filter">
                                <option value="">All Status</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div>
                                <button class="btn btn-info" id="filterBtn"><i class="fas fa-filter"></i> Filter</button>
                                <button class="btn btn-secondary" id="resetBtn"><i class="fas fa-undo"></i> Reset</button>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="calendarPitchingTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Customer</th>
                                <th>Judul</th>
                                <th>Lokasi</th>
                                <th>Tanggal & Waktu</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Calendar Pitching</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Customer</label>
                                <select class="form-control" name="customer_id" required>
                                    <option value="">Pilih Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" name="location" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tanggal & Waktu</label>
                        <input type="datetime-local" name="scheduled_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" name="description"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea class="form-control" name="notes"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editForm">
                @csrf
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Calendar Pitching</h5>
                    <button class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    
                    {{-- same fields as add --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label>Customer</label>
                            <select class="form-control" id="edit_customer_id" name="customer_id" required>
                                <option value="">Pilih Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <label>Judul</label>
                    <input type="text" id="edit_title" class="form-control" name="title" required>

                    <label>Lokasi</label>
                    <input type="text" id="edit_location" class="form-control" name="location">

                    <label>Tanggal & Waktu</label>
                    <input type="datetime-local" id="edit_scheduled_date" class="form-control" name="scheduled_date" required>

                    <label>Deskripsi</label>
                    <textarea id="edit_description" class="form-control" name="description"></textarea>

                    <label>Catatan</label>
                    <textarea id="edit_notes" class="form-control" name="notes"></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- View Modal --}}
<div class="modal fade" id="viewModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Detail Calendar Pitching</h5>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">
                <p><strong>Customer:</strong> <span id="view_customer_name"></span></p>
                <p><strong>Status:</strong> <span id="view_status_badge"></span></p>
                <p><strong>Judul:</strong> <span id="view_title"></span></p>
                <p><strong>Lokasi:</strong> <span id="view_location"></span></p>
                <p><strong>Tanggal & Waktu:</strong> <span id="view_scheduled_date"></span></p>
                <p><strong>Deskripsi:</strong></p>
                <p id="view_description"></p>
                <p><strong>Catatan:</strong></p>
                <p id="view_notes"></p>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>

<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function () {

    var table = $('#calendarPitchingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('rbac.calendar-pitching.data') }}",
            data: d => {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.status = $('#status_filter').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data:'customer_name'}, {data:'title'}, {data:'location'},
            {data:'scheduled_date_formatted'}, {data:'status_badge'},
            {data:'action', orderable:false, searchable:false}
        ]
    });

    $('#filterBtn').click(()=> table.ajax.reload());
    $('#resetBtn').click(()=>{
        $('#start_date, #end_date, #status_filter').val('');
        table.ajax.reload();
    });

    // CREATE
    $('#addForm').submit(function(e){
        e.preventDefault();
        $.post("{{ route('rbac.calendar-pitching.store') }}", $(this).serialize())
        .done(res=>{
            $('#addModal').modal('hide');
            table.ajax.reload();
            toastr.success(res.success);
            this.reset();
        })
        .fail(xhr=>{
            $.each(xhr.responseJSON.errors, (_,v)=> toastr.error(v[0]));
        });
    });

    // VIEW
    $(document).on('click', '.view-btn', function(){
        $.get("{{ route('rbac.calendar-pitching.show', ':id') }}".replace(':id', $(this).data('id')))
        .done(res=>{
            $('#view_customer_name').text(res.customer?.name ?? '-');
            $('#view_title').text(res.title);
            $('#view_location').text(res.location);
            $('#view_scheduled_date').text(res.scheduled_date);
            $('#view_description').html(res.description ?? '-');
            $('#view_notes').html(res.notes ?? '-');
            let badge = {
                scheduled:'warning', completed:'success', cancelled:'danger'
            }[res.status];
            $('#view_status_badge').html(`<span class="badge badge-${badge}">${res.status}</span>`);
            $('#viewModal').modal('show');
        });
    });

    // EDIT LOAD
    $(document).on('click', '.edit-btn', function(){
        $.get("{{ route('rbac.calendar-pitching.edit', ':id') }}".replace(':id', $(this).data('id')))
        .done(res=>{
            $('#edit_id').val(res.id);
            $('#edit_customer_id').val(res.customer_id);
            $('#edit_title').val(res.title);
            $('#edit_location').val(res.location);
            $('#edit_scheduled_date').val(res.scheduled_date?.substring(0,16));
            $('#edit_status').val(res.status);
            $('#edit_description').val(res.description);
            $('#edit_notes').val(res.notes);
            $('#editModal').modal('show');
        });
    });

    // EDIT SUBMIT
    $('#editForm').submit(function(e){
        e.preventDefault();
        let id = $('#edit_id').val();
        $.post("{{ route('rbac.calendar-pitching.update', ':id') }}".replace(':id', id), 
            $(this).serialize()+"&_method=PUT"
        ).done(res=>{
            $('#editModal').modal('hide');
            table.ajax.reload();
            toastr.success(res.success);
        })
        .fail(xhr=> $.each(xhr.responseJSON.errors, (_,v)=> toastr.error(v[0])));
    });

    // DELETE
    $(document).on('click', '.delete-btn', function(){
        if(!confirm("Yakin hapus data ini?")) return;
        $.post("{{ route('rbac.calendar-pitching.destroy', ':id') }}".replace(':id', $(this).data('id')), 
            {_method:'DELETE'}
        )
        .done(res=>{
            table.ajax.reload();
            toastr.success(res.success);
        })
        .fail(()=> toastr.error("Gagal menghapus"));
    });
});
</script>
@stop
