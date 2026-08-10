<?php

namespace App\Models\Core;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id'])]
class Employee extends Model
{
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employeeBranches()
    {
        return $this->hasMany(EmployeeBranch::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'employee_branches')
            ->withPivot(['from_date', 'to_date', 'position'])
            ->withTimestamps();
    }

    public function currentBranch()
    {
        return $this->hasOne(EmployeeBranch::class)->whereNull('to_date');
    }
}
