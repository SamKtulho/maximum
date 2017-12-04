<?php

namespace App\Services;

use App\Models\Domain;
use Illuminate\Support\Facades\DB;

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
        $allCount = DB::table('links')
            ->join('domains', 'domains.id', '=', 'links.domain_id')
            ->where('links.status', \App\Models\Link::STATUS_NOT_PROCESSED)
            ->whereNotNull('links.registrar')
            ->where('domains.status', Domain::STATUS_NOT_PROCESSED)
            ->where('domains.type', Domain::TYPE_LINK)
            ->count();

        $result = [];
        $count = 0;
        foreach (self::$masks as $title => $masks) {
            $model = DB::table('links')
                ->join('domains', 'domains.id', '=', 'links.domain_id')
                ->where('domains.status', Domain::STATUS_NOT_PROCESSED)
                ->where('domains.type', Domain::TYPE_LINK)
                ->where('links.status', \App\Models\Link::STATUS_NOT_PROCESSED)
                ->where(function ($query) use ($masks) {
                    foreach ($masks as $mask) {
                        $query->orWhere('registrar', 'like', $mask);
                    }
                })
                ->select('links.id');

            $countByMask = $model->count();
            $result[$title] = $countByMask;
            $count += $countByMask;
        }

        $result['other'] = $allCount - $count;
        $result['all'] = $allCount;

        return $result;
    }

}