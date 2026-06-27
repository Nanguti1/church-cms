<?php

use Illuminate\Http\Request;

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Set TRUSTED_PROXIES=* in production when the app runs behind LiteSpeed,
    | nginx, Apache mod_proxy, Cloudflare, or a load balancer. This lets
    | Laravel detect HTTPS correctly so secure session cookies work.
    |
    | Leave unset (or empty) for local development.
    |
    */

    'proxies' => ($proxies = env('TRUSTED_PROXIES')) === '' || $proxies === null
        ? null
        : ($proxies === '*' ? '*' : array_map('trim', explode(',', $proxies))),

    'headers' => Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO,

];
