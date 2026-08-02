<?php

namespace App\Http\Controllers\V1\Complaint;

use App\DTOs\Complaint\ComplaintActionDTO;
use App\DTOs\Complaint\Create\CreateComplaintDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Complaint\CreateComplaintActionRequest;
use App\Http\Requests\V1\Complaint\StoreComplaintRequest;
use App\Http\Resources\V1\Compensation\AppCompensationResource;
use App\Http\Resources\V1\Complaint\AppComplaintResource;
use App\Services\Complaint\CompensationService;
use App\Services\Complaint\ComplaintService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppComplaintController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private ComplaintService $service,
        private CompensationService $compensationService
    ) {}

    public function store(StoreComplaintRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        $clientId = $user?->client?->id;

        $dto = CreateComplaintDTO::fromRequest($request, $clientId);
        $complaint = $this->service->createComplaint($dto, $request->file('attachments', []));

        return $this->useResource($complaint, AppComplaintResource::class, __('messages.common.stored'));
    }

    public function myComplaints(Request $request)
    {
        $clientId = Auth::guard('sanctum')->user()?->client?->id;
        $deviceId = $request->header('X-Device-ID') ?? $request->input('device_id');

        $complaints = $this->service->clientComplaints($clientId, $deviceId);

        return $this->successCollection($complaints, AppComplaintResource::class);
    }

    public function track(string $code)
    {
        $complaint = $this->service->trackByCode($code, ['branch', 'category', 'media', 'actions', 'compensation']);

        return $this->useResource($complaint, AppComplaintResource::class);
    }

    public function clientReply(CreateComplaintActionRequest $request, string $code)
    {
        $complaint = $this->service->trackByCode($code);

        $client = Auth::guard('sanctum')->user()?->client;

        $dto = ComplaintActionDTO::fromClientRequest($request, $complaint->id, $client);

        $action = $this->service->addAction(
            complaint: $complaint,
            dto: $dto,
            attachments: $request->file('attachments', [])
        );

        return $this->successResponse($action, __('messages.complaint.action_added'));
    }

    public function myCompensations()
    {
        $client = Auth::user()->client;

        $compensations = $this->compensationService->getByClient($client->id, ['complaint']);

        return $this->successCollection($compensations, AppCompensationResource::class);
    }

    public function syncDeviceComplaints(Request $request)
    {
        $client = Auth::guard('sanctum')->user()?->client;

        $deviceId = $request->header('X-Device-ID') ?? $request->input('device_id');

        $syncedCount = $this->service->linkGuestComplaintsToClient($deviceId, $client->id);

        return $this->successResponse(['synced_count' => $syncedCount], __('messages.complaint.synced_successfully'));
    }
}
