<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Exception;

/**
 * Exception for invalid concatenation.
 */
class ConcatenationException extends TusException
{
    protected int $httpStatusCode = 400; // Bad Request

    public function __construct(string $message = 'Invalid concatenation')
    {
        parent::__construct($message, 400);
    }
}
