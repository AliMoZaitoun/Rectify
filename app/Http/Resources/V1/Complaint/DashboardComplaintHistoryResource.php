<?php

namespace App\Http\Resources\V1\Complaint;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;

class DashboardComplaintHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'old_status'        => $this->old_status ? __("labels.complaint_status.{$this->old_status}") : null,
            'new_status'        => $this->new_status ? __("labels.complaint_status.{$this->new_status}") : null,
            'duration_in_hours' => $this->duration_in_hours,

            'comment'           => $this->comment,

            'comment_label'     => $this->translateComment($this->comment),

            'created_at'        => $this->created_at?->toIso8601String(),
            'is_visible'        => $this->is_visible,

            'changed_by'  => $this->whenLoaded('changedBy', function () {
                return [
                    'id'   => $this->changedBy->id,
                    'name' => $this->changedBy->full_name ?? $this->changedBy->name ?? trim($this->changedBy->first_name . ' ' . $this->changedBy->last_name) ?: 'System',
                    'type' => class_basename($this->changed_by_type),
                ];
            }),
        ];
    }

    protected function translateComment(?string $comment): ?string
    {
        if (!$comment) return null;

        $actionKey = "labels.action_types.{$comment}";
        if (Lang::has($actionKey)) {
            return __($actionKey);
        }

        if (Str::startsWith($comment, 'complaint.history.')) {

            if (Str::contains($comment, '|')) {
                [$key, $param] = explode('|', $comment);
                return __("messages.{$key}", ['tracking_code' => $param]);
            }

            return __("messages.{$comment}");
        }

        return $comment;
    }
}
