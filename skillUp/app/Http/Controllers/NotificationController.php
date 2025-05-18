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

        $dashboardHtml = '';

        foreach (auth()->user()->notifications()->latest()->take(4)->get() as $notification) {
            $dashboardHtml .= '
            <div class="flex items-center space-x-4 leading-card mb-2.5 hover:bg-themeLightGray/20 cursor-pointer p-1 rounded-lg transition">
                <div class="bg-themeGrape text-white p-2 rounded-full">
                    <svg class="w-8 h-auto" fill="none" stroke="currentColor"><use href="#icon-bell"></use></svg>
                </div>
                <div class="[&>p]:mt-1">
                    <p class="font-semibold">' . e($notification->message) . '</p>
                    <p class="text-xs text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">'
                . $notification->created_at->diffForHumans() .
                '</p>
                </div>
            </div>';
        }

        if ($notificationCount === 0) {
            $dashboardHtml = '<p class="mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">'
                . __('messages.dashboard.no-notifications') . '</p>';
        }

        return response()->json([
            'ok' => true,
            'notificationCount' => $notificationCount,
            'dashboardHtml' => $dashboardHtml
        ]);
    }


    public function check()
    {
        $notificaciones = Notification::where('user_id', Auth::id())->latest()->get();


        return response()->json($notificaciones);
    }

}
