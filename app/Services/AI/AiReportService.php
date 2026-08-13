<?php

namespace App\Services\AI;

use App\DAO\Complaint\ComplaintDAO;
use App\DAO\AI\AiReportDAO;
use App\Exceptions\V1\AI\NoComplaintsFoundForAnalysisException;
use App\Exceptions\V1\AI\AiReportNotFoundException;
use App\Models\AI\AiReport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AiReportService
{
    public function __construct(
        private readonly GeminiCoreService $geminiCore,
        private readonly ComplaintDAO $complaintDAO,
        private readonly AiReportDAO $aiReportDAO
    ) {}

    public function generateReport(array $filters, string $title, ?int $userId = null): AiReport
    {
        $complaints = $this->complaintDAO->getFilteredForAiReport($filters);

        if ($complaints->isEmpty()) {
            throw new NoComplaintsFoundForAnalysisException();
        }

        $formattedData = $this->formatComplaintsForAi($complaints);
        $prompt = $this->buildReportPrompt($formattedData);

        $reportData = $this->geminiCore->generateJson($prompt);

        return $this->aiReportDAO->create([
            'title'       => $title,
            'filters'     => $filters,
            'report_data' => $reportData,
            'created_by'  => $userId,
        ]);
    }

    public function getAllReports(int $perPage = 15): LengthAwarePaginator
    {
        return $this->aiReportDAO->getAllPaginated($perPage);
    }

    public function getReportById(int $id): AiReport
    {
        $report = $this->aiReportDAO->findById($id);

        if (!$report) {
            throw new AiReportNotFoundException();
        }

        return $report;
    }

    private function formatComplaintsForAi(Collection $complaints): string
    {
        $data = $complaints->map(function ($complaint) {
            return [
                'id'       => $complaint->id,
                'category' => $complaint->category?->name ?? 'Uncategorized',
                'branch'   => $complaint->branch?->name ?? 'Unknown',
                'issue'    => $complaint->title . ' - ' . $complaint->description,
            ];
        })->toArray();

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private function buildReportPrompt(string $complaintsJson): string
    {
        return "
        You are an expert in institutional organizational analysis and root cause analysis.
        Analyze the following JSON dataset containing customer complaints.
        Identify structural flaws, recurring patterns, and systemic root causes.
        Provide actionable management recommendations to improve operational efficiency.

        Complaints Dataset:
        {$complaintsJson}

        Format your response EXACTLY as the following JSON structure:
        {
            \"executive_summary_ar\": \"A concise summary of the overall situation in Arabic\",
            \"executive_summary_en\": \"A concise summary of the overall situation in English\",
            \"root_causes\": [
                {
                    \"issue_ar\": \"Main problem description in Arabic\",
                    \"issue_en\": \"Main problem description in English\",
                    \"frequency_analysis\": \"High/Medium/Low based on data\",
                    \"impact_ar\": \"Business impact in Arabic\",
                    \"impact_en\": \"Business impact in English\"
                }
            ],
            \"recommendations\": [
                {
                    \"action_ar\": \"Actionable recommendation in Arabic\",
                    \"action_en\": \"Actionable recommendation in English\"
                }
            ]
        }
        ";
    }
}
