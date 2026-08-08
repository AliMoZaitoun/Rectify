<?php

namespace App\DAO\Complaint;

use App\Models\Complaint\ComplaintRating;

class ComplaintRatingDAO
{
    public function store(array $data): ComplaintRating
    {
        return ComplaintRating::create($data);
    }

    public function byComplaintId(int $complaintId)
    {
        return ComplaintRating::where('complaint_id', $complaintId)->latest()->get();
    }

    public function latestByComplaintId(int $complaintId): ?ComplaintRating
    {
        return ComplaintRating::where('complaint_id', $complaintId)->latest()->first();
    }
}
