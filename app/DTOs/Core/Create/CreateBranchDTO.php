<?php

namespace App\DTOs\Core\Create;

class CreateBranchDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public int $location_id,
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            name: $request['name'],
            description: $request['description'] ?? null,
            location_id: $request['location_id'],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'           => $this->name,
            'description'    => $this->description,
            'location_id'    => $this->location_id,
        ], fn($value) => !is_null($value));
    }
}
