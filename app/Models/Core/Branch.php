<?php

namespace App\Models\Core;

use App\Models\Location;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

#[Fillable(['name', 'description', 'location_id'])]
class Branch extends Model
{
    use SoftDeletes, HasTranslations;

    public $translatable = ['name', 'description'];
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
        ];
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function employees()
    {
        return $this->hasMany(EmployeeBranch::class);
    }
}
