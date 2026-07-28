<?php

namespace App\Services\Complaint;

use App\DAO\Complaint\ComplaintDAO;
use App\DAO\Complaint\CategoryDAO;
use App\DAO\Complaint\ComplaintActionDAO;
use App\DAO\Complaint\ComplaintHistoryDAO;
use App\DTOs\Complaint\ChangeComplaintStatusDTO;
use App\DTOs\Complaint\ComplaintActionDTO;
use App\DTOs\Complaint\Create\ComplaintHistoryDTO;
use App\DTOs\Complaint\Create\CreateComplaintDTO;
use App\Exceptions\NotFoundException;
use App\Models\Complaint\Complaint;
use App\Services\FileManagerService;
use App\Services\TransactionService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use App\Enums\ComplaintStatus;
use App\Enums\ComplaintPriority;

class ComplaintService
{
    public function __construct(
        protected ComplaintDAO $complaintDAO,
        protected CategoryDAO $categoryDAO,
        protected ComplaintHistoryDAO $historyDAO,
        protected ComplaintActionDAO $actionDAO,
        private TransactionService $transaction,
        private FileManagerService $fileManager
    ) {}

    public function paginate(array $relations = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->complaintDAO->paginate($relations, $perPage);
    }

    public function findById(int $id, array $relations = []): Complaint
    {
        $complaint = $this->complaintDAO->byId($id, $relations);
        if (!$complaint) {
            throw new NotFoundException("Complaint");
        }
        return $complaint;
    }

    public function trackByCode(string $code, array $relations = []): Complaint
    {
        $complaint = $this->complaintDAO->byTrackingCode($code, $relations);
        if (!$complaint) {
            throw new NotFoundException("Complaint");
        }
        return $complaint;
    }

    public function clientComplaints(?int $clientId, ?string $deviceId = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->complaintDAO->byClientOrDevice($clientId, $deviceId, $perPage);
    }

    public function branchComplaints(int $branchId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->complaintDAO->byBranch($branchId, $filters, $perPage);
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
                'client_id'      => $clientId,
                'device_id'      => $dto->device_id,
                'branch_id'      => $dto->branchId,
                'category_id'    => $dto->categoryId,
                'title'          => $dto->title,
                'description'    => $dto->description,
                'priority'       => ComplaintPriority::MEDIUM->value,
                'is_anonymous'   => $dto->isAnonymous,
                'tracking_code'  => $trackingCode,
                'sla_due_at'     => $slaDueAt,
                'status' => ComplaintStatus::PENDING->value,
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

            if (!empty($attachments)) {
                $this->fileManager->storeFile(
                    model: $complaint,
                    files: $attachments,
                    folderPath: "complaints",
                    relationName: 'media'
                );
            }

            return $complaint;
        });
    }

    public function changeStatus(Complaint $complaint, ChangeComplaintStatusDTO $dto): Complaint
    {
        return $this->transaction->execute(function () use ($complaint, $dto) {
            $oldStatus = $complaint->status instanceof ComplaintStatus
                ? $complaint->status->value
                : $complaint->status;

            $newStatusValue = $dto->status->value;

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
                oldStatus: $oldStatus,
                assignedToId: $dto->assignedToId ?? $complaint->assigned_to_id,
                changedByType: $dto->changedByType,
                changedById: $dto->changedById,
                durationInHours: $durationInHours,
                comment: $dto->comment
            );

            $this->historyDAO->store($historyDto);

            return $complaint->fresh(['histories', 'assignedTo']);
        });
    }


    public function updateStatus(Complaint $complaint, string $status): bool
    {
        return $this->complaintDAO->update($complaint, ['status' => $status]);
    }

    public function addAction(Complaint $complaint, ComplaintActionDTO $dto, array $attachments = [])
    {
        return $this->transaction->execute(function () use ($complaint, $dto, $attachments) {

            $action = $this->actionDAO->store($dto);

            if (!empty($attachments)) {
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
    }
}
