<?php

namespace Bluebik\Logger\Middleware;

use Bluebik\Logger\LoggerFactory;
use Closure;
use Illuminate\Support\Str;

class AccessLogMiddleware
{
    protected $logger;

    public function handle($request, Closure $next)
    {
        $request->headers->set('x-request-id', time() . Str::random(6));
        $authorization = $request->header('authorization');
        $accessToken = $request->access_token;
        if (empty($authorization) && isset($accessToken)) {
            $request->headers->set('authorization', "Bearer $accessToken");
        }

        return $next($request);
    }

    public function terminate($request, $response)
    {
        $this->logger = LoggerFactory::create("access");
        $responseTime = number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3);
        $clientIp = $request->getClientIp();
        $requestMethod = $request->getMethod();
        $requestUri = $request->getPathInfo();
        $responseStatus = $response->getStatusCode();
        $xForwardedFor = $request->header('X-Forwarded-For');

        $this->logger->info("$clientIp $requestMethod $requestUri $responseStatus $responseTime ", ['input' => $request->all(), 'query' => $request->query(), 'X-Forwarded-For' => $xForwardedFor]);
    }
}
