<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'uuid',
    'mediable_id',
    'mediable_type',
    'path',
    'original_name',
    'type',
    'custom_properties',
    'recorded_at'
])]

class Media extends Model
{

    protected function casts(): array
    {
        return [
            'custom_properties' => 'array',
            'recorded_at'       => 'datetime',
        ];
    }

    public function mediable()
    {
        return $this->morphTo();
    }
}
