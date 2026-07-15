<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Exception;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Base exception for TUS protocol errors.
 */
class TusException extends \RuntimeException
{
    protected int $httpStatusCode = 500;
    protected array $headers = [];

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
