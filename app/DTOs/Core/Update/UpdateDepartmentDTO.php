<?php

namespace App\DTOs\Core\Update;

class UpdateDepartmentDTO
{
    public function __construct(
        public ?string $name,
        public ?string $description,
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            name: $request['name'] ?? null,
            description: $request['description'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'           => $this->name,
            'description'    => $this->description,
        ], fn($value) => !is_null($value));
    }
}
