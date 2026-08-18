<?php

namespace App\Services\Complaint;

use App\DAO\Client\ClientDAO;
use App\DAO\Complaint\CompensationDAO;
use App\DTOs\Complaint\CompensationDTO;
use App\DAO\Complaint\ComplaintDAO;
use App\DAO\Core\SettingDAO;
use App\Enums\ComplaintStatus;
use App\Enums\CompensationStatus;
use App\Enums\CompensationType;
use App\Exceptions\V1\Complaint\CannotModifyMergedComplaintException;
use App\Exceptions\V1\Complaint\CompensationNotFoundException;
use App\Exceptions\V1\Complaint\ComplaintAlreadyCompensatedException;
use App\Exceptions\V1\Complaint\CouponAlreadyRedeemedException;
use App\Exceptions\V1\Complaint\CouponNotGrantedException;
use App\Exceptions\V1\Complaint\InvalidCouponException;
use App\Exceptions\V1\Complaint\UnresolvedComplaintCompensationException;
use App\Models\Complaint\ComplaintCompensation;
use App\Models\Core\Employee;
use App\Models\Core\Branch;
use App\Services\Transaction;
use Carbon\Carbon;

class CompensationService
{
    public function __construct(
        private CompensationDAO $compensationDAO,
        private ClientDAO $clientDAO,
        private ComplaintDAO $complaintDAO,
        private SettingDAO $settingDAO,
        private Transaction $transaction
    ) {}

    public function getAll(array $filters = [], int $perPage = 15)
    {
        return $this->compensationDAO->paginate($filters, $perPage);
    }

    public function compensate(CompensationDTO $dto): ComplaintCompensation
    {
        return $this->transaction->execute(function () use ($dto) {

            $complaint = null;
            $branchId = null;
            $isAnonymous = false;
            $existing = null;

            if ($dto->complaintId) {
                $complaint = $this->complaintDAO->byId($dto->complaintId);

                if ($complaint->status !== ComplaintStatus::RESOLVED->value && $complaint->status !== ComplaintStatus::RESOLVED) {
                    throw new UnresolvedComplaintCompensationException();
                }

                if ($complaint->parent_id !== null) {
                    throw new CannotModifyMergedComplaintException();
                }

                $existing = $this->compensationDAO->byComplaintId($dto->complaintId);

                if ($existing && $existing->status !== CompensationStatus::REJECTED->value) {
                    throw new ComplaintAlreadyCompensatedException();
                }

                $branchId = $complaint->branch_id;
                $isAnonymous = (bool) $complaint->is_anonymous || is_null($dto->clientId);
            } else {
                if (is_null($dto->clientId)) {
                    throw new \InvalidArgumentException('يجب تحديد العميل في حال كان التعويض غير مرتبط بشكوى.');
                }

                $employee = Employee::with('currentBranch')->find($dto->approvedById);
                $branchId = $employee?->currentBranch?->branch_id;

                $isAnonymous = false;
            }

            $employeeLimit = $this->getEmployeeCompensationLimit($dto->approvedById);
            $exceedsPersonalLimit = $dto->amount > $employeeLimit;

            $exceedsBranchBudget = false;
            if ($branchId) {
                $exceedsBranchBudget = $this->checkBranchBudgetExceeded($branchId, $dto->type, $dto->amount);
            }

            $requiresApproval = $exceedsPersonalLimit || $exceedsBranchBudget;
            $isPoints = ($dto->type === CompensationType::POINTS->value || $dto->type === CompensationType::POINTS) && $dto->amount > 0;

            if ($requiresApproval) {
                $status = CompensationStatus::PENDING_APPROVAL;
            } elseif ($isAnonymous && $isPoints) {
                $status = CompensationStatus::PENDING;
            } else {
                $status = CompensationStatus::GRANTED;
            }

            $grantedAt = ($status === CompensationStatus::GRANTED) ? now() : null;

            $compensationData = [
                'complaint_id'   => $dto->complaintId,
                'branch_id'      => $branchId,
                'client_id'      => $dto->clientId,
                'approved_by_id' => $dto->approvedById,
                'type'           => $dto->type instanceof CompensationType ? $dto->type->value : $dto->type,
                'amount'         => $dto->amount,
                'coupon_code'    => $dto->couponCode,
                'notes'          => $dto->notes,
                'status'         => $status instanceof CompensationStatus ? $status->value : $status,
                'granted_at'     => $grantedAt,
            ];

            if ($existing) {
                $this->compensationDAO->update($existing, $compensationData);
                $compensation = $existing->fresh();
            } else {
                $compensation = $this->compensationDAO->store($compensationData);
            }

            if ($status === CompensationStatus::GRANTED && $isPoints && $dto->clientId) {
                $this->clientDAO->incrementPoints($dto->clientId, (int) $dto->amount);
            }

            return $compensation;
        });
    }
    public function updateStatus(int $compensationId, CompensationStatus $newStatus): ComplaintCompensation
    {
        return $this->transaction->execute(function () use ($compensationId, $newStatus) {
            $compensation = $this->compensationDAO->ById($compensationId);

            if (! $compensation) {
                throw new CompensationNotFoundException();
            }

            $complaint = $this->complaintDAO->byId($compensation->complaint_id);

            $isAnonymous = (bool) $complaint->is_anonymous || is_null($compensation->client_id);

            $isPoints = ($compensation->type === CompensationType::POINTS->value || $compensation->type === CompensationType::POINTS);

            $finalStatus = $newStatus;

            if ($newStatus === CompensationStatus::GRANTED && $isAnonymous && $isPoints) {
                $finalStatus = CompensationStatus::PENDING;
            }

            if ($finalStatus === CompensationStatus::GRANTED && $compensation->status !== CompensationStatus::GRANTED->value) {
                if ($isPoints && !$isAnonymous) {
                    $this->clientDAO->incrementPoints($compensation->client_id, (int) $compensation->amount);
                }
                $compensation->granted_at = now();
            }

            if ($finalStatus === CompensationStatus::REJECTED && $compensation->status === CompensationStatus::GRANTED->value) {
                if ($isPoints && !$isAnonymous) {
                    $this->clientDAO->decrementPoints($compensation->client_id, (int) $compensation->amount);
                }
                $compensation->granted_at = null;
            }

            $compensation->status = $finalStatus->value;
            $this->compensationDAO->update($compensation, $compensation->toArray());

            return $compensation->fresh();
        });
    }

    private function getEmployeeCompensationLimit(?int $employeeId): float
    {
        if (! $employeeId) {
            if (auth()->check() && auth()->user()->hasRole('admin')) {
                return PHP_FLOAT_MAX;
            }
            return 0.00;
        }

        $employee = Employee::with('user')->find($employeeId);

        if ($employee && $employee->user->hasRole('admin')) {
            return PHP_FLOAT_MAX;
        }

        if ($employee && $employee->user->hasRole('manager')) {
            return (float) $this->settingDAO->getByKey('manager_compensation_limit', 200.00);
        }

        return (float) $this->settingDAO->getByKey('employee_compensation_limit', 50.00);
    }

    private function checkBranchBudgetExceeded(int $branchId, CompensationType $type, float $amount): bool
    {
        $branch = Branch::find($branchId);
        if (! $branch) {
            return false;
        }

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $spentThisMonth = ComplaintCompensation::where('branch_id', $branchId)
            ->where('type', $type->value)
            ->whereIn('status', [CompensationStatus::GRANTED->value, CompensationStatus::PENDING_APPROVAL->value])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $projectedTotal = $spentThisMonth + $amount;

        if ($type === CompensationType::POINTS) {
            return $projectedTotal > $branch->monthly_points_budget;
        }

        return $projectedTotal > $branch->monthly_amount_budget;
    }

    public function getByClient(int $clientId, array $relations = [], int $perPage = 15)
    {
        return $this->compensationDAO->byClient($clientId, $relations, $perPage);
    }

    public function delete(int $compensationId): bool
    {
        return $this->transaction->execute(function () use ($compensationId) {
            $compensation = $this->compensationDAO->ById($compensationId);

            if (! $compensation) {
                throw new CompensationNotFoundException();
            }

            if ($compensation->status === CompensationStatus::GRANTED->value && $compensation->type === CompensationType::POINTS && $compensation->client_id) {
                $this->clientDAO->decrementPoints($compensation->client_id, (int) $compensation->amount);
            }

            return $this->compensationDAO->delete($compensation);
        });
    }

    public function getCompensationForComplaint(int $complaintId, array $relations = []): ?ComplaintCompensation
    {
        return $this->compensationDAO->byComplaintId($complaintId, $relations);
    }

    public function redeemCoupon(string $couponCode, int $employeeId): ComplaintCompensation
    {
        return $this->transaction->execute(function () use ($couponCode, $employeeId) {

            $compensation = $this->compensationDAO->byCouponCode($couponCode);

            if (! $compensation) {
                throw new InvalidCouponException();
            }

            if ($compensation->status !== CompensationStatus::GRANTED->value) {
                throw new CouponNotGrantedException();
            }

            if ($compensation->redeemed_at !== null) {
                throw new CouponAlreadyRedeemedException();
            }

            $compensation->redeemed_at = now();
            $compensation->redeemed_by_id = $employeeId;

            $this->compensationDAO->update($compensation, $compensation->toArray());

            return $compensation->fresh(['client', 'approvedBy', 'redeemedBy']);
        });
    }
}
