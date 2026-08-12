<?php

namespace App\DTOs\Complaint\Create;

class ComplaintHistoryDTO
{
    public function __construct(
        public int $complaintId,
        public string $newStatus,
        public ?string $oldStatus = null,
        public ?int $assignedToId = null,
        public ?string $changedByType = null,
        public ?int $changedById = null,
        public int $durationInHours = 0,
        public ?string $comment = null,
        public bool $is_visible = true
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            complaintId: $data['complaint_id'],
            newStatus: $data['new_status'],
            oldStatus: $data['old_status'] ?? null,
            assignedToId: $data['assigned_to_id'] ?? null,
            changedByType: $data['changed_by_type'] ?? null,
            changedById: $data['changed_by_id'] ?? null,
            durationInHours: $data['duration_in_hours'] ?? 0,
            comment: $data['comment'] ?? null,
            is_visible: $data['is_visible'] ?? true
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'complaint_id'      => $this->complaintId,
            'old_status'        => $this->oldStatus,
            'new_status'        => $this->newStatus,
            'assigned_to_id'    => $this->assignedToId,
            'changed_by_type'   => $this->changedByType,
            'changed_by_id'     => $this->changedById,
            'duration_in_hours' => $this->durationInHours,
            'comment'           => $this->comment,
            'is_visible'        => $this->is_visible
        ], fn($value) => !is_null($value));
    }
}
