<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userRoles = $user->getRoleNames()->toArray();
        if (empty($userRoles)) {
            $userRoles = ['admin'];
        }

        $notifications = Notification::where(function($q) use ($user, $userRoles) {
            $q->where('user_id', $user->id)
              ->orWhereIn('role_target', $userRoles)
              ->orWhere('role_target', 'all');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function read($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return redirect($notification->url ?? route('admin.pesanan.index'));
    }

    public function markAllRead()
    {
        $user = auth()->user();
        $userRoles = $user->getRoleNames()->toArray();
        if (empty($userRoles)) {
            $userRoles = ['admin'];
        }

        Notification::where(function($q) use ($user, $userRoles) {
            $q->where('user_id', $user->id)
              ->orWhereIn('role_target', $userRoles)
              ->orWhere('role_target', 'all');
        })
        ->where('is_read', false)
        ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    public function checkNotifications(Request $request)
    {
        $user = auth()->user();
        $userRoles = $user ? $user->getRoleNames()->toArray() : ['admin'];
        if (empty($userRoles)) {
            $userRoles = ['admin'];
        }

        $unreadQuery = Notification::where(function($q) use ($user, $userRoles) {
            if ($user) {
                $q->where('user_id', $user->id);
            }
            $q->orWhereIn('role_target', $userRoles)
              ->orWhere('role_target', 'all');
        })->where('is_read', false);

        $unreadCount = $unreadQuery->count();
        $lastId = (int) $request->query('last_id', 0);

        if ($lastId === 0) {
            $latest = (clone $unreadQuery)->orderBy('id', 'desc')->first();
            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
                'has_new' => false,
                'latest_id' => $latest ? $latest->id : 0,
                'latest_notif' => null
            ]);
        }

        $newNotifications = (clone $unreadQuery)
            ->where('id', '>', $lastId)
            ->orderBy('id', 'desc')
            ->get();

        $latestNotif = $newNotifications->first();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'has_new' => $newNotifications->count() > 0,
            'latest_id' => $latestNotif ? $latestNotif->id : $lastId,
            'latest_notif' => $latestNotif ? [
                'id' => $latestNotif->id,
                'title' => $latestNotif->title,
                'message' => $latestNotif->message,
                'url' => $latestNotif->url,
                'created_at_human' => $latestNotif->created_at ? $latestNotif->created_at->diffForHumans() : 'Baru saja',
            ] : null,
        ]);
    }
}
