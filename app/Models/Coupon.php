<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    private $couponTypeMap = [
        ''
    ];

    protected $fillable = [
        'id',
        'source',
        'image',
        'name',
        'description',
        'start_date',
        'active_to',
        'coupon_type',
        'promo_code',
        'offer_name',
        'offer_id',
        'status',
        'status_id',
        'category_id',
        'action_category_name',
        'coupon_category_id',
        'coupon_category_name',
        'url',
        'url_frame',
        'look',
        'domain',
    ];

}
