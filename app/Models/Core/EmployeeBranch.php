<?php

namespace App\Models\Core;

use App\Models\Core\Employee;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['employee_id', 'branch_id', 'from_date', 'to_date', 'position'])]
class EmployeeBranch extends Model
{
    use SoftDeletes;
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
