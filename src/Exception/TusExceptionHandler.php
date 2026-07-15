<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Exception;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Lazarini\HyperfTus\Exception\TusException;

/**
 * Exception Handler for TUS exceptions.
 */
class TusExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $e, ResponseInterface $response): ResponseInterface
    {
        if ($e instanceof TusException) {
            $statusCode = $e->getHttpStatusCode();
            $headers = $e->getHeaders();
            
            // Add TUS headers
            $headers['Tus-Resumable'] = '1.0.0'];
            
            return $response->withStatus($statusCode)
                ->withAddedHeader('Content-Type', 'text/plain; charset=utf-8')
                ->withBody(new SwooleStream($e->getMessage()));
        }

        return $response;
    }

    public function isValid(Throwable $e): bool
    {
        return $e instanceof TusException;
    }
}
