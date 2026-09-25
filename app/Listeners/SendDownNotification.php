<?php

namespace App\Listeners;

use App\Events\DeviceDown;
use App\Services\Alerting\AlertService;

class SendDownNotification
{
    public function __construct(
        private AlertService $alertService
    ) {}

    public function handle(DeviceDown $event): void
    {
        $this->alertService->checkDeviceDown($event->device);
    }
}
