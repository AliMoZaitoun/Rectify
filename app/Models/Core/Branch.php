<?php

namespace App\Models\Core;

use App\Models\Location;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'name',
    'description',
    'monthly_amount_budget',
    'monthly_points_budget',
    'manager_id',
    'location_id'
])]
class Branch extends Model
{
    use SoftDeletes, HasTranslations;

    public $translatable = ['name', 'description'];
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'monthly_amount_budget' => 'decimal:2',
            'monthly_points_budget' => 'integer',
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

    public function manager()
    {
        return $this->belongsTo(Employee::class);
    }
}
