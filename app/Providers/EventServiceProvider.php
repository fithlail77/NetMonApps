<?php

namespace App\Providers;

use App\Events\AlertTriggered;
use App\Events\DeviceDown;
use App\Events\DeviceUp;
use App\Listeners\SendAlertNotification;
use App\Listeners\SendDownNotification;
use App\Listeners\SendUpNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DeviceDown::class => [
            SendDownNotification::class,
        ],
        DeviceUp::class => [
            SendUpNotification::class,
        ],
        AlertTriggered::class => [
            SendAlertNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
