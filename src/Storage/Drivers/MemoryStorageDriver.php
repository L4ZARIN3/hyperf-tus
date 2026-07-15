<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Storage\Drivers;

use Lazarini\HyperfTus\Contracts\StorageDriverInterface;

/**
 * Memory storage driver.
 * Stores uploaded data in memory (for testing or temporary uploads).
 * Data is lost when the process restarts.
 */
class MemoryStorageDriver implements StorageDriverInterface
{
    /**
     * @var array<string, array{data: string, length: ?int, metadata: array, expires_at: ?int}>
     */
    private array $uploads = [];

    public function create(string $uploadId, ?int $length): bool
    {
        if ($this->exists($uploadId)) {
            return false;
        }

        $this->uploads[$uploadId] = [
            'data' => '',
            'length' => $length,
            'metadata' => [],
            'expires_at' => null,
            'created_at' => time(),
        ];

        return true;
    }

    public function append(string $uploadId, string $data, int $offset): int
    {
        if (!$this->exists($uploadId)) {
            return 0;
        }

        $currentData = $this->uploads[$uploadId]['data'];
        
        // If offset is beyond current length, pad with null bytes
        if ($offset > strlen($currentData)) {
            $currentData = str_pad($currentData, $offset, "\0");
        }
        
        // Write data at offset
        $before = substr($currentData, 0, $offset);
        $after = substr($currentData, $offset + strlen($data));
        
        $this->uploads[$uploadId]['data'] = $before . $data . $after;
        
        return strlen($data);
    }

    public function offset(string $uploadId): int
    {
        if (!$this->exists($uploadId)) {
            return 0;
        }

        return strlen($this->uploads[$uploadId]['data']);
    }

    public function exists(string $uploadId): bool
    {
        return isset($this->uploads[$uploadId]);
    }

    public function delete(string $uploadId): bool
    {
        if (!$this->exists($uploadId)) {
            return false;
        }

        unset($this->uploads[$uploadId]);
        return true;
    }

    public function finish(string $uploadId): bool
    {
        if (!$this->exists($uploadId)) {
            return false;
        }

        // In memory, finish is a no-op since data is already complete
        return true;
    }

    public function metadata(string $uploadId, array $metadata): bool
    {
        if (!$this->exists($uploadId)) {
            return false;
        }

        $this->uploads[$uploadId]['metadata'] = $metadata;
        return true;
    }

    public function length(string $uploadId): ?int
    {
        if (!$this->exists($uploadId)) {
            return null;
        }

        return $this->uploads[$uploadId]['length'];
    }

    public function expires(string $uploadId, \DateTimeInterface $expiresAt): bool
    {
        if (!$this->exists($uploadId)) {
            return false;
        }

        $this->uploads[$uploadId]['expires_at'] = $expiresAt->getTimestamp();
        return true;
    }

    public function getPath(string $uploadId): ?string
    {
        // Memory driver doesn't have a file path
        return null;
    }

    /**
     * Get raw data for an upload.
     */
    public function getData(string $uploadId): ?string
    {
        if (!$this->exists($uploadId)) {
            return null;
        }

        return $this->uploads[$uploadId]['data'];
    }

    /**
     * Clear all uploads from memory.
     */
    public function clear(): void
    {
        $this->uploads = [];
    }

    /**
     * Get count of uploads in memory.
     */
    public function count(): int
    {
        return count($this->uploads);
    }
}
