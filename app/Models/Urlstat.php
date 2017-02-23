<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Urlstat extends Model
{
    public function shorturl()
    {
        return $this->belongsTo('App\Models\Shorturl');
    }
}
