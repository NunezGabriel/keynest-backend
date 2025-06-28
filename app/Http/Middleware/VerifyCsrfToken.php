<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/login',
        'api/register',
        'api/properties',
        'api/properties/*', // esto cubre DELETE y PUT
        'api/users',
        'api/users/*',
    ];
}
