<?php

namespace App\DTOs\Core\Create;

class AssignEmployeeDepartmentDTO
{
    public function __construct(
        public readonly int $employee_id,
        public readonly int $department_id,
        public readonly int $role_id,
        public readonly string $position,
        public readonly string $from_date,
        public readonly ?string $to_date,
    ) {}

    public static function fromRequest(array $request)
    {
        return new self(
            employee_id: $request['employee_id'],
            department_id: $request['department_id'],
            role_id: $request['role_id'],
            position: $request['position'],
            from_date: $request['from_date'],
            to_date: $request['to_date'] ?? null
        );
    }

    public function toArray()
    {
        return array_filter([
            'employee_id' => $this->employee_id,
            'role_id' => $this->role_id,
            'department_id' => $this->department_id,
            'position' => $this->position,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date
        ], fn($value) => !is_null($value));
    }
}
