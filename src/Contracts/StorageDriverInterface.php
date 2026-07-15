<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Contracts;

use Psr\Http\Message\UploadedFileInterface;

/**
 * Interface for storage drivers.
 * Responsible for actual file data storage operations.
 */
interface StorageDriverInterface
{
    /**
     * Create a new upload resource.
     *
     * @param string $uploadId Unique identifier for the upload
     * @param int|null $length Total length of the upload (null for deferred)
     * @return bool Success status
     */
    public function create(string $uploadId, ?int $length): bool;

    /**
     * Append data to an existing upload.
     *
     * @param string $uploadId Unique identifier for the upload
     * @param string $data Data chunk to append
     * @param int $offset Position to start writing
     * @return int Number of bytes written
     */
    public function append(string $uploadId, string $data, int $offset): int;

    /**
     * Get the current offset (bytes uploaded so far).
     *
     * @param string $uploadId Unique identifier for the upload
     * @return int Current offset
     */
    public function offset(string $uploadId): int;

    /**
     * Check if an upload exists.
     *
     * @param string $uploadId Unique identifier for the upload
     * @return bool True if exists
     */
    public function exists(string $uploadId): bool;

    /**
     * Delete an upload.
     *
     * @param string $uploadId Unique identifier for the upload
     * @return bool Success status
     */
    public function delete(string $uploadId): bool;

    /**
     * Finalize an upload (mark as complete).
     *
     * @param string $uploadId Unique identifier for the upload
     * @return bool Success status
     */
    public function finish(string $uploadId): bool;

    /**
     * Store metadata for an upload.
     *
     * @param string $uploadId Unique identifier for the upload
     * @param array $metadata Metadata key-value pairs
     * @return bool Success status
     */
    public function metadata(string $uploadId, array $metadata): bool;

    /**
     * Get the total length of an upload.
     *
     * @param string $uploadId Unique identifier for the upload
     * @return int|null Total length or null if not set
     */
    public function length(string $uploadId): ?int;

    /**
     * Set expiration time for an upload.
     *
     * @param string $uploadId Unique identifier for the upload
     * @param \DateTimeInterface $expiresAt Expiration datetime
     * @return bool Success status
     */
    public function expires(string $uploadId, \DateTimeInterface $expiresAt): bool;

    /**
     * Get the source path/final location of a completed upload.
     *
     * @param string $uploadId Unique identifier for the upload
     * @return string|null Path to the file or null if not found
     */
    public function getPath(string $uploadId): ?string;
}
