<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Event;

use Lazarini\HyperfTus\DTO\UploadDTO;

/**
 * Event dispatched when a new upload is created.
 */
class UploadCreated
{
    public function __construct(
        public readonly UploadDTO $upload,
    ) {}
}
