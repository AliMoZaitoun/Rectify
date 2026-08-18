<?php

namespace App\DAO\Client;

use App\DTOs\Client\Update\UpdateClientDTO;
use App\DTOs\Client\Create\CreateClientDTO;
use App\Models\Client;

class ClientDAO
{
    public function index(array $relations = [], int $perPage = 15)
    {
        $defaultRelations = ['user'];
        $allRelations = array_merge($defaultRelations, $relations);

        return Client::query()
            ->with($allRelations)
            ->withCount('complaints')
            ->latest()
            ->paginate($perPage);
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

    public function incrementPoints(int $clientId, int $points)
    {
        $client = $this->show($clientId);

        $client->points += $points;

        $client->save();

        return $client;
    }

    public function decrementPoints(int $clientId, int $points)
    {
        $client = $this->show($clientId);

        $client->points -= $points;

        $client->save();

        return $client;
    }

    public function getAtRiskClients(string $sinceDate)
    {
        return Client::with(['user'])
            ->withCount([
                'complaints as recent_complaints_count' => function ($query) use ($sinceDate) {
                    $query->where('created_at', '>=', $sinceDate);
                },
                'compensations as rejected_compensations_count' => function ($query) use ($sinceDate) {
                    $query->where('status', 'rejected')
                        ->where('created_at', '>=', $sinceDate);
                }
            ])
            ->where(function ($query) use ($sinceDate) {
                $query->whereHas('complaints', function ($q) use ($sinceDate) {
                    $q->where('created_at', '>=', $sinceDate);
                }, '>=', 3)
                    ->orWhereHas('compensations', function ($q) use ($sinceDate) {
                        $q->where('status', 'rejected')
                            ->where('created_at', '>=', $sinceDate);
                    }, '>', 0);
            })->whereDoesntHave('compensations', function ($q) use ($sinceDate) {
                $q->where('status', 'granted')
                    ->where('created_at', '>=', $sinceDate);
            })
            ->get();
    }
}
