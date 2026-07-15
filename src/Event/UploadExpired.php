<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Event;

/**
 * Event dispatched when uploads are expired and cleaned up.
 */
class UploadExpired
{
    public function __construct(
        public readonly array $uploadIds,
        public readonly int $count,
    ) {}
}
