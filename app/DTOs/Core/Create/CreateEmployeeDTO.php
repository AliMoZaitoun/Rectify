<?php

namespace App\DTOs\Core\Create;

class CreateEmployeeDTO
{
    public function __construct(
        public ?int $id,
        public ?int $user_id,
        public ?int $department_id,
        public ?string $position,
        public ?string $from_date,
        public ?string $to_date,
        public ?int $role_id,

    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            id: null,
            user_id: null,
            department_id: $request['department_id'] ?? null,
            position: $request['position'] ?? null,
            from_date: $request['from_date'] ?? null,
            to_date: $request['to_date'] ?? null,
            role_id: $request['role_id'] ?? null,
        );
    }

    public function toArray()
    {
        return array_filter([
            'user_id'  => $this->user_id
        ], fn($value) => !is_null($value));
    }

    public function getDepInfo()
    {
        return array_filter([
            'department_id'  => $this->department_id,
            'role_id'        => $this->role_id,
            'position'       => $this->position,
            'from_date'      => $this->from_date,
            'to_date'        => $this->to_date,
        ], fn($value) => !is_null($value));
    }
}
