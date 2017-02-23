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
            $i = 0;
            $model = \App\Models\Email::where('is_valid', 1)->where('email', 'like', '%' . array_shift($masks));
            if (!empty($masks)) {
                foreach ($masks as $mask) {
                    $model->orWhere('email', 'like', '%' . $mask);
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