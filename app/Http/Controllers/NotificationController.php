<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function index()
    {
        return view('notifications.index');
    }


    public function markAllNotificationsAsRead()
    {
        $user = Auth::user();

        // Marquer toutes les notifications non lues comme lues
        $user->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues.']);
    }
}
