<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Shorturl;
use App\Models\Link as ModelLink;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Random
{
    public static function manualGenText($fio, $email, $title, $text, $domainId, $isSkip = false)
    {
        $storedDomain = $isSkip ? '' : Domain::find($domainId);
        if (!$isSkip) {
            $storedDomain->status = Domain::STATUS_PROCESSED;
            $modelLink = ModelLink::where('domain_id', $storedDomain->id)->first();
            $modelLink->status = ModelLink::STATUS_PROCESSED;
            $modelLink->save();
            $storedDomain->save();
        }

        $fioRand = new TextRandomizer($fio, ($isSkip ? date('dmY') . '.com' : $storedDomain->domain));
        $emailRand = new TextRandomizer($email, ($isSkip ? date('dmY') . '.com' : $storedDomain->domain));
        $tRand = new TextRandomizer($text, ($isSkip ? date('dmY') . '.com' : $storedDomain->domain));
        $titleRand = new TextRandomizer($title, ($isSkip ? date('dmY') . '.com' : $storedDomain->domain));


        if (!$isSkip && !empty($storedDomain)) {
            $shortUrl = new Shorturl();
            $shortUrl->url = $tRand->getShortUrl();
            $shortUrl->domain_id = $storedDomain->id;
            $shortUrl->type = Shorturl::TYPE_REGISTRAR;
            $shortUrl->user_id = Auth::user()->id;
            $shortUrl->save();
        }

        return (['fio' => $fioRand->getText(), 'email' => $emailRand->getText(), 'text' => $tRand->getText(), 'title' => ($titleRand ? $titleRand->getText() : ''), 'domain' => ($isSkip ? date('dmY') . '.com' : $storedDomain->domain)]);
    }
    
    public static function getNextDomain($domains, $isSkip = false)
    {
        $linkMasks = Link::getMasks();
        $modelManual = DB::table('links')
            ->join('domains', 'domains.id', '=', 'links.domain_id')
            ->where('domains.status', Domain::STATUS_MANUAL_CHECK);
        $domains = empty($domains) && $isSkip ? (array) key(Email::getMasks()) : $domains;

        $modelManual->where(function ($query) use ($domains, $linkMasks) {
            if (in_array('other', $domains, true)) {
                foreach ($linkMasks as $linkMask) {
                    foreach ($linkMask as $item) {
                        $query->where('links.registrar', 'not like', $item);
                    }
                }
            }
            foreach ($domains as $domain) {
                if (isset($linkMasks[$domain])) {
                    foreach ($linkMasks[$domain] as $linkMask) {
                        $query->orWhere('links.registrar', $linkMask);
                    }
                }
            }
        });

        $modelManual->where('links.status', ModelLink::STATUS_NOT_PROCESSED)->select('domains.*');
        $result = $modelManual->first();
        if (empty($result)) {
            return [];
        }
        return ['id' => $result->id, 'domain' => $result->domain];
    }

    public static function linkPrepareData($fio, $email, $title, $text, $domains, $isSkip = false)
    {
        $linkMasks = Link::getMasks();
        $modelLink = DB::table('links')
        ->join('domains', 'domains.id', '=', 'links.domain_id')
        ->where('domains.status', Domain::STATUS_NOT_PROCESSED);
        $domains = empty($domains) && $isSkip ? (array) key(Email::getMasks()) : $domains;

        $modelLink->where(function ($query) use ($domains, $linkMasks) {
            if (in_array('other', $domains, true)) {
                foreach ($linkMasks as $linkMask) {
                    foreach ($linkMask as $item) {
                        $query->where('links.registrar', 'not like', $item);
                    }
                }
            }
            foreach ($domains as $domain) {
                if (isset($linkMasks[$domain])) {
                    foreach ($linkMasks[$domain] as $linkMask) {
                        $query->orWhere('links.registrar', $linkMask);
                    }
                }
            }
        });

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

            $link = $isSkip ? 'https://www.nic.ru/cgi/whois_webmail.cgi?domain=' . date('dmY') . '.com' : $result->link;

            return ([$fioRand->getText(), $emailRand->getText(), $tRand->getText(), ($titleRand ? $titleRand->getText() : ''), $link, ($isSkip ? date('dmY') . '.com' : $storedDomain->domain)]);
        }
        return [];
    }

    public static function emailPrepareData($text, $title, $edomain, $tic, $isSkip)
    {
        $emailMasks = Email::getMasks();

        $modelEmail = DB::table('emails')
            ->join('domains', 'domains.id', '=', 'emails.domain_id')
            ->where('domains.status', Domain::STATUS_NOT_PROCESSED)
            ->where('domains.type', Domain::TYPE_EMAIL)
            ->where('emails.is_valid', \App\Models\Email::STATUS_VALID);

        $edomain = empty($edomain) && $isSkip ? (array) key(Email::getMasks()) : $edomain;

        $modelEmail->where(function ($query) use ($edomain, $emailMasks) {
            if (in_array('other', $edomain, true)) {
                foreach ($emailMasks as $emailMask) {
                    foreach ($emailMask as $item) {
                        $query->where('emails.email', 'not like', '%' . $item);
                    }
                }
            }
            foreach ($edomain as $domain) {
                if (isset($emailMasks[$domain])) {
                    foreach ($emailMasks[$domain] as $emailMask) {
                        $query->orWhere('emails.email', 'like', '%' . $emailMask);
                    }
                }
            }
        });


        $count = count($modelEmail->count());
        $results = $modelEmail->first();;

        if (!empty($results) || $isSkip) {
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
