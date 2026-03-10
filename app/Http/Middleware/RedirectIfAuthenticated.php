<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if (Auth::user()->role_id == 1) {
                return redirect()->route('admin.dashboard');
            }elseif(Auth::user()->role_id == 2){
                return redirect()->route('subadmin.dashboard');
            }elseif(Auth::user()->role_id == 3){
                return redirect()->route('editor.dashboard');
            }else{
                return redirect()->route('author.dashboard');
            }
        }

        return $next($request);
    }
}
