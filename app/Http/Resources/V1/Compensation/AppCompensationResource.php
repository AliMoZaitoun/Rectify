<?php

namespace App\Http\Resources\V1\Compensation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppCompensationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $complaint = $this->whenLoaded('complaint', fn() => $this->complaint);
        $isAnonymous = $complaint ? ((bool) $complaint->is_anonymous || is_null($complaint->client_id)) : is_null($this->client_id);

        return [
            'id'            => $this->id,
            'type'          => $this->type?->value,
            'type_label'    => $this->type?->label(),
            'amount'        => (float) $this->amount,
            'coupon_code'   => $this->when(! $isAnonymous && $this->coupon_code, $this->coupon_code),
            'status'        => $this->status?->value,
            'status_label'  => $this->status?->label(),
            'granted_at'    => $this->granted_at?->toIso8601String(),

            'requires_auth' => $isAnonymous,
            'auth_hint'     => $isAnonymous
                ? __('messages.complaint.login_to_claim_reward')
                : null,
        ];
    }
}
