<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    const TYPE_EMAIL = 1;
    const TYPE_LINK = 2;
    const TYPE_MANUAL_DOMAIN = 3;
    const TYPE_MANUAL_SUBDOMAIN = 4;
    const TYPE_MANUAL_EMAIL = 5;
}
