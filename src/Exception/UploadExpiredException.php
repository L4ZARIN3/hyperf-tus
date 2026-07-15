<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Exception;

/**
 * Exception for upload expiration errors.
 */
class UploadExpiredException extends TusException
{
    protected int $httpStatusCode = 410; // Gone

    public function __construct(string $message = 'Upload has expired')
    {
        parent::__construct($message, 410);
    }
}
