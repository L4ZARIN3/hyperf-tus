<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Exception;

/**
 * Exception for upload length errors.
 */
class UploadLengthException extends TusException
{
    protected int $httpStatusCode = 400; // Bad Request

    public function __construct(string $message = 'Invalid or missing upload length')
    {
        parent::__construct($message, 400);
    }
}
