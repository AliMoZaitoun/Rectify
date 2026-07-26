<?php

namespace App\Models\Complaint;

use App\Models\Core\Employee;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'complaint_id',
    'old_status',
    'new_status',
    'assigned_to_id',
    'changed_by_type',
    'changed_by_id',
    'duration_in_hours',
    'comment',
])]

class ComplaintHistory extends Model
{
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to_id');
    }

    public function changedBy(): MorphTo
    {
        return $this->morphTo();
    }
}
