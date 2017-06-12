<?php

namespace App\Services;

use App\Models\Domain;
use Illuminate\Support\Facades\DB;

class Manual
{
    public static function getLinksCount()
    {
        $allCount = DB::table('links')
            ->join('domains', 'domains.id', '=', 'links.domain_id')
            ->where('links.status', \App\Models\Link::STATUS_NOT_PROCESSED)
            ->whereNotNull('links.registrar')
            ->where('domains.status', Domain::STATUS_MANUAL_CHECK)->count();

        $result = [];
        $count = 0;
        foreach (\App\Services\Link::getMasks() as $title => $masks) {
            $model = DB::table('links')
                ->join('domains', 'domains.id', '=', 'links.domain_id')
                ->where('domains.status', Domain::STATUS_MANUAL_CHECK)
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

    public static function getSubdomainsCount()
    {
        $model = DB::table('domains')
            ->where('domains.status', Domain::STATUS_MANUAL_CHECK)
            ->where('domains.type', Domain::TYPE_SUBDOMAIN)
            ->select('domains.id');

        $count = $model->count();

        return $count;
    }

}