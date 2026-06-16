<?php

namespace App\DTOs\Core\Update;

class UpdateEmployeeDTO
{
    public function __construct(
        public ?int $user_id = null,
        public ?int $department_id = null,
        public ?string $position = null
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            position: $request['position'] ?? null,
            department_id: $request['department_id'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([], fn($value) => !is_null($value));
    }
}
