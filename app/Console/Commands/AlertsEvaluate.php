<?php

namespace App\Console\Commands;

use App\Services\Alerting\AlertService;
use Illuminate\Console\Command;

class AlertsEvaluate extends Command
{
    protected $signature = 'alerts:evaluate';
    protected $description = 'Evaluate alert rules and create alerts';

    public function handle(AlertService $alertService): int
    {
        $this->info('Evaluating alert rules...');

        $alerts = $alertService->evaluateRules();

        if (empty($alerts)) {
            $this->info('No new alerts triggered.');
        } else {
            $this->info(count($alerts) . ' alert(s) triggered:');
            foreach ($alerts as $alert) {
                $this->warn("  [{$alert->severity}] {$alert->message}");
            }
        }

        return self::SUCCESS;
    }
}
