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
        return Inertia::render('Notification');
    }

    public function notificationPagination(Request $request)
    {
        $paginatedNotifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->selectRaw("*, DATE(created_at) as date, TIME_FORMAT(created_at, '%H:%i') as time")
            ->paginate(10);

        $modifiedCollection = $paginatedNotifications->getCollection()->map(function ($notification) {
            $time = Carbon::createFromFormat('H:i', $notification->time, 'UTC')->setTimezone('Asia/Makassar')->format('H:i');
            $notification->time = $time;
            return $notification;
        });
        $paginatedNotifications = new LengthAwarePaginator($modifiedCollection, $paginatedNotifications->total(), $paginatedNotifications->perPage());
        return json_encode($paginatedNotifications);
    }
}
