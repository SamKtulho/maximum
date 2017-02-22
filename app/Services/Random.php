<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Shorturl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Random
{
    private static $masks = [
        'mail' => ['@mail.ru', '@list.ru', '@bk.ru', '@inbox.ru'],
        'yandex' => ['@yandex.%', '@ya.%'],
        'gmail' => ['@gmail.%'],
    ];
    public static function prepareData($text, $title, $edomain, $tic, $isSkip)
    {
        $pattern = '';
        foreach ($edomain as $domain) {
            if (isset(self::$masks[$domain])) {
                foreach (self::$masks[$domain] as $pat)
                    $pattern .= 'LIKE \'%' . $pat . '\' OR e.email ';
            }
            if ($domain === 'other') {
                $need = false;
                if (!empty($pattern)) {
                    $pattern = substr($pattern, 0, -12);
                    $pattern .= ' OR (e.email ';
                    $need = true;
                }
                foreach (self::$masks as $pat)
                    foreach ($pat as $p) {
                        $pattern .= 'NOT LIKE \'%' . $p . '\' AND e.email ';
                    }
                $pattern = substr($pattern, 0, -13);
                if ($need) $pattern .= ')';

            }
        }
        if (!empty($pattern) && !in_array('other', $edomain)) $pattern = substr($pattern, 0, -12);

        $results = DB::select('SELECT d.status, e.email, d.domain from domains as d RIGHT JOIN emails as e ON d.id = e.domain_id WHERE e.is_valid = 1 AND d.tic ' . ($tic > 1 ? ' = ' : ' > ') . ' ? AND d.status = 0 AND (e.email ' . $pattern . ');', [$tic]);
        $count = count($results);
        $results = reset($results);

        if (!empty($results)) {
            if (!$isSkip) {
                $storedDomain = Domain::where('domain', $results->domain)->first();
                $storedDomain->status = Domain::STATUS_PROCESSED;
                $storedDomain->save();
            }

            $forMailRu = false;
            foreach (self::$masks['mail'] as $pat) {
                if (strpos($results->email, $pat) !== false) {
                    $forMailRu = true;
                    break;
                }
            }

            $tRand = new TextRandomizer($text, $results->domain, $forMailRu);
            $titleRand = new TextRandomizer($title, $results->domain, $forMailRu);

            if (!$isSkip && !empty($storedDomain)) {
                $shortUrl = new Shorturl();
                $shortUrl->url = $tRand->getShortUrl();
                $shortUrl->domain_id = $storedDomain->id;
                $shortUrl->type = $forMailRu ? Shorturl::TYPE_OTHER : Shorturl::TYPE_GOOGLE;
                $shortUrl->user_id = Auth::user()->id;
                $shortUrl->save();
            }

            return ([$tRand->getText(), $titleRand->getText(), $results->domain, $results->email, $count]);
        }
        return [];

    }
}
