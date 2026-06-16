<?php

namespace App\DAO\Client;

use App\DTOs\Client\Create\CreateFavoriteDTO;
use App\Exceptions\NotFoundException;
use App\Models\Client\Favorite;

class FavoriteDAO
{
    public function index(int $client_id)
    {
        return Favorite::where('client_id', $client_id)->with(['unit', 'unit.attachments'])->get();
    }

    public function store(CreateFavoriteDTO $favoriteDTO)
    {
        return Favorite::create($favoriteDTO->toArray());
    }

    public function exists(int $client_id, int $unit_id): bool
    {
        return Favorite::where('client_id', $client_id)
            ->where('unit_id', $unit_id)
            ->exists();
    }

    public function show(int $id)
    {
        return Favorite::find($id) ?? throw new NotFoundException("Favorite");
    }

    public function showByUnitId(int $unit_id)
    {
        return Favorite::where('unit_id', $unit_id)->first() ?? throw new NotFoundException("Favorite");
    }

    public function destroy(int $unit_id)
    {
        $Favorite = $this->showByUnitId($unit_id);
        return $Favorite->delete();
    }
}
