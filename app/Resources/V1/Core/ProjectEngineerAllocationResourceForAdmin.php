<?php

namespace App\Http\Resources\V1\Core;

use App\Http\Resources\V1\EngineerDetailResource;
use App\Http\Resources\V1\RealEstate\ProjectResource;
use App\Http\Resources\V1\RealEstate\BuildingResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectEngineerAllocationResourceForAdmin extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'is_project_wide' => is_null($this->building_id),

            'project_id'      => $this->project_id,
            'building_id'     => $this->building_id,
            'engineer_id'     => $this->engineer_id,

            'start_date'      => $this->start_date,
            'end_date'        => $this->end_date,
            'created_at'      => $this->created_at?->format('Y-m-d h:i A'),

            'project'         => new ProjectResource($this->whenLoaded('project')),
            'building'        => new BuildingResource($this->whenLoaded('building')),
            'engineer'        => new EngineerDetailResource($this->whenLoaded('engineer')),
        ];
    }
}
