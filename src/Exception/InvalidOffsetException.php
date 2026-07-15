<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Exception;

/**
 * Exception for invalid offset errors.
 */
class InvalidOffsetException extends TusException
{
    protected int $httpStatusCode = 409; // Conflict

    public function __construct(string $message = 'Invalid offset')
    {
        parent::__construct($message, 409);
    }
}
