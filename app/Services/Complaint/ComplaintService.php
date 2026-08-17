<?php

namespace App\Services\Complaint;

use App\DAO\Complaint\CategoryDAO;
use App\DAO\Complaint\ComplaintDAO;
use App\DAO\Complaint\ComplaintHistoryDAO;
use App\DTOs\Complaint\ChangeComplaintStatusDTO;
use App\DTOs\Complaint\ComplaintActionDTO;
use App\DTOs\Complaint\Create\ComplaintHistoryDTO;
use App\DTOs\Complaint\Create\CreateComplaintDTO;
use App\DTOs\Complaint\RateComplaintDTO;
use App\DTOs\Complaint\ReopenComplaintDTO;
use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Exceptions\V1\Complaint\ComplaintNotFoundException;
use App\Jobs\ProcessComplaintAiAnalysis;
use App\Models\Complaint\Complaint;
use App\Services\AI\ComplaintAiService;
use App\Services\FileManagerService;
use App\Services\TransactionService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ComplaintService
{
    public function __construct(
        protected ComplaintDAO $complaintDAO,
        protected CategoryDAO $categoryDAO,
        protected ComplaintHistoryDAO $historyDAO,
        protected TransactionService $transaction,
        protected FileManagerService $fileManager,
        protected ComplaintLifecycleService $lifecycleService,
        protected ComplaintActionService $actionService,
        protected ComplaintMergeService $mergeService,
        protected ComplaintGuestLinkService $guestLinkService,
        private ComplaintAiService $aiService
    ) {}

    public function paginate(array $filters = [], array $relations = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->complaintDAO->paginate($filters, $relations, $perPage);
    }

    public function findById(int $id, array $relations = []): Complaint
    {
        $complaint = $this->complaintDAO->byId($id, $relations);
        if (! $complaint) {
            throw new ComplaintNotFoundException();
        }

        return $complaint;
    }

    public function trackByCode(string $code, array $relations = []): Complaint
    {
        $complaint = $this->complaintDAO->byTrackingCode($code, $relations);
        if (! $complaint) {
            throw new ComplaintNotFoundException();
        }

        return $complaint;
    }

    public function clientComplaints(?int $clientId, ?string $deviceId = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->complaintDAO->byClientOrDevice($clientId, $deviceId, $perPage);
    }

    public function branchComplaints(int $branchId, array $filters = [], int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        return $this->complaintDAO->byBranch($branchId, $filters, $perPage, $relations);
    }

    public function createComplaint(CreateComplaintDTO $dto, array $attachments = []): Complaint
    {
        return $this->transaction->execute(function () use ($dto, $attachments) {

            $category = $dto->categoryId ? $this->categoryDAO->byId($dto->categoryId) : null;
            $slaHours = $category ? $category->sla_hours : 24;

            $complaint = $this->complaintDAO->store([
                'client_id'             => $dto->clientId,
                'device_id'             => $dto->device_id,
                'uuid'                  => $dto->uuid,
                'branch_id'             => $dto->branchId,
                'category_id'           => $dto->categoryId,
                'title'                 => $dto->title,
                'description'           => $dto->description,
                'priority'              => 'medium',
                'is_anonymous'          => $dto->isAnonymous,
                'tracking_code'         => 'CMP-' . strtoupper(Str::random(8)),
                'sla_due_at'            => now()->addHours($slaHours),
                'status'                => ComplaintStatus::PENDING->value,
            ]);

            $this->historyDAO->store(new ComplaintHistoryDTO(
                complaintId: $complaint->id,
                newStatus: ComplaintStatus::PENDING->value,
                changedByType: $dto->clientId ? 'App\Models\Client' : null,
                changedById: $dto->clientId,
                comment: 'complaint_submitted'
            ));

            if (! empty($attachments)) {
                $this->fileManager->storeFile(
                    model: $complaint,
                    files: $attachments,
                    folderPath: 'complaints',
                    relationName: 'media'
                );
            }

            ProcessComplaintAiAnalysis::dispatch(
                $complaint->id,
                $complaint->title,
                $complaint->description
            )->afterResponse();

            return $complaint;
        });
    }

    public function changeStatus(Complaint $complaint, ChangeComplaintStatusDTO $dto): Complaint
    {
        return $this->lifecycleService->changeStatus($complaint, $dto);
    }

    public function addAction(Complaint $complaint, ComplaintActionDTO $dto, array $attachments = [])
    {
        return $this->actionService->addAction($complaint, $dto, $attachments);
    }

    public function mergeComplaints(Complaint $parentComplaint, array $childIds, $employee = null): void
    {
        $this->mergeService->mergeComplaints($parentComplaint, $childIds, $employee);
    }

    public function unmergeComplaint(int $childId, $employee = null): Complaint
    {
        $child = $this->findById($childId);
        return $this->mergeService->unmergeComplaint($child, $employee);
    }

    public function rateComplaint(string $code, RateComplaintDTO $dto)
    {
        $complaint = $this->trackByCode($code);
        return $this->lifecycleService->rateComplaint($complaint, $dto);
    }

    public function reopenComplaint(ReopenComplaintDTO $dto): Complaint
    {
        $complaint = $this->trackByCode($dto->complaintCode);
        return $this->lifecycleService->reopenComplaint($complaint, $dto);
    }

    public function linkAllGuestComplaintsToClient(?string $deviceId, int $clientId): int
    {
        return $this->guestLinkService->linkAllGuestComplaintsToClient($deviceId, $clientId);
    }

    public function linkSingleGuestComplaintToClient(string $code, int $clientId): bool
    {
        return $this->guestLinkService->linkSingleGuestComplaintToClient($code, $clientId);
    }
}
