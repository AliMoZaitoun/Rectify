<?php

namespace App\Http\Resources\V1\Complaint;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintRatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'rating'     => $this->rating,
            'comment'    => $this->comment,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
