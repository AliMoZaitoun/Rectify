<?php

namespace App\Http\Controllers\V1\Complaint;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Complaint\GetComplaintReportRequest;
use App\Services\Complaint\ComplaintReportService;
use App\Traits\ResponseTrait;

class ComplaintReportController extends Controller
{
    use ResponseTrait;
    public function __construct(
        protected ComplaintReportService $reportService
    ) {}

    public function index(GetComplaintReportRequest $request)
    {
        $reportData = $this->reportService->generateReport($request->toDTO());

        return $this->successResponse(
            $reportData,
            __('messages.complaint.report_generated_successfully')
        );
    }
}
