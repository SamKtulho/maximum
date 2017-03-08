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

    public static function getLinksCount()
    {
        $allCount = 0;

        $allLinks = \App\Models\Link::where('status', \App\Models\Link::STATUS_NOT_PROCESSED)->with(['domain'=> function ($query) {
            $query->where('status', Domain::STATUS_NOT_PROCESSED);
        }])->get();

        foreach ($allLinks as $link) {
            if ($link->domain) {
                ++$allCount;
            }
        }

        $result = [];
        $count = 0;
        foreach (self::$masks as $title => $masks) {
            $i = 0;
            $model = \App\Models\Link::where('status', \App\Models\Link::STATUS_NOT_PROCESSED)->where('registrar', 'like', array_shift($masks));
            if (!empty($masks)) {
                foreach ($masks as $mask) {
                    $model->orWhere('registrar', 'like', $mask);
                }
            }
            foreach ($model->get() as $item) {
                if ((int) $item->domain->status === Domain::STATUS_NOT_PROCESSED) {
                    ++$i;
                }
            }
            $result[$title] = $i;
            $count += $i;
        }

        $result['other'] = $allCount - $count;

        return $result;
    }

}