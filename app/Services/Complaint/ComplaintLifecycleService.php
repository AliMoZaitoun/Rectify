<?php

namespace App\Services\Complaint;

use App\DAO\Complaint\ComplaintDAO;
use App\DAO\Complaint\ComplaintHistoryDAO;
use App\DAO\Complaint\ComplaintRatingDAO;
use App\DTOs\Complaint\ChangeComplaintStatusDTO;
use App\DTOs\Complaint\Create\ComplaintHistoryDTO;
use App\DTOs\Complaint\RateComplaintDTO;
use App\DTOs\Complaint\ReopenComplaintDTO;
use App\Enums\ComplaintStatus;
use App\Events\ComplaintStatusUpdated;
use App\Exceptions\V1\Complaint\CannotModifyMergedComplaintException;
use App\Exceptions\V1\Complaint\CannotReopenComplaintException;
use App\Exceptions\V1\Complaint\ComplaintAlreadyRatedException;
use App\Exceptions\V1\Complaint\ComplaintNotResolvedForRatingException;
use App\Models\Complaint\Complaint;
use App\Services\TransactionService;

class ComplaintLifecycleService
{
    public function __construct(
        protected ComplaintDAO $complaintDAO,
        protected ComplaintHistoryDAO $historyDAO,
        protected ComplaintRatingDAO $ratingDAO,
        protected TransactionService $transaction
    ) {}

    public function changeStatus(Complaint $complaint, ChangeComplaintStatusDTO $dto): Complaint
    {
        if ($complaint->parent_id !== null) {
            throw new CannotModifyMergedComplaintException();
        }

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

            $this->historyDAO->store(new ComplaintHistoryDTO(
                complaintId: $complaint->id,
                newStatus: $newStatusValue,
                oldStatus: $oldStatusValue,
                assignedToId: $dto->assignedToId ?? $complaint->assigned_to_id,
                changedByType: $dto->changedByType,
                changedById: $dto->changedById,
                durationInHours: $durationInHours,
                comment: $dto->comment
            ));

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
                        comment: "complaint.history.auto_updated_parent|{$complaint->tracking_code}"
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

    public function rateComplaint(Complaint $complaint, RateComplaintDTO $dto)
    {
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

        return $this->transaction->execute(function () use ($complaint, $dto, $statusValue) {
            $rating = $this->ratingDAO->store($dto->toArray($complaint->id));

            $targetStatus = ComplaintStatus::CLOSED->value;
            $this->complaintDAO->update($complaint, ['status' => $targetStatus]);

            $lastHistory = $complaint->histories()->first();
            $durationInHours = $lastHistory
                ? (int) $lastHistory->created_at->diffInHours(now())
                : (int) $complaint->created_at->diffInHours(now());

            $this->historyDAO->store(new ComplaintHistoryDTO(
                complaintId: $complaint->id,
                newStatus: $targetStatus,
                oldStatus: $statusValue,
                assignedToId: $complaint->assigned_to_id,
                changedByType: 'client',
                changedById: $dto->clientId ?? $complaint->client_id,
                durationInHours: $durationInHours,
                comment: 'complaint.history.closed_after_rating'
            ));

            ComplaintStatusUpdated::dispatch($complaint, $statusValue);

            return $rating;
        });
    }

    public function reopenComplaint(Complaint $complaint, ReopenComplaintDTO $dto): Complaint
    {
        $oldStatus = $complaint->status instanceof ComplaintStatus
            ? $complaint->status->value
            : $complaint->status;

        if ($oldStatus !== ComplaintStatus::RESOLVED->value) {
            throw new CannotReopenComplaintException();
        }

        $updatedComplaint = $this->transaction->execute(function () use ($complaint, $oldStatus, $dto) {
            $targetStatus = ComplaintStatus::IN_PROGRESS->value;

            $this->complaintDAO->update($complaint, [
                'status'      => $targetStatus,
                'resolved_at' => null,
            ]);

            $lastHistory = $complaint->histories()->first();
            $durationInHours = $lastHistory
                ? (int) $lastHistory->created_at->diffInHours(now())
                : (int) $complaint->created_at->diffInHours(now());

            $this->historyDAO->store(new ComplaintHistoryDTO(
                complaintId: $complaint->id,
                newStatus: $targetStatus,
                oldStatus: $oldStatus,
                assignedToId: $complaint->assigned_to_id,
                changedByType: $dto->actorType,
                changedById: $dto->actorId,
                durationInHours: $durationInHours,
                comment: "complaint.history.reopened_by_customer|{$dto->reason}"
            ));

            return $complaint->fresh(['branch', 'category', 'media', 'actions', 'histories', 'compensation', 'latestRating']);
        });

        ComplaintStatusUpdated::dispatch($updatedComplaint, $oldStatus);

        return $updatedComplaint;
    }
}
