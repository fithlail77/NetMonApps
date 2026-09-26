<?php

namespace App\Listeners;

use App\Events\DeviceUp;
use App\Services\Alerting\AlertService;
use App\Services\Alerting\NotificationService;

class SendUpNotification
{
    public function __construct(
        private AlertService $alertService,
        private NotificationService $notificationService
    ) {}

    public function handle(DeviceUp $event): void
    {
        $this->alertService->resolveDeviceAlerts($event->device);

        $this->notificationService->sendDeviceRecoveryNotification($event->device);
    }
}
