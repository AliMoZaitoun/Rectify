<?php

namespace App\Http\Resources\V1\Compensation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppCompensationResource extends JsonResource
{
    public function toArray($request): array
    {
        $complaint = $this->relationLoaded('complaint')
            ? $this->complaint
            : $this->whenLoaded('complaint', $this->complaint, null);

        if (! $complaint && isset($this->complaint_id)) {
            $complaint = \App\Models\Complaint\Complaint::find($this->complaint_id);
        }

        $isGuest = is_null($this->client_id);

        $isAnonymous = $complaint ? (bool) $complaint->is_anonymous : false;

        $statusValue = $this->status instanceof \App\Enums\CompensationStatus
            ? $this->status->value
            : $this->status;

        $requiresAction = ($statusValue === 'pending') && ($isGuest || $isAnonymous);

        $authHint = null;
        if ($requiresAction) {
            $authHint = $isGuest
                ? __('messages.complaint.login_to_claim_reward')
                : __('messages.complaint.reveal_identity_to_claim_reward');
        }

        return [
            'id'              => $this->id,
            'type'            => $this->type instanceof \App\Enums\CompensationType ? $this->type->value : $this->type,
            'type_label'      => $this->type instanceof \App\Enums\CompensationType ? $this->type->label() : $this->type,
            'amount'          => (float) $this->amount,
            'coupon_code'     => $this->when(! $isGuest, $this->coupon_code),
            'status'          => $statusValue,
            'status_label'    => $this->status instanceof \App\Enums\CompensationStatus ? $this->status->label() : $this->status,
            'granted_at'      => $this->granted_at,
            'requires_action' => $requiresAction,
            'auth_hint'       => $authHint,
        ];
    }
}
