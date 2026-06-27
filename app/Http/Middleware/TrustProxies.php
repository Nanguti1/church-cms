<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * @var int
     */
    protected $headers;

    public function __construct()
    {
        $this->proxies = config('trustedproxy.proxies');
        $this->headers = config('trustedproxy.headers');
    }
}
