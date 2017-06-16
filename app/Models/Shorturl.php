<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Shorturl extends Model
{
    const TYPE_GOOGLE = 1;
    const TYPE_OTHER = 2;
    const TYPE_REGISTRAR = 3;
    const TYPE_SUBDOMAIN = 4;

    const ACTION_MAIL_SENT = 1;
    const ACTION_FORM_SENT = 2;
    const ACTION_NOT_FOUND = 3;

    public function user()
    {
        return $this->belongsTo('\App\User')->select(['id', 'name']);
    }

    public function domain()
    {
        return $this->belongsTo('App\Models\Domain')->select(['id', 'domain']);
    }

    public function urlstats()
    {
        return $this->hasMany('\App\Models\Urlstat');
    }
    
    public static function getStatistic($type)
    {
        $mails = Shorturl::where('type', $type)->where('user_id', Auth::id())->where('action', self::ACTION_MAIL_SENT)->count();
        $forms = Shorturl::where('type', $type)->where('user_id', Auth::id())->where('action', self::ACTION_FORM_SENT)->count();

        return [self::ACTION_MAIL_SENT => $mails, self::ACTION_FORM_SENT => $forms];
    }
}
