<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Trust all proxies by default so deployments behind HTTPS-terminating
     * load balancers or reverse proxies correctly detect the original scheme.
     * Set TRUSTED_PROXIES to a comma-separated list of proxy IPs when known.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxy-related request data.
     *
     * Laravel 10/Symfony no longer supports the old integer value that was
     * commonly used for HEADER_X_FORWARDED_ALL. Explicitly trusting the
     * forwarded proto/host/port headers allows secure session cookies and
     * generated URLs to behave correctly in production behind a proxy.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO;

}
