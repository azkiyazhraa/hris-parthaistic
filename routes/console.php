<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Create pending attendance records for approved change day requests when the requested date arrives
Schedule::command('changeday:create-attendance')->dailyAt('00:05');
