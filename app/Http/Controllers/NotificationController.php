<?php

namespace App\Http\Controllers;

use App\Models\ErpNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = ErpNotification::forUser(auth()->id())
            ->latest()
            ->paginate(20);
        return view('erp.employee.notifications', compact('notifications'));
    }

    public function markAllRead()
    {
        NotificationService::markAllRead(auth()->id());
        return back()->with('success', 'All notifications marked as read.');
    }

    public function markRead(int $id)
    {
        $notification = ErpNotification::where('user_id', auth()->id())->findOrFail($id);
        $notification->markAsRead();
        return back();
    }
}
