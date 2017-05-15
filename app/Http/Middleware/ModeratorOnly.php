<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class ModeratorOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!User::isModerator()) {
            return abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
