<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        $notificationId = $request->notification;
        if (!$notificationId) {
            Auth::user()->unreadNotifications->markAsRead();
        } else {
            $notification = Auth::user()->notifications()->where('id', $notificationId)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        }

        return redirect()->back();
    }

    public function notificationPage(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();
        return Inertia::render('Notification');
    }

    public function notificationPagination(Request $request)
    {
        $paginatedNotifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return json_encode($paginatedNotifications);
    }
}
