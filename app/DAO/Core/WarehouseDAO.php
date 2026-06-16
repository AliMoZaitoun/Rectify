<?php

namespace App\DAO\Core;

use App\DTOs\Core\Update\UpdateWarehouseDTO;
use App\Exceptions\NotFoundException;
use App\Models\Core\Warehouse;

class WarehouseDAO
{
    public function index()
    {
        return Warehouse::with(['items'])->get();
    }

    public function store(array $data)
    {
        return Warehouse::create($data);
    }

    public function show(int $id)
    {
        return Warehouse::where('id', $id)->with(['items'])->first() ?? throw new NotFoundException('Warehouse');
    }

    public function update(int $id, UpdateWarehouseDTO $warehouseDTO)
    {
        $warehouse = $this->show($id);
        $warehouse->update($warehouseDTO->toArray());
        return $warehouse;
    }

    public function destroy(int $id)
    {
        $warehouse = $this->show($id);
        return $warehouse->delete();
    }
}
