<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Exception;

/**
 * Exception for upload not found errors.
 */
class UploadNotFoundException extends TusException
{
    protected int $httpStatusCode = 404; // Not Found

    public function __construct(string $message = 'Upload not found')
    {
        parent::__construct($message, 404);
    }
}
