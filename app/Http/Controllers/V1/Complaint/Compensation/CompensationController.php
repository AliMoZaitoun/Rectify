<?php

namespace App\Http\Controllers\V1\Complaint\Compensation;

use App\DTOs\Complaint\CompensationDTO;
use App\Enums\CompensationStatus;
use App\Exceptions\V1\Core\EmployeeRequiredException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Complaint\StoreCompensationRequest;
use App\Http\Resources\V1\Compensation\DashboardCompensationResource;
use App\Services\Complaint\CompensationService;
use App\Services\Complaint\ComplaintService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompensationController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private CompensationService $compensationService,
        private ComplaintService $complaintService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->only(['type', 'status', 'branch_id']);

        if ($user->hasRole('manager')) {
            $filters['branch_id'] = $user->employee?->branch_id;

            if (isset($filters['status']) && $filters['status'] === CompensationStatus::PENDING_APPROVAL->value) {
                $filters['requested_by_role'] = 'staff';
            }
        } elseif ($user->hasRole('admin')) {
            if (isset($filters['status']) && $filters['status'] === CompensationStatus::PENDING_APPROVAL->value) {
                $filters['requested_by_role'] = 'manager';
            }
        }

        $compensations = $this->compensationService->getAll(
            filters: $filters,
            perPage: $request->integer('per_page', 15)
        );

        return $this->successCollection($compensations, DashboardCompensationResource::class);
    }

    public function store(StoreCompensationRequest $request, int $complaintId)
    {
        $complaint = $this->complaintService->findById($complaintId);
        $employee = Auth::user()?->employee;

        $dto = CompensationDTO::fromRequest(
            request: $request,
            complaintId: $complaintId,
            clientId: $complaint?->client_id,
            employeeId: $employee?->id
        );

        $compensation = $this->compensationService->compensate($dto);

        return $this->useResource($compensation, DashboardCompensationResource::class, __('messages.common.stored'));
    }

    public function showByComplaint(int $complaintId)
    {
        $compensation = $this->compensationService->getCompensationForComplaint($complaintId, ['approvedBy', 'client']);

        return $this->useResource($compensation, DashboardCompensationResource::class);
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $status = CompensationStatus::from($request->status);
        $compensation = $this->compensationService->updateStatus($id, $status);

        return $this->useResource($compensation, DashboardCompensationResource::class, __('messages.common.updated'));
    }

    public function redeemCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => ['required', 'string'],
        ]);

        $employee = Auth::user()?->employee;

        if (!$employee) {
            throw new EmployeeRequiredException();
        }

        $compensation = $this->compensationService->redeemCoupon($request->coupon_code, $employee->id);

        return $this->useResource(
            $compensation,
            DashboardCompensationResource::class,
            __('messages.compensations.redeemed_successfully')
        );
    }
}
