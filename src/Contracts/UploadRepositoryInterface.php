<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Contracts;

/**
 * Interface for upload metadata repository.
 * Responsible for persisting upload state and metadata.
 */
interface UploadRepositoryInterface
{
    /**
     * Create a new upload record.
     *
     * @param string $id Unique identifier
     * @param int|null $length Total length (null for deferred)
     * @param array $metadata Metadata key-value pairs
     * @param \DateTimeInterface|null $expiresAt Expiration time
     * @return bool Success status
     */
    public function create(
        string $id,
        ?int $length,
        array $metadata = [],
        ?\DateTimeInterface $expiresAt = null
    ): bool;

    /**
     * Find an upload by ID.
     *
     * @param string $id Upload ID
     * @return array|null Upload data or null if not found
     */
    public function find(string $id): ?array;

    /**
     * Update upload offset.
     *
     * @param string $id Upload ID
     * @param int $offset New offset value
     * @return bool Success status
     */
    public function updateOffset(string $id, int $offset): bool;

    /**
     * Update upload length (for deferred length uploads).
     *
     * @param string $id Upload ID
     * @param int $length Total length
     * @return bool Success status
     */
    public function updateLength(string $id, int $length): bool;

    /**
     * Mark upload as completed.
     *
     * @param string $id Upload ID
     * @return bool Success status
     */
    public function markCompleted(string $id): bool;

    /**
     * Delete an upload record.
     *
     * @param string $id Upload ID
     * @return bool Success status
     */
    public function delete(string $id): bool;

    /**
     * Check if an upload exists.
     *
     * @param string $id Upload ID
     * @return bool True if exists
     */
    public function exists(string $id): bool;

    /**
     * Get all expired uploads.
     *
     * @return array List of expired upload IDs
     */
    public function getExpired(): array;

    /**
     * Get uploads that are partial (for concatenation).
     *
     * @param string $finalId Final upload ID
     * @return array List of partial upload IDs
     */
    public function getPartials(string $finalId): array;

    /**
     * Set concatenated parts for a final upload.
     *
     * @param string $finalId Final upload ID
     * @param array $partialIds Array of partial upload IDs
     * @return bool Success status
     */
    public function setConcatParts(string $finalId, array $partialIds): bool;

    /**
     * Clean up expired uploads from repository.
     *
     * @param int $olderThan Timestamp threshold
     * @return int Number of records deleted
     */
    public function cleanExpired(int $olderThan): int;
}
