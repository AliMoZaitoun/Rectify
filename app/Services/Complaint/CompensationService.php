<?php

namespace App\Services\Complaint;

use App\DAO\Client\ClientDAO;
use App\DAO\Complaint\CompensationDAO;
use App\DTOs\Complaint\CompensationDTO;
use App\DAO\Complaint\ComplaintDAO;
use App\Enums\ComplaintStatus;
use App\Enums\CompensationStatus;
use App\Enums\CompensationType;
use App\Exceptions\V1\Complaint\CannotDeleteGrantedCompensationException;
use App\Exceptions\V1\Complaint\CompensationNotFoundException;
use App\Exceptions\V1\Complaint\ComplaintAlreadyCompensatedException;
use App\Exceptions\V1\Complaint\UnresolvedComplaintCompensationException;
use App\Models\Complaint\ComplaintCompensation;
use App\Services\Transaction;
use Exception;

class CompensationService
{
    public function __construct(
        private CompensationDAO $compensationDAO,
        private ClientDAO $clientDAO,
        private ComplaintDAO $complaintDAO,
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
                throw new ComplaintAlreadyCompensatedException();
            }

            $complaint = $this->complaintDAO->byId($dto->complaintId);

            $complaintStatus = $complaint->status instanceof ComplaintStatus
                ? $complaint->status->value
                : $complaint->status;

            if ($complaintStatus !== ComplaintStatus::RESOLVED->value) {
                throw new UnresolvedComplaintCompensationException();
            }

            $isAnonymous = $complaint ? ((bool) $complaint->is_anonymous || is_null($dto->clientId)) : true;

            $isPointsForClient = ($dto->type === CompensationType::POINTS && $dto->clientId && $dto->amount > 0 && ! $isAnonymous);

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

    public function getByClient(int $clientId, array $relations = [], int $perPage = 15)
    {
        return $this->compensationDAO->byClient($clientId, $relations, $perPage);
    }

    public function updateStatus(int $compensationId, CompensationStatus $status): ComplaintCompensation
    {
        return $this->transaction->execute(function () use ($compensationId, $status) {
            $compensation = $this->compensationDAO->ById($compensationId);

            if (! $compensation) {
                throw new CompensationNotFoundException();
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
                throw new CompensationNotFoundException();
            }

            $statusValue = $compensation->status instanceof CompensationStatus
                ? $compensation->status->value
                : $compensation->status;

            if ($statusValue === CompensationStatus::GRANTED->value) {
                throw new CannotDeleteGrantedCompensationException();
            }

            return $this->compensationDAO->delete($compensation);
        });
    }

    public function getCompensationForComplaint(int $complaintId, array $relations = []): ?ComplaintCompensation
    {
        return $this->compensationDAO->byComplaintId($complaintId, $relations);
    }
}
