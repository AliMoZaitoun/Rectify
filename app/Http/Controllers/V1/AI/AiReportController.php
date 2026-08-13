<?php

namespace App\Http\Controllers\V1\AI;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AI\GenerateAiReportRequest;
use App\Http\Resources\V1\AI\AiReportResource;
use App\Services\AI\AiReportService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class AiReportController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private readonly AiReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $reports = $this->reportService->getAllReports($perPage);

        return $this->successCollection(
            $reports,
            AiReportResource::class,
            __('messages.ai.reports_retrieved')
        );
    }

    public function show(int $id)
    {
        $report = $this->reportService->getReportById($id);

        return $this->useResource(
            $report,
            AiReportResource::class,
            __('messages.ai.report_retrieved')
        );
    }

    public function generate(GenerateAiReportRequest $request)
    {
        $report = $this->reportService->generateReport(
            $request->except('title'),
            $request->validated('title'),
            auth()->id()
        );

        return $this->useResource(
            $report,
            AiReportResource::class,
            __('messages.ai.report_generated')
        );
    }
}
