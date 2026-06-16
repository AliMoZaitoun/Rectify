<?php

namespace App\DTOs\Core\Update;

class UpdateItemDTO
{
    public function __construct(
        public ?int $warehouse_id = null,
        public ?string $sku = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?int $quantity = null,
        public ?string $status = null,
        public ?string $expiry_date = null,
        public ?string $purchase_date = null,
        public ?string $received_date = null,
    ) {}


    public static function fromRequest(array $request): self
    {
        return new self(
            warehouse_id: $request['warehouse_id:'] ?? null,
            sku: $request['sku'] ?? null,
            name: $request['name'] ?? null,
            description: $request['description'] ?? null,
            quantity: $request['quantity'] ?? null,
            status: $request['status'] ?? null,
            expiry_date: $request['expiry_date'] ?? null,
            purchase_date: $request['purchase_date'] ?? null,
            received_date: $request['received_date'] ?? null
        );
    }
    public function toArray(): array
    {
        return array_filter([
            'warehouse_id'  => $this->warehouse_id,
            'sku'           => $this->sku,
            'name'          => $this->name,
            'description'   => $this->description,
            'quantity'      => $this->quantity,
            'status'        => $this->status,
            'expiry_date'   => $this->expiry_date,
            'purchase_date' => $this->purchase_date,
            'received_date' => $this->received_date,
        ], fn($value) => !is_null($value));
    }
}
