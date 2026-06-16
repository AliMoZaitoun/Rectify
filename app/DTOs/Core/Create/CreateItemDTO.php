<?php

namespace App\DTOs\Core\Create;

class CreateItemDTO
{
    public function __construct(
        public int $warehouseID,
        public string $sku,
        public string $name,
        public ?string $description,
        public int $quantity,
        public string $status,
        public ?string $expiry_date,
        public string $purchase_date,
        public string $received_date,
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            warehouseID: $request['warehouse_id'],
            sku: $request['sku'],
            name: $request['name'],
            description: $request['description'] ?? null,
            quantity: $request['quantity'],
            status: $request['status'],
            expiry_date: $request['expiry_date'] ?? null,
            purchase_date: $request['purchase_date'],
            received_date: $request['received_date']
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'warehouse_id'   => $this->warehouseID,
            'sku'            => $this->sku,
            'name'           => $this->name,
            'description'    => $this->description,
            'quantity'       => $this->quantity,
            'status'         => $this->status,
            'expiry_date'    => $this->expiry_date ? new \DateTime($this->expiry_date) : null,
            'purchase_date'  => new \DateTime($this->purchase_date),
            'received_date'  => new \DateTime($this->received_date),
        ], fn($value) => !is_null($value));
    }
}
