<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Shorturl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Link
{
    private static $masks = [
        'regru' => ['reg.ru'],
        'nicru' => ['nic.ru'],
    ];

    /**
     * @return array
     */
    public static function getMasks()
    {
        return self::$masks;
    }
    

}