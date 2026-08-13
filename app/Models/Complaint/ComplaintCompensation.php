<?php

namespace App\Models\Complaint;

use App\Enums\CompensationStatus;
use App\Enums\CompensationType;
use App\Models\Client;
use App\Models\Core\Employee;
use App\Models\Core\Branch;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'complaint_id',
    'branch_id',
    'client_id',
    'approved_by_id',
    'type',
    'amount',
    'coupon_code',
    'notes',
    'status',
    'granted_at',
])]
class ComplaintCompensation extends Model
{
    use HasFactory;

    protected $table = 'complaint_compensations';

    protected $casts = [
        'type'       => CompensationType::class,
        'status'     => CompensationStatus::class,
        'amount'     => 'decimal:2',
        'granted_at' => 'datetime',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by_id');
    }
}
