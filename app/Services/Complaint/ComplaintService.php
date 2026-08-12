<?php

namespace App\Services\Complaint;

use App\DAO\Client\ClientDAO;
use App\DAO\Complaint\CategoryDAO;
use App\DAO\Complaint\CompensationDAO;
use App\DAO\Complaint\ComplaintActionDAO;
use App\DAO\Complaint\ComplaintDAO;
use App\DAO\Complaint\ComplaintHistoryDAO;
use App\DAO\Complaint\ComplaintRatingDAO;
use App\DTOs\Complaint\ChangeComplaintStatusDTO;
use App\DTOs\Complaint\ComplaintActionDTO;
use App\DTOs\Complaint\Create\ComplaintHistoryDTO;
use App\DTOs\Complaint\Create\CreateComplaintDTO;
use App\DTOs\Complaint\RateComplaintDTO;
use App\DTOs\Complaint\ReopenComplaintDTO;
use App\Enums\CompensationStatus;
use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Events\ComplaintReplyAdded;
use App\Events\ComplaintStatusUpdated;
use App\Exceptions\V1\Complaint\CannotReopenComplaintException;
use App\Exceptions\V1\Complaint\ComplaintAlreadyRatedException;
use App\Exceptions\V1\Complaint\ComplaintNotFoundException;
use App\Exceptions\V1\Complaint\ComplaintNotResolvedForRatingException;
use App\Exceptions\V1\Complaint\DeviceIdRequiredException;
use App\Models\Complaint\Complaint;
use App\Services\FileManagerService;
use App\Services\TransactionService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ComplaintService
{
    public function __construct(
        protected ComplaintDAO $complaintDAO,
        protected CategoryDAO $categoryDAO,
        protected ComplaintHistoryDAO $historyDAO,
        protected ComplaintActionDAO $actionDAO,
        private CompensationDAO $compensationDAO,
        protected ClientDAO $clientDAO,
        protected ComplaintRatingDAO $ratingDAO,
        private TransactionService $transaction,
        private FileManagerService $fileManager
    ) {}

    public function paginate(array $filters = [], array $relations = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->complaintDAO->paginate($filters, $relations, $perPage);
    }

    public function findById(int $id, array $relations = []): Complaint
    {
        $complaint = $this->complaintDAO->byId($id, $relations);
        if (! $complaint) {
            throw new ComplaintNotFoundException();
        }

        return $complaint;
    }

    public function trackByCode(string $code, array $relations = []): Complaint
    {
        $complaint = $this->complaintDAO->byTrackingCode($code, $relations);
        if (! $complaint) {
            throw new ComplaintNotFoundException();
        }

        return $complaint;
    }

    public function clientComplaints(?int $clientId, ?string $deviceId = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->complaintDAO->byClientOrDevice($clientId, $deviceId, $perPage);
    }

    public function branchComplaints(int $branchId, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        return $this->complaintDAO->byBranch($branchId, $filters, $perPage, $relations);
    }

    public function createComplaint(CreateComplaintDTO $dto, array $attachments = []): Complaint
    {
        return $this->transaction->execute(function () use ($dto, $attachments) {
            $category = $this->categoryDAO->byId($dto->categoryId);

            $slaHours = $category ? $category->sla_hours : 24;
            $slaDueAt = now()->addHours($slaHours);

            $trackingCode = 'CMP-' . strtoupper(Str::random(8));

            $clientId = $dto->clientId;

            $complaintData = [
                'client_id'     => $clientId,
                'device_id'     => $dto->device_id,
                'branch_id'     => $dto->branchId,
                'category_id'   => $dto->categoryId,
                'title'         => $dto->title,
                'description'   => $dto->description,
                'priority'      => ComplaintPriority::MEDIUM->value,
                'is_anonymous'  => $dto->isAnonymous,
                'tracking_code' => $trackingCode,
                'sla_due_at'    => $slaDueAt,
                'status'        => ComplaintStatus::PENDING->value,
            ];

            $complaint = $this->complaintDAO->store($complaintData);

            $historyDto = new ComplaintHistoryDTO(
                complaintId: $complaint->id,
                newStatus: ComplaintStatus::PENDING->value,
                changedByType: $clientId ? 'App\Models\Client' : null,
                changedById: $clientId,
                comment: 'Complaint submitted'
            );

            $this->historyDAO->store($historyDto);

            if (! empty($attachments)) {
                $this->fileManager->storeFile(
                    model: $complaint,
                    files: $attachments,
                    folderPath: 'complaints',
                    relationName: 'media'
                );
            }

            return $complaint;
        });
    }

    public function changeStatus(Complaint $complaint, ChangeComplaintStatusDTO $dto): Complaint
    {
        $oldStatusValue = $complaint->status instanceof ComplaintStatus
            ? $complaint->status->value
            : (string) $complaint->status;

        $newStatusValue = $dto->status->value;

        $updatedComplaint = $this->transaction->execute(function () use ($complaint, $dto, $oldStatusValue, $newStatusValue) {

            $lastHistory = $complaint->histories()->first();
            $durationInHours = $lastHistory
                ? (int) $lastHistory->created_at->diffInHours(now())
                : (int) $complaint->created_at->diffInHours(now());

            $updateData = ['status' => $newStatusValue];
            if ($dto->assignedToId) {
                $updateData['assigned_to_id'] = $dto->assignedToId;
            }
            if ($newStatusValue === ComplaintStatus::RESOLVED->value) {
                $updateData['resolved_at'] = now();
            }

            $this->complaintDAO->update($complaint, $updateData);

            $historyDto = new ComplaintHistoryDTO(
                complaintId: $complaint->id,
                newStatus: $newStatusValue,
                oldStatus: $oldStatusValue,
                assignedToId: $dto->assignedToId ?? $complaint->assigned_to_id,
                changedByType: $dto->changedByType,
                changedById: $dto->changedById,
                durationInHours: $durationInHours,
                comment: $dto->comment
            );

            $this->historyDAO->store($historyDto);

            if ($complaint->children()->exists()) {
                foreach ($complaint->children as $child) {
                    $childOldStatus = $child->status instanceof ComplaintStatus ? $child->status->value : (string) $child->status;

                    $childUpdateData = ['status' => $newStatusValue];
                    if ($newStatusValue === ComplaintStatus::RESOLVED->value) {
                        $childUpdateData['resolved_at'] = now();
                    }

                    $this->complaintDAO->update($child, $childUpdateData);

                    $this->historyDAO->store(new ComplaintHistoryDTO(
                        complaintId: $child->id,
                        newStatus: $newStatusValue,
                        oldStatus: $childOldStatus,
                        changedByType: $dto->changedByType,
                        changedById: $dto->changedById,
                        comment: "Auto-updated via parent complaint #{$complaint->tracking_code}"
                    ));

                    ComplaintStatusUpdated::dispatch($child, $childOldStatus);
                }
            }

            return $complaint->fresh(['histories', 'assignedTo', 'client.user']);
        });

        if ($oldStatusValue !== $newStatusValue) {
            ComplaintStatusUpdated::dispatch($updatedComplaint, $oldStatusValue);
        }

        return $updatedComplaint;
    }

    public function updateStatus(Complaint $complaint, string $status)
    {
        $oldStatus = $complaint->status;
        $updated = $this->complaintDAO->update($complaint, ['status' => $status]);

        if ($updated) {
            $complaint->status = $status;
            ComplaintStatusUpdated::dispatch($complaint, $oldStatus);
        }

        return $updated;
    }

    public function addAction(Complaint $complaint, ComplaintActionDTO $dto, array $attachments = [])
    {
        $targetStatus = null;
        $oldStatusValue = null;

        $action = $this->transaction->execute(function () use ($complaint, $dto, $attachments) {

            $action = $this->actionDAO->store($dto);

            if (! empty($attachments)) {
                $this->fileManager->storeFile(
                    model: $action,
                    files: $attachments,
                    folderPath: "complaints/{$complaint->id}/actions",
                    relationName: 'media'
                );
            }

            $oldStatus = $complaint->status instanceof ComplaintStatus
                ? $complaint->status->value
                : $complaint->status;

            $targetStatus = null;

            if ($dto->actionType === 'request_documents' && $oldStatus !== ComplaintStatus::WAITING_DOCUMENTS->value) {
                $targetStatus = ComplaintStatus::WAITING_DOCUMENTS->value;
            } elseif ($dto->actionType === 'document_submitted' && $oldStatus === ComplaintStatus::WAITING_DOCUMENTS->value) {
                $targetStatus = ComplaintStatus::IN_PROGRESS->value;
            }

            if ($targetStatus) {
                $lastHistory = $complaint->histories()->first();
                $durationInHours = $lastHistory
                    ? (int) $lastHistory->created_at->diffInHours(now())
                    : (int) $complaint->created_at->diffInHours(now());

                $this->complaintDAO->update($complaint, ['status' => $targetStatus]);

                $historyDto = new ComplaintHistoryDTO(
                    complaintId: $complaint->id,
                    newStatus: $targetStatus,
                    oldStatus: $oldStatus,
                    assignedToId: $complaint->assigned_to_id,
                    changedByType: $dto->actorType,
                    changedById: $dto->actorId,
                    durationInHours: $durationInHours,
                    comment: "Automatic status change via action: {$dto->actionType}"
                );

                $this->historyDAO->store($historyDto);
            }

            return $action->load(['actor', 'media']);
        });

        ComplaintReplyAdded::dispatch($complaint, $action);
        if ($targetStatus) {

            $complaint->status = $targetStatus;
            ComplaintStatusUpdated::dispatch($complaint, $oldStatusValue);
        }

        return $action;
    }

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

            return $updated;
        });
    }

    private function processPendingCompensations(int $clientId): void
    {
        $pendingCompensations = $this->compensationDAO->getPendingPointsCompensationsByClient($clientId);

        foreach ($pendingCompensations as $compensation) {
            $this->clientDAO->incrementPoints($clientId, (int) $compensation->amount);

            $this->compensationDAO->update($compensation, [
                'status'     => \App\Enums\CompensationStatus::GRANTED->value,
                'granted_at' => now(),
            ]);
        }
    }

    public function rateComplaint(string $code, RateComplaintDTO $dto)
    {
        $complaint = $this->trackByCode($code);

        $statusValue = $complaint->status instanceof ComplaintStatus
            ? $complaint->status->value
            : $complaint->status;

        if ($statusValue !== ComplaintStatus::RESOLVED->value) {
            throw new ComplaintNotResolvedForRatingException();
        }

        $latestRating = $this->ratingDAO->latestByComplaintId($complaint->id);
        if ($latestRating && $complaint->resolved_at && $latestRating->created_at->gt($complaint->resolved_at)) {
            throw new ComplaintAlreadyRatedException();
        }

        return $this->transaction->execute(function () use ($complaint, $dto) {
            return $this->ratingDAO->store($dto->toArray($complaint->id));
        });
    }

    public function reopenComplaint(ReopenComplaintDTO $dto): Complaint
    {
        $complaint = $this->trackByCode($dto->complaintCode);

        $oldStatus = $complaint->status instanceof ComplaintStatus
            ? $complaint->status->value
            : $complaint->status;

        if ($oldStatus !== ComplaintStatus::RESOLVED->value) {
            throw new CannotReopenComplaintException();
        }

        return $this->transaction->execute(function () use ($complaint, $oldStatus, $dto) {
            $targetStatus = ComplaintStatus::IN_PROGRESS->value;

            $this->complaintDAO->update($complaint, [
                'status'      => $targetStatus,
                'resolved_at' => null,
            ]);

            $lastHistory = $complaint->histories()->first();
            $durationInHours = $lastHistory
                ? (int) $lastHistory->created_at->diffInHours(now())
                : (int) $complaint->created_at->diffInHours(now());

            $historyDto = new ComplaintHistoryDTO(
                complaintId: $complaint->id,
                newStatus: $targetStatus,
                oldStatus: $oldStatus,
                assignedToId: $complaint->assigned_to_id,
                changedByType: $dto->actorType,
                changedById: $dto->actorId,
                durationInHours: $durationInHours,
                comment: "Reopened by customer: {$dto->reason}"
            );

            $this->historyDAO->store($historyDto);

            return $complaint->fresh(['branch', 'category', 'media', 'actions', 'histories', 'compensation', 'latestRating']);
        });
    }

    public function mergeComplaints(Complaint $parentComplaint, array $childIds, $employee = null): void
    {
        $this->transaction->execute(function () use ($parentComplaint, $childIds, $employee) {
            $this->complaintDAO->updateParentId($childIds, $parentComplaint->id);

            foreach ($childIds as $childId) {
                $child = $this->complaintDAO->byId($childId);
                $currentStatus = $child?->status instanceof ComplaintStatus ? $child->status->value : $child?->status;

                $this->historyDAO->store(new ComplaintHistoryDTO(
                    complaintId: $childId,
                    newStatus: $currentStatus,
                    oldStatus: $currentStatus,
                    changedByType: $employee ? get_class($employee) : null,
                    changedById: $employee?->id,
                    comment: __('messages.complaint.history.merged', ['code' => $parentComplaint->tracking_code])
                ));
            }
        });
    }

    public function unmergeComplaint(int $childId, $employee = null): Complaint
    {
        $childComplaint = $this->findById($childId);

        return $this->transaction->execute(function () use ($childComplaint, $employee) {
            $parentCode = $childComplaint->parent?->tracking_code ?? 'N/A';

            $this->complaintDAO->update($childComplaint, [
                'parent_id' => null
            ]);

            $this->historyDAO->store(new ComplaintHistoryDTO(
                complaintId: $childComplaint->id,
                newStatus: $childComplaint->status instanceof ComplaintStatus ? $childComplaint->status->value : $childComplaint->status,
                changedByType: $employee ? get_class($employee) : null,
                changedById: $employee?->id,
                comment: "Unmerged from parent complaint #{$parentCode}"
            ));

            return $childComplaint->fresh();
        });
    }
}
