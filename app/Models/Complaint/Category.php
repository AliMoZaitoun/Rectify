<?php

namespace App\Models\Complaint;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'name',
    'description',
    'sla_hours',
])]
class Category extends Model
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

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
