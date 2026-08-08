<?php

namespace App\Http\Controllers\V1\Complaint;

use App\DTOs\Complaint\ComplaintActionDTO;
use App\DTOs\Complaint\Create\CreateComplaintDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Complaint\CreateComplaintActionRequest;
use App\Http\Requests\V1\Complaint\RateComplaintRequest;
use App\Http\Requests\V1\Complaint\ReopenComplaintRequest;
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

        $syncedCount = $this->service->linkAllGuestComplaintsToClient($deviceId, $client->id);

        return $this->successResponse(
            ['synced_count' => $syncedCount],
            __('messages.complaint.synced_successfully')
        );
    }

    public function syncDeviceComplaint(string $code, Request $request)
    {
        $client = Auth::guard('sanctum')->user()?->client;

        $this->service->linkSingleGuestComplaintToClient($code, $client->id);

        return $this->successResponse(
            ['tracking_code' => $code],
            __('messages.complaint.synced_successfully')
        );
    }

    public function rate(int $id, RateComplaintRequest $request)
    {
        $client = Auth::guard('sanctum')->user()?->client;

        $dto = $request->toDTO($id, $client?->id);

        $rating = $this->service->rateComplaint($id, $dto);

        return $this->successResponse(
            $rating,
            __('messages.complaint.rated_successfully')
        );
    }

    public function reopen(int $id, ReopenComplaintRequest $request)
    {
        $user = Auth::guard('sanctum')->user();

        $dto = $request->toDTO(
            complaintId: $id,
            actorId: $user?->id,
            actorType: $user ? get_class($user) : null
        );

        $complaint = $this->service->reopenComplaint($dto);

        return $this->useResource(
            $complaint,
            AppComplaintResource::class,
            __('messages.complaint.reopened_successfully')
        );
    }
}
