<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        $notificationId = $request->notification;

        $notification = Auth::user()->notifications()->where('id', $notificationId)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return redirect()->back();
    }
}
