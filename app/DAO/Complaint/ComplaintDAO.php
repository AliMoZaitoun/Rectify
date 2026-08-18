<?php

namespace App\DAO\Complaint;

use App\Models\Complaint\Complaint;
use Illuminate\Pagination\LengthAwarePaginator;

class ComplaintDAO
{
    public function paginate(array $filters = [], array $relations = [], int $perPage = 15)
    {
        $defaultRelations = ['branch', 'category', 'client', 'media', 'compensation', 'latestRating', 'parent', 'children.client', 'children.category', 'children.branch'];
        $allRelations = array_merge($defaultRelations, $relations);

        return Complaint::query()
            ->with($allRelations)
            ->whereNull('parent_id')
            ->when(! empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(! empty($filters['priority']), fn($q) => $q->where('priority', $filters['priority']))
            ->when(! empty($filters['category_id']), fn($q) => $q->where('category_id', $filters['category_id']))
            ->when(
                isset($filters['is_spam']),
                fn($q) =>
                $q->where('is_spam', filter_var($filters['is_spam'], FILTER_VALIDATE_BOOLEAN))
            )
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
        return Complaint::with(['branch', 'category', 'compensation', 'media', 'latestRating'])
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
        $defaultRelations = ['branch', 'category', 'client', 'media', 'compensation', 'latestRating', 'parent', 'children.client', 'children.category', 'children.branch'];
        $allRelations = array_merge($defaultRelations, $relations);

        return Complaint::query()
            ->where('branch_id', $branchId)
            ->with($allRelations)
            ->whereNull('parent_id')
            ->when(! empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(! empty($filters['priority']), fn($q) => $q->where('priority', $filters['priority']))
            ->when(! empty($filters['category_id']), fn($q) => $q->where('category_id', $filters['category_id']))
            ->latest()
            ->paginate($perPage);
    }

    public function update(Complaint $complaint, array $data)
    {
        $complaint->update($data);
        return $complaint->refresh();
    }

    public function updateParentId(array $ids, int $parentId): bool
    {
        return Complaint::whereIn('id', $ids)->update([
            'parent_id' => $parentId
        ]);
    }

    public function getFilteredForAiReport(array $filters)
    {
        $query = Complaint::with(['category', 'branch'])->select([
            'id',
            'category_id',
            'branch_id',
            'title',
            'description',
            'status',
            'created_at'
        ]);

        if (!empty($filters['complaint_ids'])) {
            $query->whereIn('id', $filters['complaint_ids']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        return $query->get();
    }
}
