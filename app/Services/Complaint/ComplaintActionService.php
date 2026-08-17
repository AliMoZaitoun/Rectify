<?php

namespace App\Services\Complaint;

use App\DAO\Complaint\ComplaintActionDAO;
use App\DAO\Complaint\ComplaintDAO;
use App\DAO\Complaint\ComplaintHistoryDAO;
use App\DTOs\Complaint\ComplaintActionDTO;
use App\DTOs\Complaint\Create\ComplaintHistoryDTO;
use App\Enums\ComplaintStatus;
use App\Events\ComplaintReplyAdded;
use App\Events\ComplaintStatusUpdated;
use App\Exceptions\V1\Complaint\CannotModifyMergedComplaintException;
use App\Models\Complaint\Complaint;
use App\Services\FileManagerService;
use App\Services\TransactionService;

class ComplaintActionService
{
    public function __construct(
        protected ComplaintDAO $complaintDAO,
        protected ComplaintActionDAO $actionDAO,
        protected ComplaintHistoryDAO $historyDAO,
        protected TransactionService $transaction,
        protected FileManagerService $fileManager
    ) {}

    public function addAction(Complaint $complaint, ComplaintActionDTO $dto, array $attachments = [])
    {
        if ($complaint->parent_id !== null) {
            throw new CannotModifyMergedComplaintException();
        }

        $targetStatus = null;
        $oldStatusValue = null;

        $action = $this->transaction->execute(function () use ($complaint, $dto, $attachments, &$targetStatus, &$oldStatusValue) {

            $action = $this->actionDAO->store($dto);

            if (! empty($attachments)) {
                $this->fileManager->storeFile(
                    model: $$complaint,
                    files: $attachments,
                    folderPath: "complaints/{$complaint->id}/actions",
                    relationName: 'media'
                );
            }

            $oldStatusValue = $complaint->status instanceof ComplaintStatus
                ? $complaint->status->value
                : $complaint->status;

            if ($dto->actionType === 'request_documents' && $oldStatusValue !== ComplaintStatus::WAITING_DOCUMENTS->value) {
                $targetStatus = ComplaintStatus::WAITING_DOCUMENTS->value;
            } elseif ($dto->actionType === 'document_submitted' && $oldStatusValue === ComplaintStatus::WAITING_DOCUMENTS->value) {
                $targetStatus = ComplaintStatus::IN_PROGRESS->value;
            }

            if ($targetStatus) {
                $lastHistory = $complaint->histories()->first();
                $durationInHours = $lastHistory
                    ? (int) $lastHistory->created_at->diffInHours(now())
                    : (int) $complaint->created_at->diffInHours(now());

                $this->complaintDAO->update($complaint, ['status' => $targetStatus]);

                $this->historyDAO->store(new ComplaintHistoryDTO(
                    complaintId: $complaint->id,
                    newStatus: $targetStatus,
                    oldStatus: $oldStatusValue,
                    assignedToId: $complaint->assigned_to_id,
                    changedByType: $dto->actorType,
                    changedById: $dto->actorId,
                    durationInHours: $durationInHours,
                    comment: "Automatic status change via action: {$dto->actionType}"
                ));
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
}
