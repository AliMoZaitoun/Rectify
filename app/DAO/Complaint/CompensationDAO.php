<?php

namespace App\DAO\Complaint;

use App\Models\Complaint\ComplaintCompensation;

class CompensationDAO
{
    public function store(array $data): ComplaintCompensation
    {
        return ComplaintCompensation::create($data);
    }

    public function byComplaintId(int $complaintId, array $relations = []): ?ComplaintCompensation
    {
        return ComplaintCompensation::with($relations)
            ->where('complaint_id', $complaintId)
            ->first();
    }

    public function byClient(int $clientId, array $relations = [], int $perPage = 15)
    {
        return ComplaintCompensation::with($relations)
            ->where('client_id', $clientId)
            ->latest()
            ->paginate($perPage);
    }

    public function ById(int $id, array $relations = []): ?ComplaintCompensation
    {
        return ComplaintCompensation::with($relations)->find($id);
    }

    public function update(ComplaintCompensation $compensation, array $data): bool
    {
        return $compensation->update($data);
    }

    public function delete(ComplaintCompensation $compensation): bool
    {
        return (bool) $compensation->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15)
    {
        return ComplaintCompensation::with(['complaint', 'client', 'approvedBy'])
            ->when(isset($filters['type']), fn($q) => $q->where('type', $filters['type']))
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->latest()
            ->paginate($perPage);
    }

    public function getPendingPointsCompensationsByClient(int $clientId)
    {
        return ComplaintCompensation::whereHas('complaint', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        })
            ->where('type', \App\Enums\CompensationType::POINTS->value)
            ->where('status', \App\Enums\CompensationStatus::PENDING->value)
            ->get();
    }
}
