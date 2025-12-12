@extends('adminlte::page')

@section('title', 'Notifications')

@section('content_header')
<h1>All Notifications</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Your Notification History</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" id="markAllReadBtn" title="Mark All as Read">
                        <i class="fas fa-check-double"></i> Mark All Read
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="notificationsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notification)
                            <tr id="row-{{ $notification->id }}">
                                <td class="status-cell">
                                    @if(!$notification->is_read)
                                        <span class="badge badge-primary">New</span>
                                    @else
                                        <span class="badge badge-secondary">Read</span>
                                    @endif
                                </td>
                                <td>{{ $notification->title }}</td>
                                <td>{!! $notification->message !!}</td>
                                <td>{{ $notification->created_at->format('d M Y H:i') }}</td>
                                <td class="action-cell">
                                    @if(!$notification->is_read)
                                        <button class="btn btn-sm btn-success mark-read-btn" data-id="{{ $notification->id }}">
                                            <i class="fas fa-check"></i> Read
                                        </button>
                                    @elseif($notification->link)
                                        <a href="{{ $notification->link }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                {{ $notifications->links('pagination::bootstrap-4') }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css">
@stop

@push('js')
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#notificationsTable').DataTable({
                "paging": false, // Handled by Laravel
                "lengthChange": false,
                "searching": false, // Simple view
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": true,
            });

            // Mark One Read
            $('.mark-read-btn').click(function () {
                var btn = $(this);
                var id = btn.data('id');
                var row = $('#row-' + id);

                $.ajax({
                    url: '/notifications/' + id + '/read',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function (res) {
                        // Update UI
                        row.find('.status-cell').html('<span class="badge badge-secondary">Read</span>');
                        // Replace button with View button (if link exists, tricky without knowing link in JS, 
                        // but we can maybe just reload or assuming we have link.
                        // Ideally we reload or just hide the button.
                        // Let's just reload to be safe and simple or just hide.
                        // User prompt imply "table ganti dengan read", but if read, maybe they want to view now?
                        // Let's reload page to refresh state correctly including Navbar count.
                        location.reload();
                    }
                });
            });

            // Mark All Read
            $('#markAllReadBtn').click(function () {
                if (confirm('Mark all notifications as read?')) {
                    $.ajax({
                        url: "{{ route('notifications.mark-read') }}",
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        success: function (res) {
                            location.reload();
                        }
                    });
                }
            });
        });
    </script>
@endpush