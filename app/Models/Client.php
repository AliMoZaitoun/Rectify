<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'points'])]
class Client extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
