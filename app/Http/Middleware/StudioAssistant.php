<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class StudioAssistant
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
        if ($user->role_id != 3  && $user->user_details->degination != 'STUDIO ASSISTANT') {
            Auth::logout();
            return redirect(route('login'));
        }
        return $next($request);
    }
}
