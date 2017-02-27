<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Shorturl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Random
{
    public static function prepareData($text, $title, $edomain, $tic, $isSkip)
    {
        $pattern = '';
        $emailMasks = Email::getMasks();
        $edomain = empty($edomain) && $isSkip ? (array) key(Email::getMasks()) : $edomain;

        foreach ($edomain as $domain) {
            if (isset($emailMasks[$domain])) {
                foreach ($emailMasks[$domain] as $pat)
                    $pattern .= 'LIKE \'%' . $pat . '\' OR e.email ';
            }
            if ($domain === 'other') {
                $need = false;
                if (!empty($pattern)) {
                    $pattern = substr($pattern, 0, -12);
                    $pattern .= ' OR (e.email ';
                    $need = true;
                }
                foreach ($emailMasks as $pat)
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
            foreach ($emailMasks['mail'] as $pat) {
                if (strpos($results->email, $pat) !== false) {
                    $forMailRu = true;
                    break;
                }
            }

            $tRand = new TextRandomizer($text, ($isSkip ? date('dmY') . '.com' : $results->domain), $forMailRu);
            $titleRand = new TextRandomizer($title, ($isSkip ? date('dmY') . '.com' : $results->domain), $forMailRu);

            if (!$isSkip && !empty($storedDomain)) {
                $shortUrl = new Shorturl();
                $shortUrl->url = $tRand->getShortUrl();
                $shortUrl->domain_id = $storedDomain->id;
                $shortUrl->type = $forMailRu ? Shorturl::TYPE_OTHER : Shorturl::TYPE_GOOGLE;
                $shortUrl->user_id = Auth::user()->id;
                $shortUrl->save();
            }

            return ([$tRand->getText(), $titleRand->getText(), ($isSkip ? date('dmY') . '.com' : $results->domain), ($isSkip ? '*' : $results->email), $count]);
        }
        return [];

    }
}
