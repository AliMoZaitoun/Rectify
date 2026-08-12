<?php

namespace App\Services\Complaint;

use App\DAO\Complaint\ComplaintDAO;
use App\DAO\Complaint\ComplaintHistoryDAO;
use App\DTOs\Complaint\Create\ComplaintHistoryDTO;
use App\Enums\ComplaintStatus;
use App\Exceptions\V1\Complaint\CannotMergeParentComplaintException;
use App\Models\Complaint\Complaint;
use App\Services\TransactionService;

class ComplaintMergeService
{
    public function __construct(
        protected ComplaintDAO $complaintDAO,
        protected ComplaintHistoryDAO $historyDAO,
        protected TransactionService $transaction
    ) {}

    public function mergeComplaints(Complaint $parentComplaint, array $childIds, $employee = null): void
    {
        $this->transaction->execute(function () use ($parentComplaint, $childIds, $employee) {
            foreach ($childIds as $childId) {
                $child = $this->complaintDAO->byId($childId);

                if ($child && $child->children()->exists()) {
                    throw new CannotMergeParentComplaintException();
                }
            }

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
                    comment: __('messages.complaint.history.merged_internal'),
                    is_visible: false
                ));
            }
        });
    }

    public function unmergeComplaint(Complaint $childComplaint, $employee = null): Complaint
    {
        return $this->transaction->execute(function () use ($childComplaint, $employee) {
            $this->complaintDAO->update($childComplaint, [
                'parent_id' => null
            ]);

            $this->historyDAO->store(new ComplaintHistoryDTO(
                complaintId: $childComplaint->id,
                newStatus: $childComplaint->status instanceof ComplaintStatus ? $childComplaint->status->value : $childComplaint->status,
                changedByType: $employee ? get_class($employee) : null,
                changedById: $employee?->id,
                comment: __('messages.complaint.history.unmerged_internal'),
                is_visible: false
            ));

            return $childComplaint->fresh();
        });
    }
}
