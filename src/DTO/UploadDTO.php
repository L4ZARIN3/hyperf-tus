<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\DTO;

/**
 * Data Transfer Object for Upload information.
 */
class UploadDTO
{
    public function __construct(
        public readonly string $id,
        public readonly ?int $length,
        public readonly int $offset,
        public readonly array $metadata,
        public readonly ?\DateTimeInterface $expiresAt,
        public readonly bool $completed,
        public readonly \DateTimeInterface $createdAt,
        public readonly \DateTimeInterface $updatedAt,
        public readonly ?string $finalId = null,
        public readonly array $partialIds = [],
    ) {}

    /**
     * Check if upload is expired.
     */
    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return $this->expiresAt < new \DateTimeImmutable();
    }

    /**
     * Check if upload is complete.
     */
    public function isComplete(): bool
    {
        return $this->completed && $this->offset === $this->length;
    }

    /**
     * Check if length is deferred.
     */
    public function isDeferred(): bool
    {
        return $this->length === null;
    }

    /**
     * Get remaining bytes to upload.
     */
    public function getRemaining(): int
    {
        if ($this->length === null) {
            return 0;
        }

        return max(0, $this->length - $this->offset);
    }

    /**
     * Get metadata value by key.
     */
    public function getMetadata(string $key): mixed
    {
        return $this->metadata[$key] ?? null;
    }

    /**
     * Check if this is a partial upload (for concatenation).
     */
    public function isPartial(): bool
    {
        return $this->finalId !== null;
    }

    /**
     * Check if this is a final upload (concatenation of partials).
     */
    public function isFinal(): bool
    {
        return !empty($this->partialIds);
    }
}
