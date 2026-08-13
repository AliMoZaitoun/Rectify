<?php

namespace App\Services\Client;

use App\DAO\Client\ClientDAO;
use Carbon\Carbon;

class ChurnRiskService
{
    public function __construct(
        private ClientDAO $clientDAO
    ) {}

    public function getAlerts(): array
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30)->toDateTimeString();

        $clients = $this->clientDAO->getAtRiskClients($thirtyDaysAgo);

        return $clients->map(function ($client) {
            $reasons = [];
            $riskLevel = 'medium';

            if ($client->recent_complaints_count >= 3) {
                $reasons[] = 'تكرار الشكاوى (' . $client->recent_complaints_count . ' خلال 30 يوم)';
                $riskLevel = 'high';
            }

            if ($client->rejected_compensations_count > 0) {
                $reasons[] = 'رفض تعويض مؤخراً';
                $riskLevel = 'critical';
            }

            if (count($reasons) > 1) {
                $riskLevel = 'critical';
            }

            return [
                'id'           => $client->id,
                'name'         => $client->user?->full_name ?? $client->user?->first_name,
                'email'        => $client->user?->email,
                'phone'        => $client->user?->phone,
                'points'       => $client->points,
                'risk_level'   => $riskLevel,
                'risk_reasons' => $reasons,
            ];
        })->toArray();
    }
}
