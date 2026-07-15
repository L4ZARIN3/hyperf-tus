<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Lazarini\HyperfTus\Exception\ProtocolException;

/**
 * Middleware to validate TUS protocol headers.
 */
class TusMiddleware implements MiddlewareInterface
{
    private const TUS_VERSION = '1.0.0';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Only validate for TUS endpoints
        $path = $request->getUri()->getPath();
        $tusRoute = config('tus.route', '/files');
        
        if (!str_starts_with($path, $tusRoute)) {
            return $handler->handle($request);
        }

        // For OPTIONS requests, we don't require Tus-Resumable header
        if ($request->getMethod() === 'OPTIONS') {
            return $handler->handle($request);
        }

        // Validate Tus-Resumable header for all other requests
        $tusResumable = $request->getHeaderLine('Tus-Resumable');
        
        if (empty($tusResumable)) {
            throw new ProtocolException('Missing Tus-Resumable header');
        }

        // Check version compatibility
        if ($tusResumable !== self::TUS_VERSION) {
            throw new ProtocolException('Unsupported TUS version: ' . $tusResumable);
        }

        return $handler->handle($request);
    }
}
