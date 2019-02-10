<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    const ROLE_GUEST = 0;
    const ROLE_FINDER = 1;
    const ROLE_MODERATOR = 2;
    const ROLE_ADMIN = 3;
    const ROLE_HEY_CONTENT = 4;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function shorturls()
    {
        return $this->hasMany('App\Models\Shorturl');
    }

    public static function isAdmin()
    {
        return Auth::user() && Auth::user()->role == self::ROLE_ADMIN;
    }

    public static function isModerator()
    {
        return Auth::user() && (Auth::user()->role == self::ROLE_MODERATOR || Auth::user()->role == self::ROLE_ADMIN);
    }

    public static function isExternalUser()
    {
        return Auth::user() && (Auth::user()->role == self::ROLE_FINDER || Auth::user()->role == self::ROLE_ADMIN);
    }
}
