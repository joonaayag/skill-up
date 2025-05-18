<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        $notificationCount = auth()->user()->notifications()->count();

        if (request()->ajax()) {
            return response()->json(['ok' => true, 'notificationCount' => $notificationCount]);
        }

        return back()->with('notificationCount', $notificationCount);
    }
    public function check()
    {
        $notificaciones = Notification::where('user_id', Auth::id())->latest()->get();


        return response()->json($notificaciones);
    }

}
