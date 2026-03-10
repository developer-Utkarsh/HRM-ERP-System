<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
   public function boot()
{
    Schema::defaultStringLength(191);

    View::composer('*', function ($view) {
        if (Auth::check()) {
            $userId = Auth::id();

            $hasRequestAccessUpdates = DB::table('system_access_request as sar')
                ->join('system_master as sm', 'sar.software_id', '=', 'sm.id')
                ->where(function ($q) use ($userId) {
                    $q->where('sm.owner_id', $userId)
                      ->orWhere('sar.requester_id', $userId);
                })
                ->where('sar.status', 'InProcess')
                ->exists();

            $view->with('hasRequestAccessUpdates', $hasRequestAccessUpdates);
        }
    });
}

}
