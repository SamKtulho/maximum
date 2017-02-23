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
        $allDomains = \App\Models\Domain::where('status', 0)->get();
        foreach ($allDomains as $value) {
            foreach ($value->emails as $email) {
                if ($email->is_valid) {
                    ++$allCount;
                }
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
                if ($item->domain->status === 0) {
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