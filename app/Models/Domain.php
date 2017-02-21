<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Domain extends Model
{
    const STATUS_NOT_PROCESSED = 0;
    const STATUS_PROCESSED = 1;
    //
    public function prepareData($content)
    {
        if (empty($content)) return null;


    }

    public function emails()
    {
        return $this->hasMany('App\Models\Email');
    }
}
