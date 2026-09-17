<?php

namespace App\Listeners;

use App\Events\DeviceDown;
use App\Services\Alerting\AlertService;
use App\Services\Alerting\NotificationService;

class SendDownNotification
{
    public function __construct(
        private AlertService $alertService,
        private NotificationService $notificationService
    ) {
    }

    public function handle(DeviceDown $event): void
    {
        $alert = $this->alertService->checkDeviceDown($event->device);

        if ($alert) {
            $this->notificationService->sendAlertNotification($alert);
        }
    }
}
