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
}
