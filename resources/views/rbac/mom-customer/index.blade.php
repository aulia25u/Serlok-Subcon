@extends('adminlte::page')

@section('title', 'MoM Customer')
@section('page-title', 'MoM Customer')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"/>

{{-- CKEditor 5 --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">MoM Customer Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>Start Date:</label>
                            <input type="date" class="form-control" id="start_date">
                        </div>
                        <div class="col-md-3">
                            <label>End Date:</label>
                            <input type="date" class="form-control" id="end_date">
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div>
                                <button class="btn btn-info" id="filterBtn"><i class="fas fa-filter"></i> Filter</button>
                                <button class="btn btn-secondary" id="resetBtn"><i class="fas fa-undo"></i> Reset</button>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="momCustomerTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Customer</th>
                                <th>Tanggal Rapat</th>
                                <th>Agenda</th>
                                <th>Rapat Selanjutnya</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="addForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah MoM Customer</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <label>Customer</label>
                            <select class="form-control" id="customer_id" name="customer_id" required>
                                <option value="">Pilih Customer</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Tanggal Rapat</label>
                            <input type="date" class="form-control" id="meeting_date" name="meeting_date" required>
                        </div>
                    </div>

                    <label>Peserta</label>
                    <textarea class="form-control" id="attendees" name="attendees" rows="2"></textarea>

                    <label>Agenda</label>
                    <textarea class="form-control" id="agenda" name="agenda" rows="3"></textarea>

                    <label>Notulen Rapat</label>
                    <textarea class="form-control" id="minutes" name="minutes" rows="5"></textarea>

                    <label>Tindak Lanjut</label>
                    <textarea class="form-control" id="action_items" name="action_items" rows="3"></textarea>

                    <label>Rapat Selanjutnya</label>
                    <input type="date" class="form-control" id="next_meeting_date" name="next_meeting_date">

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="editForm">
                @csrf
                <input type="hidden" id="edit_id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit MoM Customer</h5>
                    <button class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Customer</label>
                            <select class="form-control" id="edit_customer_id" name="customer_id" required>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Tanggal Rapat</label>
                            <input type="date" class="form-control" id="edit_meeting_date" name="meeting_date" required>
                        </div>
                    </div>

                    <label>Peserta</label>
                    <textarea class="form-control" id="edit_attendees" name="attendees" rows="2"></textarea>

                    <label>Agenda</label>
                    <textarea class="form-control" id="edit_agenda" name="agenda" rows="3"></textarea>

                    <label>Notulen Rapat</label>
                    <textarea class="form-control" id="edit_minutes" name="minutes" rows="5"></textarea>

                    <label>Tindak Lanjut</label>
                    <textarea class="form-control" id="edit_action_items" name="action_items" rows="3"></textarea>

                    <label>Rapat Selanjutnya</label>
                    <input type="date" class="form-control" id="edit_next_meeting_date" name="next_meeting_date">
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- VIEW MODAL --}}
<div class="modal fade" id="viewModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Detail MoM Customer</h5>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">
                <p><strong>Customer:</strong> <span id="view_customer_name"></span></p>
                <p><strong>Tgl Rapat:</strong> <span id="view_meeting_date"></span></p>
                <p><strong>Rapat Selanjutnya:</strong> <span id="view_next_meeting_date"></span></p>
                <p><strong>Peserta:</strong><br><span id="view_attendees"></span></p>
                <p><strong>Agenda:</strong><br><span id="view_agenda"></span></p>
                <p><strong>Notulen:</strong><br><span id="view_minutes"></span></p>
                <p><strong>Tindak Lanjut:</strong><br><span id="view_action_items"></span></p>
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
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

let addEditor;
let editEditor;

function initEditor(selector) {
    return ClassicEditor.create(document.querySelector(selector), {
        toolbar: [
            'undo','redo','|',
            'bold','italic','underline','|',
            'bulletedList','numberedList','|',
            'link','blockQuote'
        ]
    });
}

$(document).ready(function () {

    // ✅ INIT DATATABLE
    var table = $('#momCustomerTable').DataTable({
        processing:true, serverSide:true,
        ajax:{
            url:"{{ route('rbac.mom-customer.data') }}",
            data:d=>{
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns:[
            {data:'id'},
            {data:'customer_name'},
            {data:'meeting_date_formatted'},
            {data:'agenda'},
            {data:'next_meeting_date_formatted'},
            {data:'action', orderable:false, searchable:false}
        ],
        order:[[0,'desc']]
    });

    $('#filterBtn').click(()=> table.ajax.reload());
    $('#resetBtn').click(()=>{ $('#start_date,#end_date').val(''); table.ajax.reload(); });

    // ✅ ADD MODAL
    $('#addModal').on('shown.bs.modal', function () {
        if (!addEditor) {
            initEditor('#minutes').then(editor => addEditor = editor);
        }
    });

    $('#addForm').submit(function(e){
        e.preventDefault();

        let payload = $(this).serializeArray();
        payload.push({ name:'minutes', value:addEditor.getData() });

        $.post("{{ route('rbac.mom-customer.store') }}", payload)
        .done(res=>{
            $('#addModal').modal('hide');
            table.ajax.reload();
            toastr.success(res.success);
            addEditor.setData('');
            this.reset();
        })
        .fail(xhr=> $.each(xhr.responseJSON.errors,(k,v)=> toastr.error(v[0])));
    });

    $('#addModal').on('hidden.bs.modal', function(){
        if (addEditor) { addEditor.setData(''); }
    });

    // ✅ VIEW MODAL
    $(document).on('click','.view-btn', function(){
        let id = $(this).data('id');

        $.get("{{ route('rbac.mom-customer.show',':id') }}".replace(':id',id))
        .done(res=>{
            $('#view_customer_name').text(res.customer?.name ?? '-');
            $('#view_meeting_date').text(res.meeting_date ?? '-');
            $('#view_next_meeting_date').text(res.next_meeting_date ?? '-');
            $('#view_attendees').html(res.attendees ?? '-');
            $('#view_agenda').html(res.agenda ?? '-');
            $('#view_minutes').html(res.minutes ?? '-');
            $('#view_action_items').html(res.action_items ?? '-');

            $('#viewModal').modal('show');
        });
    });

    // ✅ EDIT MODAL
    $(document).on('click','.edit-btn',function(){
        let id = $(this).data('id');

        $.get("{{ route('rbac.mom-customer.edit',':id') }}".replace(':id',id))
        .done(res=>{
            $('#edit_id').val(res.id);
            $('#edit_customer_id').val(res.customer_id);
            $('#edit_meeting_date').val(res.meeting_date);
            $('#edit_attendees').val(res.attendees);
            $('#edit_agenda').val(res.agenda);
            $('#edit_action_items').val(res.action_items);
            $('#edit_next_meeting_date').val(res.next_meeting_date);

            $('#edit_minutes').val(res.minutes);

            $('#editModal').modal('show');

            setTimeout(() => {
                if (editEditor) editEditor.destroy();
                initEditor('#edit_minutes').then(editor => {
                    editEditor = editor;
                    editor.setData(res.minutes ?? '');
                });
            }, 150);
        });
    });

    $('#editForm').submit(function(e){
        e.preventDefault();

        let id = $('#edit_id').val();
        let payload = $(this).serializeArray();
        payload.push({ name:"minutes", value:editEditor.getData() });

        $.post("{{ route('rbac.mom-customer.update',':id') }}".replace(':id',id),
            payload.concat({name:"_method",value:"PUT"})
        )
        .done(res=>{
            $('#editModal').modal('hide');
            table.ajax.reload();
            toastr.success(res.success);
        })
        .fail(xhr=> $.each(xhr.responseJSON.errors,(k,v)=> toastr.error(v[0])));
    });

    $('#editModal').on('hidden.bs.modal', function(){
        if (editEditor) { editEditor.destroy(); editEditor = null; }
    });

    // ✅ DELETE
    $(document).on('click','.delete-btn',function(){
        if(!confirm("Yakin hapus data ini?")) return;

        $.post("{{ route('rbac.mom-customer.destroy',':id') }}".replace(':id',$(this).data('id')),
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
