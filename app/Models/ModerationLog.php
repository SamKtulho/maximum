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

    const IS_SKIPPED = 1;
    const IS_NO_SKIPPED = 0;
    
    private static $typeMap = [
        self::TYPE_EMAIL => 'Почта',
        self::TYPE_LINK => 'Регистраторы',
        self::TYPE_SUBDOMAIN => 'Субдомены'
    ];

    /**
     * @return array
     */
    public static function getTypeMap()
    {
        return self::$typeMap;
    }

    public function user()
    {
        return $this->belongsTo('\App\User')->select(['id', 'name']);
    }

    public function domain()
    {
        return $this->belongsTo('App\Models\Domain')->select(['id', 'domain']);
    }
}
