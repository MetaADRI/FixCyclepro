<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
//use Fideloper\Proxy\TrustProxies as Middleware;
use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string|null
     */
    protected $proxies;

    public function __construct()
    {
        // Render (and comparable PaaS) terminate TLS at a load balancer and
        // forward X-Forwarded-Proto/For/Host. Without trusting that proxy,
        // $request->isSecure() is false, url()/asset() emit http:// links and
        // HTTPS redirects loop. Default '*' (trust the platform LB); override
        // with TRUSTED_PROXIES as comma-separated IPs/CIDRs, or '' to trust
        // none (e.g. running directly without a proxy).
        $proxies = env('TRUSTED_PROXIES', '*');

        if ($proxies === '' || $proxies === null) {
            $this->proxies = null;
        } elseif ($proxies === '*') {
            $this->proxies = '*';
        } else {
            $this->proxies = array_map('trim', explode(',', (string) $proxies));
        }
    }

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR |
    Request::HEADER_X_FORWARDED_HOST |
    Request::HEADER_X_FORWARDED_PORT |
    Request::HEADER_X_FORWARDED_PROTO |
    Request::HEADER_X_FORWARDED_AWS_ELB;
}
