<?php

namespace App\Services;

use App\Models\Email;
use App\Models\Domain;
use Illuminate\Support\Facades\DB;

class Random
{
    private static $masks = [
        'mail' => ['@mail.ru', '@list.ru', '@bk.ru', '@inbox.ru'],
        'yandex' => ['@yandex.%'],
        'gmail' => ['@gmail.%']
    ];
    public static function prepareData($text, $title, $edomain, $tic)
    {
        $pattern = '';
        foreach ($edomain as $domain) {
            if (isset(self::$masks[$domain])) {
                foreach (self::$masks[$domain] as $pat)
                $pattern .= 'LIKE \'%' . $pat . '\' OR e.email ';
                break;
            }
        }
        if (!empty($pattern)) $pattern = substr($pattern, 0, -12);
            $results = DB::select('SELECT d.status, e.email, d.domain from domains as d RIGHT JOIN emails as e ON d.id = e.domain_id WHERE e.is_valid = 1 AND d.tic ' . ($tic > 1 ? ' = ' : ' > ') . ' ? AND d.status = 0 AND (e.email ' . $pattern . ');', [$tic]);
        $results = reset($results);

        if (!empty($results)) {
            $storedDomain = Domain::where('domain', $results->domain)->first();
            $storedDomain->status = Domain::STATUS_PROCESSED;
            $storedDomain->save();

            $tRand = new TextRandomizer($text, $results->domain);
            $titleRand = new TextRandomizer($title, $results->domain);
            return ([$tRand->getText(), $titleRand->getText(), $results->domain, $results->email]);
        }
        return [];

    }
}
