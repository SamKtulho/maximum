<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shorturl extends Model
{
    const TYPE_GOOGLE = 1;
    const TYPE_OTHER = 2;

    public function user()
    {
        return $this->belongsTo('\App\User');
    }
}
