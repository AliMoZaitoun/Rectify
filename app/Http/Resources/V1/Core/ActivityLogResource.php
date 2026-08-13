<?php

namespace App\Http\Resources\V1\Core;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'log_name'     => $this->log_name,
            'description'  => $this->description,
            'event'        => $this->event,
            'subject_type' => class_basename($this->subject_type),
            'subject_id'   => $this->subject_id,
            'causer_id'    => $this->causer_id,
            'causer_name'  => $this->causer ? ($this->causer->full_name ?? $this->causer->first_name ?? 'System') : 'System',
            'properties'   => $this->properties,
            'created_at'   => $this->created_at->format('Y-m-d h:i A'),
        ];
    }
}
