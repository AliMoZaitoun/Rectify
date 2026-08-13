<?php

namespace App\Http\Resources\V1\AI;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->report_data;

        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'filters'           => $this->filters,
            'created_at'        => $this->created_at?->toIso8601String(),
            'executive_summary' => [
                'ar' => $data['executive_summary_ar'] ?? '',
                'en' => $data['executive_summary_en'] ?? '',
            ],
            'root_causes' => collect($data['root_causes'] ?? [])->map(function ($cause) {
                return [
                    'issue' => [
                        'ar' => $cause['issue_ar'] ?? '',
                        'en' => $cause['issue_en'] ?? '',
                    ],
                    'frequency_analysis' => $cause['frequency_analysis'] ?? '',
                    'impact' => [
                        'ar' => $cause['impact_ar'] ?? '',
                        'en' => $cause['impact_en'] ?? '',
                    ],
                ];
            })->toArray(),
            'recommendations' => collect($data['recommendations'] ?? [])->map(function ($rec) {
                return [
                    'action' => [
                        'ar' => $rec['action_ar'] ?? '',
                        'en' => $rec['action_en'] ?? '',
                    ],
                ];
            })->toArray(),
        ];
    }
}
