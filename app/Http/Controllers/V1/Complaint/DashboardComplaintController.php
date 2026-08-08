<?php

namespace App\Http\Controllers\V1\Complaint;

use App\DTOs\Complaint\ChangeComplaintStatusDTO;
use App\DTOs\Complaint\CompensationDTO;
use App\DTOs\Complaint\ComplaintActionDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Complaint\ChangeComplaintStatusRequest;
use App\Http\Requests\V1\Complaint\CreateComplaintActionRequest;
use App\Http\Requests\V1\Complaint\FilterComplaintRequest;
use App\Http\Requests\V1\Complaint\StoreCompensationRequest;
use App\Http\Resources\V1\Complaint\CompensationResource;
use App\Http\Resources\V1\Complaint\DashboardComplaintResource;
use App\Services\Complaint\CompensationService;
use App\Services\Complaint\ComplaintService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardComplaintController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private ComplaintService $service,
        private CompensationService $compensationService
    ) {}

    public function index(FilterComplaintRequest $request)
    {
        $perPage = $request->input('per_page', 15);

        $complaints = $this->service->paginate(
            filters: $request->filters(),
            perPage: (int) $perPage
        );

        return $this->successCollection($complaints, DashboardComplaintResource::class);
    }

    public function show(int $id)
    {
        $complaint = $this->service->findById($id, ['branch', 'category', 'client', 'media', 'actions', 'histories', 'compensation']);

        return $this->useResource($complaint, DashboardComplaintResource::class);
    }

    public function branchComplaints(int $branchId, Request $request)
    {
        $filters = $request->only(['status', 'priority']);
        $complaints = $this->service->branchComplaints($branchId, $filters);

        return $this->successCollection($complaints, DashboardComplaintResource::class);
    }

    public function changeStatus(ChangeComplaintStatusRequest $request, int $id)
    {
        $complaint = $this->service->findById($id);
        $currentEmployee = Auth::user()?->employee;

        $dto = ChangeComplaintStatusDTO::fromRequest($request, $currentEmployee);
        $updatedComplaint = $this->service->changeStatus($complaint, $dto);

        return $this->useResource($updatedComplaint, DashboardComplaintResource::class, __('messages.complaint.status_updated'));
    }

    public function employeeAction(CreateComplaintActionRequest $request, int $id)
    {
        $complaint = $this->service->findById($id);
        $employee = Auth::user()?->employee;

        $dto = ComplaintActionDTO::fromEmployeeRequest($request, $complaint->id, $employee);

        $action = $this->service->addAction(
            complaint: $complaint,
            dto: $dto,
            attachments: $request->file('attachments', [])
        );

        return $this->successResponse($action, __('messages.complaint.action_added'));
    }
}
