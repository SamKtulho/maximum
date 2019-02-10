<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponLog extends Model
{
    const STATUS_DONE = 1;

    protected $fillable = [
        'coupon_id',
        'status',
        'user_id',
    ];

}
