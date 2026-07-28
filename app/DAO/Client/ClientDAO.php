<?php

namespace App\DAO\Client;

use App\DTOs\Client\Update\UpdateClientDTO;
use App\DTOs\Client\Create\CreateClientDTO;
use App\Exceptions\NotFoundException;
use App\Models\Client;

class ClientDAO
{
    public function index()
    {
        return Client::all();
    }

    public function store(CreateClientDTO $clientDTO)
    {
        $client = Client::create($clientDTO->toArray());
        $client->user->assignRole('client');
        return $client;
    }

    public function show(int $id)
    {
        return Client::where('id', $id)->first();
    }

    public function update(int $id, UpdateClientDTO $clientDTO)
    {
        $client = $this->show($id);
        return $client->update($clientDTO->toArray());
    }

    public function destroy(int $id)
    {
        $client = $this->show($id);
        return $client->user->delete();
    }

    public function incrementPoints(int $clientId, int $points): bool
    {
        return (bool) Client::where('id', $clientId)->increment('points', $points);
    }
}
