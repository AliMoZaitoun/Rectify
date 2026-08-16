<?php

namespace App\Models;

use App\Models\Complaint\Complaint;
use App\Models\Complaint\ComplaintCompensation;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Client extends Model
{
    use LogsActivity;

    protected $fillable = ['user_id', 'points'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function compensations()
    {
        return $this->hasMany(ComplaintCompensation::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}");
    }
}
