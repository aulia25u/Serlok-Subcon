<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function getNotifications()
    {
        $user = Auth::user();

        // Fetch Unread Notifications (Limited for navbar)
        $notifs = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(10)
            ->get();

        $data = $notifs->map(function ($n) {
            $icon = 'fas fa-info-circle text-info';
            if ($n->type == 'warning')
                $icon = 'fas fa-exclamation-circle text-warning';
            if ($n->type == 'success')
                $icon = 'fas fa-check-circle text-success';
            if ($n->type == 'error')
                $icon = 'fas fa-times-circle text-danger';

            return [
                'id' => $n->id,
                'message' => $n->title,
                'time' => $n->created_at->diffForHumans(),
                'icon' => $icon,
                'details' => $n->message,
                'link' => $n->link
            ];
        });

        return response()->json([
            'count' => $notifs->count(),
            'logs' => $data
        ]);
    }

    public function markAsRead(Request $request)
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markOne(Request $request, $id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
