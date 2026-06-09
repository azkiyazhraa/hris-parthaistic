<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\AbsensiKaryawan;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {

            $absensiToday = null;
            $pendingChangeDays = 0;

            if (Auth::check()) {
                $today = Carbon::today();

                $absensiToday = AbsensiKaryawan::where('karyawan_id', Auth::id())
                    ->whereDate('tanggal', $today)
                    ->where('is_change_day', false)
                    ->first();

                $pendingChangeDays = AbsensiKaryawan::where('karyawan_id', Auth::id())
                    ->where('is_change_day', true)
                    ->where('change_day_status', AbsensiKaryawan::CHANGE_DAY_PENDING)
                    ->count();
            }

            $view->with([
                'absensiToday' => $absensiToday,
                'pendingChangeDays' => $pendingChangeDays
            ]);
        });
    }
}
