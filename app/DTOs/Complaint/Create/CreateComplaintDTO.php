<?php

namespace App\DTOs\Complaint\Create;

class CreateComplaintDTO
{
    public function __construct(
        public ?int $clientId,
        public ?string $device_id,
        public int $branchId,
        public int $categoryId,
        public string $title,
        public string $description,
        public bool $isAnonymous = false,
        public ?array $mediaFiles = []
    ) {}

    public static function fromRequest($request, ?int $clientId): self
    {
        return new self(
            clientId: $clientId,
            device_id: $request->validated('device_id'),
            branchId: $request->validated('branch_id'),
            categoryId: $request->validated('category_id'),
            title: $request->validated('title'),
            description: $request->validated('description'),
            isAnonymous: $request->validated('is_anonymous', false),
            mediaFiles: $request->file('attachments', [])
        );
    }
}
