<?php

namespace App\DTOs\Core\Create;

class CreateBranchDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public int $location_id,
        public ?float $monthly_amount_budget,
        public ?int $monthly_points_budget,
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            name: $request['name'],
            description: $request['description'] ?? null,
            location_id: $request['location_id'],
            monthly_amount_budget: isset($request['monthly_amount_budget']) ? (float) $request['monthly_amount_budget'] : null,
            monthly_points_budget: isset($request['monthly_points_budget']) ? (int) $request['monthly_points_budget'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'                  => $this->name,
            'description'           => $this->description,
            'location_id'           => $this->location_id,
            'monthly_amount_budget' => $this->monthly_amount_budget,
            'monthly_points_budget' => $this->monthly_points_budget,
        ], fn($value) => !is_null($value));
    }
}
