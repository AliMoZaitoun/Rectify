<?php

namespace App\DTOs\Core\Update;

class UpdateEmployeeDepartmentDTO
{
    public function __construct(
        public readonly ?int $employee_id,
        public readonly ?int $department_id,
        public readonly ?string $position,
        public readonly ?string $from_date,
        public readonly ?string $to_date,
    ) {}

    public static function fromRequest(array $request)
    {
        return new self(
            employee_id: $request['employee_id'] ?? null,
            department_id: $request['department_id'] ?? null,
            position: $request['position'] ?? null,
            from_date: $request['from_date'] ?? null,
            to_date: $request['to_date'] ?? null
        );
    }

    public function toArray()
    {
        return array_filter([
            'employee_id' => $this->employee_id,
            'department_id' => $this->department_id,
            'position' => $this->position,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date
        ], fn($value) => !is_null($value));
    }
}
