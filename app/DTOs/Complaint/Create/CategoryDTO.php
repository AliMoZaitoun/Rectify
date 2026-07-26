<?php

namespace App\DTOs\Complaint\Create;

use App\Http\Requests\V1\Complaint\StoreCategoryRequest;
use App\Http\Requests\V1\Complaint\UpdateCategoryRequest;

class CategoryDTO
{
    public function __construct(
        public ?string $name,
        public ?int $slaHours,
        public ?string $description = null
    ) {}

    public static function fromStoreRequest(StoreCategoryRequest $request): self
    {
        return new self(
            name: (string) $request->validated('name'),
            slaHours: (int) $request->validated('sla_hours'),
            description: $request->validated('description')
        );
    }

    public static function fromUpdateRequest(UpdateCategoryRequest $request): self
    {
        return new self(
            name: $request->validated('name') ?? null,
            slaHours: (int) $request->validated('sla_hours') ?? 0,
            description: $request->validated('description') ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'        => $this->name,
            'sla_hours'   => $this->slaHours,
            'description' => $this->description,
        ], fn($value) => $value !== null);
    }
}
