<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModerationLog extends Model
{
    const TYPE_EMAIL = 0;
    const TYPE_LINK = 1;
    const TYPE_SUBDOMAIN = 2;

    const RESULT_YES = 1;
    const RESULT_NO = 0;

    public function user()
    {
        return $this->belongsTo('\App\User')->select(['id', 'name']);
    }

    public function domain()
    {
        return $this->belongsTo('App\Models\Domain')->select(['id', 'domain']);
    }
}
