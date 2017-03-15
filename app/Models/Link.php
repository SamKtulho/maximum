<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    const STATUS_NOT_PROCESSED = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_DELAYED = 2;

    public function domain()
    {
        return $this->belongsTo('App\Models\Domain');
    }
}
