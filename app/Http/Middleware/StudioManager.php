<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class StudioManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user->role_id != 4 && $user->role_id != 27) {
            Auth::logout();
            return redirect(route('login'));
        }
        return $next($request);
    }
}
