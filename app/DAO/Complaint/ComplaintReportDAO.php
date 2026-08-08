<?php

namespace App\DAO\Complaint;

use App\DTOs\Complaint\ComplaintReportFilterDTO;
use App\Enums\ComplaintStatus;
use App\Models\Complaint\Complaint;
use App\Models\Complaint\ComplaintRating;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ComplaintReportDAO
{
    private function applyFilters(Builder $query, ComplaintReportFilterDTO $filter): Builder
    {
        return $query
            ->when($filter->fromDate, fn($q) => $q->whereDate('created_at', '>=', $filter->fromDate))
            ->when($filter->toDate, fn($q) => $q->whereDate('created_at', '<=', $filter->toDate))
            ->when($filter->branchId, fn($q) => $q->where('branch_id', $filter->branchId));
    }

    public function getStatusSummary(ComplaintReportFilterDTO $filter): array
    {
        $query = Complaint::query();
        $this->applyFilters($query, $filter);

        $results = $query->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'total'       => array_sum($results),
            'pending'     => $results[ComplaintStatus::PENDING->value] ?? 0,
            'in_progress' => $results[ComplaintStatus::IN_PROGRESS->value] ?? 0,
            'resolved'    => $results[ComplaintStatus::RESOLVED->value] ?? 0,
        ];
    }

    public function getSlaAndPerformanceStats(ComplaintReportFilterDTO $filter): array
    {
        $query = Complaint::query()->where('status', ComplaintStatus::RESOLVED->value);
        $this->applyFilters($query, $filter);

        $resolvedComplaints = $query->get(['created_at', 'resolved_at', 'sla_due_at']);

        $totalResolved = $resolvedComplaints->count();
        if ($totalResolved === 0) {
            return [
                'resolved_within_sla' => 0,
                'sla_breached'        => 0,
                'sla_compliance_rate' => 0.0,
                'avg_resolution_hours' => 0.0,
            ];
        }

        $withinSlaCount = 0;
        $totalHours = 0;

        foreach ($resolvedComplaints as $complaint) {
            // فحص الـ SLA
            if ($complaint->sla_due_at && $complaint->resolved_at->lte($complaint->sla_due_at)) {
                $withinSlaCount++;
            }

            $totalHours += $complaint->created_at->diffInHours($complaint->resolved_at);
        }

        return [
            'resolved_within_sla'  => $withinSlaCount,
            'sla_breached'         => $totalResolved - $withinSlaCount,
            'sla_compliance_rate'  => round(($withinSlaCount / $totalResolved) * 100, 2),
            'avg_resolution_hours' => round($totalHours / $totalResolved, 2),
        ];
    }

    public function getCsatSummary(ComplaintReportFilterDTO $filter): array
    {
        $query = ComplaintRating::query();

        if ($filter->fromDate || $filter->toDate || $filter->branchId) {
            $query->whereHas('complaint', function ($q) use ($filter) {
                $this->applyFilters($q, $filter);
            });
        }

        $ratings = $query->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $totalRatings = array_sum($ratings);
        $sumRatings = 0;

        foreach ($ratings as $stars => $count) {
            $sumRatings += ($stars * $count);
        }

        return [
            'total_ratings' => $totalRatings,
            'average_csat'  => $totalRatings > 0 ? round($sumRatings / $totalRatings, 2) : 0.0,
            'breakdown'     => [
                '5_stars' => $ratings[5] ?? 0,
                '4_stars' => $ratings[4] ?? 0,
                '3_stars' => $ratings[3] ?? 0,
                '2_stars' => $ratings[2] ?? 0,
                '1_star'  => $ratings[1] ?? 0,
            ],
        ];
    }

    public function getTopCategories(ComplaintReportFilterDTO $filter, int $limit = 5): array
    {
        $query = Complaint::query();
        $this->applyFilters($query, $filter);

        return $query->select('category_id', DB::raw('count(*) as total'))
            ->with('category:id,name')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'category_id'   => $item->category_id,
                'category_name' => $item->category?->name,
                'total'         => $item->total,
            ])->toArray();
    }
}
