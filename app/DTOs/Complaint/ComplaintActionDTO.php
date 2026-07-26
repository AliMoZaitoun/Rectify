<?php

namespace App\DTOs\Complaint;

use App\Http\Requests\V1\Complaint\CreateComplaintActionRequest;
use Illuminate\Database\Eloquent\Model;

class ComplaintActionDTO
{
    public function __construct(
        public int $complaintId,
        public string $actionType,
        public string $content,
        public ?string $actorType = null,
        public ?int $actorId = null,
    ) {}

    public static function fromEmployeeRequest(
        CreateComplaintActionRequest $request,
        int $complaintId,
        ?Model $actor = null
    ): self {
        return new self(
            complaintId: $complaintId,
            actionType: $request->validated('action_type', 'comment'),
            content: $request->validated('content'),
            actorType: $actor ? get_class($actor) : null,
            actorId: $actor?->id,
        );
    }

    public static function fromClientRequest(
        $request,
        int $complaintId,
        ?Model $actor = null
    ): self {
        return new self(
            complaintId: $complaintId,
            actionType: !empty($request->file('attachments')) ? 'document_submitted' : 'client_reply',
            content: $request->validated('content'),
            actorType: $actor ? get_class($actor) : null,
            actorId: $actor?->id,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'complaint_id' => $this->complaintId,
            'action_type'  => $this->actionType,
            'content'      => $this->content,
            'actor_type'   => $this->actorType,
            'actor_id'     => $this->actorId,
        ], fn($val) => !is_null($val));
    }
}
