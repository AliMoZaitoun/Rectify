<?php

namespace App\Models\AI;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'filters',
    'report_data',
    'created_by',
])]

class AiReport extends Model
{
    protected $casts = [
        'filters'     => 'array',
        'report_data' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
