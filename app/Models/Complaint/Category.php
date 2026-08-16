<?php

namespace App\Models\Complaint;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'name',
    'description',
    'sla_hours',
])]
class Category extends Model
{
    use SoftDeletes, HasTranslations, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}");
    }
}
