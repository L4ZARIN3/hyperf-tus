<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Exception;

/**
 * Exception for size limit exceeded.
 */
class SizeLimitExceededException extends TusException
{
    protected int $httpStatusCode = 413; // Payload Too Large

    public function __construct(string $message = 'Size limit exceeded')
    {
        parent::__construct($message, 413);
    }
}
