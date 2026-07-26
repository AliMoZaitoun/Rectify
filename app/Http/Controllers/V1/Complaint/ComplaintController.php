<?php

namespace App\Http\Controllers\V1\Complaint;

use App\DTOs\Complaint\ChangeComplaintStatusDTO;
use App\DTOs\Complaint\ComplaintActionDTO;
use App\DTOs\Complaint\Create\CreateComplaintDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Complaint\ChangeComplaintStatusRequest;
use App\Http\Requests\V1\Complaint\CreateComplaintActionRequest;
use App\Http\Requests\V1\Complaint\StoreComplaintRequest;
use App\Http\Resources\V1\Complaint\ComplaintResource;
use App\Services\Complaint\ComplaintService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private ComplaintService $service
    ) {}

    public function index()
    {
        $complaints = $this->service->paginate();
        return $this->successCollection($complaints, ComplaintResource::class);
    }

    public function store(StoreComplaintRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        $clientId = $user?->client?->id;

        $dto = CreateComplaintDTO::fromRequest($request, $clientId);
        $complaint = $this->service->createComplaint($dto, $request->file('attachments', []));

        return $this->useResource($complaint, ComplaintResource::class, __('messages.common.stored'));
    }

    public function show(int $id)
    {
        $complaint = $this->service->findById($id, ['branch', 'category', 'client', 'media', 'actions', 'histories']);

        return $this->useResource($complaint, ComplaintResource::class);
    }

    public function trackByCode(string $code)
    {
        $complaint = $this->service->findByTrackingCode($code, ['branch', 'category', 'media', 'actions', 'histories']);

        return $this->useResource($complaint, ComplaintResource::class);
    }

    public function myComplaints()
    {
        $client = Auth::user()?->client;

        $complaints = $this->service->clientComplaints($client->id);
        return $this->successCollection($complaints, ComplaintResource::class);
    }

    public function branchComplaints(int $branchId, Request $request)
    {
        $filters = $request->only(['status', 'priority']);
        $complaints = $this->service->branchComplaints($branchId, $filters);

        return $this->successCollection($complaints, ComplaintResource::class);
    }

    public function changeStatus(ChangeComplaintStatusRequest $request, int $id)
    {
        $complaint = $this->service->findById($id);

        $currentEmployee = Auth::user()?->employee;

        $dto = ChangeComplaintStatusDTO::fromRequest($request, $currentEmployee);

        $updatedComplaint = $this->service->changeStatus($complaint, $dto);

        return $this->useResource($updatedComplaint, ComplaintResource::class, __('messages.complaint.status_updated'));
    }

    public function track(string $token)
    {
        $complaint = $this->service->trackByToken($token);

        return $this->useResource($complaint, ComplaintResource::class);
    }

    public function clientReply(CreateComplaintActionRequest $request, string $token)
    {
        $complaint = $this->service->trackByToken($token);

        $client = Auth::guard('sanctum')->user()?->client;

        $dto = ComplaintActionDTO::fromClientRequest($request, $complaint->id, $client);

        $action = $this->service->addAction(
            complaint: $complaint,
            dto: $dto,
            attachments: $request->file('attachments', [])
        );

        return $this->successResponse($action, __('messages.complaint.action_added'));
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
