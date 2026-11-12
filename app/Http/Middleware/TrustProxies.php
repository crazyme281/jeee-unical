<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array
     */
    /**
     * Render terminates TLS at its edge proxy and forwards requests with
     * X-Forwarded-* headers. Its proxy IPs are not fixed, so trust them all.
     * (Without this, Laravel generates http:// URLs and secure cookies break.)
     */
    protected $proxies = '*';

    /**
     * The current proxy header mappings.
     *
     * @var array
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO;
}
