<?php

namespace App\Listeners;

use App\Events\AlertTriggered;
use App\Services\Alerting\NotificationService;

class SendAlertNotification
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function handle(AlertTriggered $event): void
    {
        $this->notificationService->sendAlertNotification($event->alert);
    }
}
