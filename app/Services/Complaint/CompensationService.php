<?php

namespace App\Services\Complaint;

use App\DAO\Client\ClientDAO;
use App\DAO\Complaint\CompensationDAO;
use App\DTOs\Complaint\CompensationDTO;
use App\Enums\CompensationStatus;
use App\Enums\CompensationType;
use App\Models\Complaint\ComplaintCompensation;
use App\Services\Transaction;
use Exception;

class CompensationService
{
    public function __construct(
        private CompensationDAO $compensationDAO,
        private ClientDAO $clientDAO,
        private Transaction $transaction
    ) {}

    public function getAll(array $filters = [], int $perPage = 15)
    {
        return $this->compensationDAO->paginate($filters, $perPage);
    }

    public function compensate(CompensationDTO $dto): ComplaintCompensation
    {
        return $this->transaction->execute(function () use ($dto) {

            $existing = $this->compensationDAO->byComplaintId($dto->complaintId);
            if ($existing) {
                throw new Exception(__('messages.complaint.already_compensated'));
            }

            $isPointsForClient = ($dto->type === CompensationType::POINTS && $dto->clientId && $dto->amount > 0);

            $status = $isPointsForClient ? CompensationStatus::GRANTED->value : CompensationStatus::PENDING->value;
            $grantedAt = $isPointsForClient ? now() : null;

            $compensationData = [
                'complaint_id'   => $dto->complaintId,
                'client_id'      => $dto->clientId,
                'approved_by_id' => $dto->approvedById,
                'type'           => $dto->type->value,
                'amount'         => $dto->amount,
                'coupon_code'    => $dto->couponCode,
                'notes'          => $dto->notes,
                'status'         => $status,
                'granted_at'     => $grantedAt,
            ];

            $compensation = $this->compensationDAO->store($compensationData);

            if ($isPointsForClient) {
                $this->clientDAO->incrementPoints($dto->clientId, (int) $dto->amount);
            }

            return $compensation;
        });
    }

    public function updateStatus(int $compensationId, CompensationStatus $status): ComplaintCompensation
    {
        return $this->transaction->execute(function () use ($compensationId, $status) {
            $compensation = $this->compensationDAO->ById($compensationId);

            if (! $compensation) {
                throw new Exception(__('messages.common.not_found'));
            }

            if ($status === CompensationStatus::GRANTED && $compensation->status !== CompensationStatus::GRANTED->value) {

                $typeValue = $compensation->type instanceof CompensationType
                    ? $compensation->type->value
                    : $compensation->type;

                if ($typeValue === CompensationType::POINTS->value && $compensation->client_id) {
                    $this->clientDAO->incrementPoints($compensation->client_id, (int) $compensation->amount);
                }

                $compensation->granted_at = now();
            }

            $compensation->status = $status->value;
            $this->compensationDAO->update($compensation, $compensation->toArray());

            return $compensation->fresh();
        });
    }

    public function delete(int $compensationId): bool
    {
        return $this->transaction->execute(function () use ($compensationId) {
            $compensation = $this->compensationDAO->ById($compensationId);

            if (! $compensation) {
                throw new Exception(__('messages.common.not_found'));
            }

            $statusValue = $compensation->status instanceof CompensationStatus
                ? $compensation->status->value
                : $compensation->status;

            if ($statusValue === CompensationStatus::GRANTED->value) {
                throw new Exception(__('messages.complaint.cannot_delete_granted_compensation'));
            }

            return $this->compensationDAO->delete($compensation);
        });
    }

    public function getCompensationForComplaint(int $complaintId): ?ComplaintCompensation
    {
        return $this->compensationDAO->byComplaintId($complaintId, ['approvedBy', 'client']);
    }
}
