<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('monitoring:ping')->everyMinute();
Schedule::command('monitoring:snmp')->everyFiveMinutes();
Schedule::command('alerts:evaluate')->everyMinute();
Schedule::command('alerts:cleanup')->daily();
