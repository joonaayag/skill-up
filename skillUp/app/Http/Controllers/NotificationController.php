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

        $dashboardHtml = '<h3 class="text-2xl font-semibold mb-8">'
        . __('messages.dashboard.recent-notifications') .'</h3>';

        foreach (auth()->user()->notifications()->latest()->take(4)->get() as $notification) {
            $dashboardHtml .= '
            <div
            class="flex items-center space-x-4 leading-card mb-2.5 hover:bg-themeLightGray/20 cursor-pointer p-1 rounded-lg transition">
            <div class="bg-themeGrape text-white p-2 rounded-full">
                <svg  xmlns="http://www.w3.org/2000/svg"  width="32"  height="auto"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-bell"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
            </div>
            <div class="[&>p]:mt-1">
                <p class="font-semibold">' . $notification->message . '</p>
                <p class="text-xs text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                    ' . $notification->created_at->diffForHumans() . '
                </p>
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
