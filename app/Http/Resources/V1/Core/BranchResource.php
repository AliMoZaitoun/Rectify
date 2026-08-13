<?php

namespace App\Http\Resources\V1\Core;

use App\Http\Resources\V1\LocationResource;
use App\Models\Complaint\ComplaintCompensation;
use App\Enums\CompensationStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $spentAmount = ComplaintCompensation::where('branch_id', $this->id)
            ->whereIn('status', [CompensationStatus::GRANTED->value, CompensationStatus::PENDING_APPROVAL->value])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'description'           => $this->description,
            'location_id'           => $this->location_id,
            'monthly_amount_budget' => $this->monthly_amount_budget ? (float) $this->monthly_amount_budget : 0.00,
            'monthly_points_budget' => $this->monthly_points_budget ? (int) $this->monthly_points_budget : 0,
            'spent_amount'          => (float) $spentAmount,
            'remaining_amount'      => max(0, ($this->monthly_points_budget ?? 0) - $spentAmount),
            'location'              => new LocationResource($this->whenLoaded('location')),
            'employees'             => EmployeeBranchResource::collection($this->whenLoaded('employees')),
            'created_at'            => $this->created_at->format('Y-m-d h:i A'),
        ];
    }
}
