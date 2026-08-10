<?php

namespace App\DTOs\Role\Update;

class UpdateRoleDTO
{
    public function __construct(
        public ?string $name,
        public ?string $guard_name,
        public ?array $permissions
    ) {}

    public static function fromRequest(array $request)
    {
        return new self(
            name: $request['name'] ?? null,
            guard_name: $request['guard_name'] ?? null,
            permissions: $request['permissions'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'          => $this->name,
            'guard_name'    => $this->guard_name
        ], fn($v) => !is_null($v));
    }
}
