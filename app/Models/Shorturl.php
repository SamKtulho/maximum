<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shorturl extends Model
{
    const TYPE_GOOGLE = 1;
    const TYPE_OTHER = 2;
    const TYPE_REGISTRAR = 3;

    public function user()
    {
        return $this->belongsTo('\App\User');
    }

    public function domain()
    {
        return $this->belongsTo('App\Models\Domain');
    }

    public function urlstats()
    {
        return $this->hasMany('\App\Models\Urlstat');
    }
}
