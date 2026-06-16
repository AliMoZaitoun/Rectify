<?php

namespace App\DTOs\Client\Update;

class UpdateClientDTO
{
    public function __construct(
        public ?string $birth_date,
        public ?string $job_title,
        public ?string $social_status,
        public ?string $national_id
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            birth_date: $request['birth_date'] ?? null,
            job_title: $request['job_title'] ?? null,
            social_status: $request['social_status'] ?? null,
            national_id: $request['national_id'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'birth_date'        => new \DateTime($this->birth_date),
            'job_title'         => $this->job_title,
            'social_status'     => $this->social_status,
            'national_id'       => $this->national_id
        ], fn($value) => !is_null($value));
    }
}
