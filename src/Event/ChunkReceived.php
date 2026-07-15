<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Event;

use Lazarini\HyperfTus\DTO\UploadDTO;

/**
 * Event dispatched when a chunk is received.
 */
class ChunkReceived
{
    public function __construct(
        public readonly UploadDTO $upload,
        public readonly int $chunkSize,
        public readonly int $newOffset,
    ) {}
}
