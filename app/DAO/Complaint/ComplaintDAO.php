<?php

namespace App\DAO\Complaint;

use App\Models\Complaint\Complaint;
use Illuminate\Pagination\LengthAwarePaginator;

class ComplaintDAO
{
    public function paginate(array $relations = [], int $perPage = 15): LengthAwarePaginator
    {
        $defaultRelations = ['branch', 'category', 'client', 'media'];
        $allRelations = array_merge($defaultRelations, $relations);

        return Complaint::query()
            ->with($allRelations)
            ->latest()
            ->paginate($perPage);
    }

    public function store(array $data): Complaint
    {
        return Complaint::create($data);
    }

    public function byId(int $id, array $relations = []): ?Complaint
    {
        return Complaint::with($relations)->find($id);
    }

    public function byTrackingCode(string $code, array $relations = []): ?Complaint
    {
        return Complaint::with($relations)
            ->where('tracking_code', $code)
            ->first();
    }

    public function byTrackingToken(string $token, array $relations = []): ?Complaint
    {
        return Complaint::with($relations)->where('tracking_token', $token)->first();
    }

    public function byClient(int $clientId, int $perPage = 15): LengthAwarePaginator
    {
        return Complaint::with(['branch', 'category'])
            ->where('client_id', $clientId)
            ->latest()
            ->paginate($perPage);
    }

    public function byBranch(int $branchId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Complaint::with(['category', 'client'])
            ->where('branch_id', $branchId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function update(Complaint $complaint, array $data): bool
    {
        return $complaint->update($data);
    }
}
