<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class SessionTimeout
{
    protected $timeout = 300;

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = Session::get('lastActivityTime');

            if ($lastActivity && (time() - $lastActivity > $this->timeout)) {
                Auth::logout();
                Session::flush();

                return redirect()->route('login')->withErrors([
                    'timeout' => 'Tu sesión ha expirado por inactividad.'
                ]);
            }

            Session::put('lastActivityTime', time());
        }

        return $next($request);
    }
}
