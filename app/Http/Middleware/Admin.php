<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\Cache;


class Admin
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
        /*$route=$request->route()->getName();//die();
        if($user->role_id != 1 && $user->role_id != 24 && $user->role_id != 21 && $user->role_id != 29 && $user->role_id != 20 && $user->role_id != 28 && $user->user_details->degination != 'STUDIO ASSISTANT MANAGER' && $user->user_details->degination != 'TIME TABLE MANAGER' && $user->role_id != 16 && $user->role_id != 6 && $user->role_id != 22 && $user->role_id != 23 && $user->role_id != 25 && $user->role_id != 26 && $user->role_id != 30 && $user->role_id != 3 && $user->role_id !=31 && $user->role_id !=2 && $user->role_id !=32 && !str_starts_with($route,'admin.support')) {
            Auth::logout();
            return redirect(route('login'));
        }*/

        $permissions=Cache::get('permissionRole'.$user->id);
        $route=$request->route()->getName();
        if(!empty($permissions) && count($permissions)){
            if(!in_array($route,$permissions)){
              // echo "No Permission ".$route;die();
            }
        }

        return $next($request);
    }
}
