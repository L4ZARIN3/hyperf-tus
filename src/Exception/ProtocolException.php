<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Exception;

/**
 * Exception for TUS protocol violations.
 */
class ProtocolException extends TusException
{
    protected int $httpStatusCode = 400; // Bad Request

    public function __construct(string $message = 'Protocol violation')
    {
        parent::__construct($message, 400);
    }
}
