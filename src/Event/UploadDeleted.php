<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Event;

use Lazarini\HyperfTus\DTO\UploadDTO;

/**
 * Event dispatched when an upload is deleted.
 */
class UploadDeleted
{
    public function __construct(
        public readonly UploadDTO $upload,
    ) {}
}
