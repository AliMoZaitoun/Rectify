<?php

namespace App\Services\Complaint;

use App\DAO\Complaint\ComplaintReportDAO;
use App\DTOs\Complaint\ComplaintReportFilterDTO;

class ComplaintReportService
{
    public function __construct(
        protected ComplaintReportDAO $reportDAO
    ) {}

    public function generateReport(ComplaintReportFilterDTO $filter): array
    {
        return [
            'status_summary' => $this->reportDAO->getStatusSummary($filter),
            'sla_performance' => $this->reportDAO->getSlaAndPerformanceStats($filter),
            'csat_satisfaction' => $this->reportDAO->getCsatSummary($filter),
            'top_categories' => $this->reportDAO->getTopCategories($filter),
        ];
    }
}
