<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Shorturl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Email
{
    private static $masks = [
        'mail' => ['@mail.ru', '@list.ru', '@bk.ru', '@inbox.ru'],
        'yandex' => ['@yandex.%', '@ya.%'],
        'gmail' => ['@gmail.%'],
    ];

    /**
     * @return array
     */
    public static function getMasks()
    {
        return self::$masks;
    }
    
    public static function getEmailsCount()
    {
        $allCount = 0;

        $allEmails = \App\Models\Email::where('is_valid', \App\Models\Email::STATUS_VALID)->with(['domain'=> function ($query) {
            $query->where('status', Domain::STATUS_NOT_PROCESSED);
        }])->get();

        foreach ($allEmails as $email) {
            if ($email->domain) {
                ++$allCount;
            }
        }

        $result = [];
        $count = 0;
        foreach (self::$masks as $title => $masks) {
            $model = DB::table('emails')
                ->join('domains', 'domains.id', '=', 'emails.domain_id')
                ->where('domains.status', Domain::STATUS_NOT_PROCESSED)
                ->where('domains.type', Domain::TYPE_EMAIL)
                ->where('emails.is_valid', 1)
                ->where(function ($query) use ($masks) {
                    foreach ($masks as $mask) {
                        $query->orWhere('email', 'like', '%' . $mask);
                    }
                })
                ->select('emails.id');

            $countByMask = $model->count();
            $result[$title] = $countByMask;
            $count += $countByMask;
        }

        $result['other'] = $allCount - $count;

        return $result;
    }
}