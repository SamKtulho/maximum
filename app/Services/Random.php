<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Shorturl;
use App\Models\Link as ModelLink;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Random
{
    public static function linkPrepareData($fio, $email, $title, $text, $domains, $isSkip = false)
    {
        $linkMasks = Link::getMasks();
        $modelLink = DB::table('links')
        ->join('domains', 'domains.id', '=', 'links.domain_id')
        ->where('domains.status', Domain::STATUS_NOT_PROCESSED);
        $domains = empty($domains) && $isSkip ? (array) key(Email::getMasks()) : $domains;


        if (in_array('other', $domains, true)) {
            foreach ($linkMasks as $linkMask) {
                foreach ($linkMask as $item) {
                    $modelLink->where('links.registrar', 'not like', $item);
                }
            }
        }
        foreach ($domains as $domain) {
            if (isset($linkMasks[$domain])) {
                foreach ($linkMasks[$domain] as $linkMask) {
                    $modelLink->orWhere('links.registrar', $linkMask);
                }
            }
        }

        $modelLink->where('links.status', ModelLink::STATUS_NOT_PROCESSED)->select('domains.*', 'links.*');

        $result = $modelLink->first();

        if (!empty($result) || $isSkip) {
            $storedDomain = $isSkip ? '' : Domain::where('id', $result->domain_id)->first();
            if (!$isSkip) {
                $storedDomain->status = Domain::STATUS_PROCESSED;
                $modelLink = ModelLink::find($result->id);
                $modelLink->status = ModelLink::STATUS_PROCESSED;
                $modelLink->save();
                $storedDomain->save();
            }

            $fioRand = new TextRandomizer($fio, ($isSkip ? date('dmY') . '.com' : $storedDomain->domain));
            $emailRand = new TextRandomizer($email, ($isSkip ? date('dmY') . '.com' : $storedDomain->domain));
            $tRand = new TextRandomizer($text, ($isSkip ? date('dmY') . '.com' : $storedDomain->domain));
            $titleRand = null;
            if ($result->registrar !== 'nic.ru' && $result->registrar !== 'reg.ru') {
                $titleRand = new TextRandomizer($title, ($isSkip ? date('dmY') . '.com' : $storedDomain->domain));
            }

            if (!$isSkip && !empty($storedDomain)) {
                $shortUrl = new Shorturl();
                $shortUrl->url = $tRand->getShortUrl();
                $shortUrl->domain_id = $result->domain_id;
                $shortUrl->type = Shorturl::TYPE_REGISTRAR;
                $shortUrl->user_id = Auth::user()->id;
                $shortUrl->save();
            }

            $link = $result->registrar === 'nic.ru' ? 'https://www.nic.ru/cgi/whois_webmail.cgi?domain=' . ($isSkip ? date('dmY') . '.com' : $storedDomain->domain) : $result->link;

            return ([$fioRand->getText(), $emailRand->getText(), $tRand->getText(), ($titleRand ? $titleRand->getText() : ''), $link, ($isSkip ? date('dmY') . '.com' : $storedDomain->domain)]);
        }
        return [];
    }

    public static function emailPrepareData($text, $title, $edomain, $tic, $isSkip)
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
            if (!$isSkip) {
                foreach ($emailMasks['mail'] as $pat) {
                    if (strpos($results->email, $pat) !== false) {
                        $forMailRu = true;
                        break;
                    }
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
