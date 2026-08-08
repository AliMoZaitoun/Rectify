<?php

namespace App\DAO\Complaint;

use App\Models\Complaint\Complaint;
use Illuminate\Pagination\LengthAwarePaginator;

class ComplaintDAO
{
    public function paginate(array $filters = [], array $relations = [], int $perPage = 15)
    {
        $defaultRelations = ['branch', 'category', 'client', 'media', 'compensation'];
        $allRelations = array_merge($defaultRelations, $relations);

        return Complaint::query()
            ->with($allRelations)
            ->when(! empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(! empty($filters['priority']), fn($q) => $q->where('priority', $filters['priority']))
            ->when(! empty($filters['category_id']), fn($q) => $q->where('category_id', $filters['category_id']))
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

    public function byClientOrDevice(?int $clientId, ?string $deviceId, int $perPage = 15): LengthAwarePaginator
    {
        return Complaint::with(['branch', 'category', 'compensation', 'media'])
            ->when($clientId, function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->when(!$clientId && $deviceId, function ($query) use ($deviceId) {
                $query->where('device_id', $deviceId)
                    ->whereNull('client_id');
            })
            ->latest()
            ->paginate($perPage);
    }

    public function linkComplaintsAndRevealIdentity(string $deviceId, int $clientId): int
    {
        return Complaint::where(function ($q) use ($deviceId, $clientId) {
            $q->whereNull('client_id')->where('device_id', $deviceId);
        })
            ->orWhere(function ($q) use ($clientId) {
                $q->where('client_id', $clientId)->where('is_anonymous', true);
            })
            ->update([
                'client_id'    => $clientId,
                'is_anonymous' => false
            ]);
    }

    public function byBranch(int $branchId, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        $defaultRelations = ['branch', 'category', 'client', 'media', 'compensation'];
        $allRelations = array_merge($defaultRelations, $relations);

        return Complaint::query()
            ->where('branch_id', $branchId)
            ->with($allRelations)
            ->when(! empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(! empty($filters['priority']), fn($q) => $q->where('priority', $filters['priority']))
            ->when(! empty($filters['category_id']), fn($q) => $q->where('category_id', $filters['category_id']))
            ->latest()
            ->paginate($perPage);
    }

    public function update(Complaint $complaint, array $data): bool
    {
        return $complaint->update($data);
    }
}
