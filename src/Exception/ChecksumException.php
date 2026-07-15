<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Exception;

/**
 * Exception for checksum validation errors.
 */
class ChecksumException extends TusException
{
    protected int $httpStatusCode = 460; // Checksum Mismatch (TUS specific)

    public function __construct(string $message = 'Checksum mismatch')
    {
        parent::__construct($message, 460);
    }
}
