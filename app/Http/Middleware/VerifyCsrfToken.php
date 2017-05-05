<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/email/statistic/data',
        '/link/statistic/data',
        '/subdomain/statistic/data',
        '/link/moderation_log/data',
        '/email/moderation_log/data',
        '/moderator/change_vote_link',
    ];
}
