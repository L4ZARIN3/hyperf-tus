<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Exception;

/**
 * Exception for invalid content type.
 */
class InvalidContentTypeException extends TusException
{
    protected int $httpStatusCode = 415; // Unsupported Media Type

    public function __construct(string $message = 'Invalid Content-Type')
    {
        parent::__construct($message, 415);
    }
}
