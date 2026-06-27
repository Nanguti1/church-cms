<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Only trust proxies when TRUSTED_PROXIES is set in .env (e.g. "*" behind
     * a load balancer, or a comma-separated list of proxy IPs). Leaving it
     * unset avoids forwarded headers from breaking local session cookies.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    public function __construct()
    {
        $trustedProxies = env('TRUSTED_PROXIES');

        if ($trustedProxies === null || $trustedProxies === '') {
            $this->proxies = null;
        } elseif ($trustedProxies === '*') {
            $this->proxies = '*';
        } else {
            $this->proxies = array_map('trim', explode(',', $trustedProxies));
        }
    }

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
