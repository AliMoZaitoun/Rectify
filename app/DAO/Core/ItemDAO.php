<?php

namespace App\DAO\Core;

use App\DTOs\Core\Create\CreateItemDTO;
use App\DTOs\Core\Update\UpdateItemDTO;
use App\Exceptions\NotFoundException;
use App\Models\Core\Item;

class ItemDAO
{

    public function index()
    {
        return Item::with('warehouse')->get();
    }

    public function store(CreateItemDTO $itemDTO)
    {
        return Item::create($itemDTO->toArray());
    }

    public function getItemsByWarehouseID(int $warehouse_id)
    {
        return Item::where('warehouse_id', $warehouse_id)->get();
    }

    public function show(int $id)
    {
        return Item::where('id', $id)->with('warehouse')->first() ?? throw new NotFoundException("Item");
    }

    public function update(int $id, UpdateItemDTO $itemDTO)
    {
        $item = $this->show($id);
        $item->update($itemDTO->toArray());
        return $item;
    }

    public function destroy(int $id)
    {
        $item = $this->show($id);
        return $item->delete();
    }
}
