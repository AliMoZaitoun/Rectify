<?php

namespace App\Services\Complaint;

use App\DAO\Client\ClientDAO;
use App\DAO\Complaint\CompensationDAO;
use App\DAO\Complaint\ComplaintDAO;
use App\Enums\CompensationStatus;
use App\Exceptions\V1\Complaint\ComplaintNotFoundException;
use App\Exceptions\V1\Complaint\DeviceIdRequiredException;
use App\Services\TransactionService;

class ComplaintGuestLinkService
{
    public function __construct(
        protected ComplaintDAO $complaintDAO,
        protected CompensationDAO $compensationDAO,
        protected ClientDAO $clientDAO,
        protected TransactionService $transaction
    ) {}

    public function linkAllGuestComplaintsToClient(?string $deviceId, int $clientId): int
    {
        if (empty($deviceId)) {
            throw new DeviceIdRequiredException();
        }

        return $this->transaction->execute(function () use ($deviceId, $clientId) {
            $updatedCount = $this->complaintDAO->linkComplaintsAndRevealIdentity($deviceId, $clientId);

            if ($updatedCount > 0) {
                $this->processPendingCompensations($clientId);
            }

            return $updatedCount;
        });
    }

    public function linkSingleGuestComplaintToClient(string $code, int $clientId): bool
    {
        return $this->transaction->execute(function () use ($code, $clientId) {
            $complaint = $this->complaintDAO->byTrackingCode($code);

            if (! $complaint) {
                throw new ComplaintNotFoundException();
            }

            $updated = $this->complaintDAO->update($complaint, [
                'client_id'    => $clientId,
                'is_anonymous' => false,
            ]);

            if ($updated) {
                $this->processPendingCompensations($clientId);
            }

            return true;
        });
    }

    private function processPendingCompensations(int $clientId): void
    {
        $pendingCompensations = $this->compensationDAO->getPendingPointsCompensationsByClient($clientId);

        foreach ($pendingCompensations as $compensation) {
            $this->clientDAO->incrementPoints($clientId, (int) $compensation->amount);

            $this->compensationDAO->update($compensation, [
                'status'     => CompensationStatus::GRANTED->value,
                'granted_at' => now(),
            ]);
        }
    }
}
