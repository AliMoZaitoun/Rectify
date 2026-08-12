<?php

namespace App\Jobs;

use App\DAO\Complaint\ComplaintDAO;
use App\DAO\Complaint\ComplaintHistoryDAO;
use App\DTOs\Complaint\Create\ComplaintHistoryDTO;
use App\Enums\ComplaintStatus;
use App\Models\Complaint\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EscalateOverdueComplaintsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(ComplaintDAO $complaintDAO, ComplaintHistoryDAO $historyDAO): void
    {
        $overdueComplaints = Complaint::query()
            ->whereNull('parent_id')
            ->whereNotIn('status', [ComplaintStatus::RESOLVED->value, ComplaintStatus::ESCALATED->value])
            ->where('sla_due_at', '<', now())
            ->with(['branch.manager'])
            ->get();

        foreach ($overdueComplaints as $complaint) {
            $oldStatus = $complaint->status instanceof ComplaintStatus ? $complaint->status->value : $complaint->status;

            $branchManagerEmployeeId = $complaint->branch?->manager_id;

            $updateData = [
                'status' => ComplaintStatus::ESCALATED->value,
            ];

            if ($branchManagerEmployeeId) {
                $updateData['assigned_to_id'] = $branchManagerEmployeeId;
            }

            $complaintDAO->update($complaint, $updateData);

            $historyDAO->store(new ComplaintHistoryDTO(
                complaintId: $complaint->id,
                newStatus: ComplaintStatus::ESCALATED->value,
                oldStatus: $oldStatus,
                assignedToId: $branchManagerEmployeeId,
                changedByType: null,
                changedById: null,
                comment: __('messages.complaint.history.sla_escalated_internal'),
                is_visible: false
            ));

            // if ($complaint->branch?->manager) {
            //     $complaint->branch->manager->notify(new ComplaintEscalatedNotification($complaint));
            // }
        }
    }
}
