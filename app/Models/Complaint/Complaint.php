<?php

namespace App\Models\Complaint;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Models\Client;
use App\Models\Core\Branch;
use App\Models\Core\Employee;
use App\Models\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'client_id',
    'device_id',
    'uuid',
    'tracking_code',
    'is_anonymous',
    'branch_id',
    'category_id',
    'title',
    'description',
    'status',
    'priority',
    'parent_id',
    'sla_due_at',
    'resolved_at',
    'ai_summary',
    'ai_suggested_category',
    'is_spam'
])]
class Complaint extends Model
{
    protected $casts = [
        'sla_due_at' => 'datetime',
        'resolved_at' => 'datetime',
        'status' => ComplaintStatus::class,
        'priority' => ComplaintPriority::class,
        'is_anonymous' => 'boolean',
        'is_spam' => 'boolean'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function parent()
    {
        return $this->belongsTo(Complaint::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Complaint::class, 'parent_id');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function assignedTo()
    {
        return $this->belongsTo(Employee::class, 'assigned_to_id');
    }

    public function histories()
    {
        return $this->hasMany(ComplaintHistory::class)->latest();
    }

    public function actions()
    {
        return $this->hasMany(ComplaintAction::class)->latest();
    }

    public function compensation()
    {
        return $this->hasOne(ComplaintCompensation::class);
    }

    public function ratings()
    {
        return $this->hasMany(ComplaintRating::class);
    }

    public function latestRating()
    {
        return $this->hasOne(ComplaintRating::class)->latestOfMany();
    }
}
