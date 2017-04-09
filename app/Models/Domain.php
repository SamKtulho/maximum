<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    const STATUS_NOT_PROCESSED = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_DELAYED = 2;
    const STATUS_MANUAL_CHECK = 3;
    const STATUS_MODERATE = 4;
    const STATUS_BAD = 5;
    const STATUS_EMAIL_NOT_FOUND = 6;

    const TYPE_EMAIL = 0;
    const TYPE_LINK = 1;
    const TYPE_SUBDOMAIN = 2;
    const TYPE_UNKNOWN = 3;

    protected $fillable = ['domain', 'type', 'tic', 'status', 'source'];

    //
    public function prepareData($content)
    {
        if (empty($content)) return null;
    }

    public function emails()
    {
        return $this->hasMany('App\Models\Email')->select(['id', 'email']);
    }

    public function shorturls()
    {
        return $this->hasMany('\App\Models\Shorturl');
    }

    public function links()
    {
        return $this->hasMany('\App\Models\Link');
    }
}
