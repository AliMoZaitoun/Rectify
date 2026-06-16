<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'longitude', 'latitude'])]
class Branch extends Model
{
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'location_id' => 'float'
        ];
    }
}
