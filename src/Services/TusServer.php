<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Services;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\EventDispatcher;
use Lazarini\HyperfTus\Contracts\StorageDriverInterface;
use Lazarini\HyperfTus\Contracts\UploadRepositoryInterface;
use Lazarini\HyperfTus\DTO\UploadDTO;
use Lazarini\HyperfTus\DTO\ChecksumDTO;
use Lazarini\HyperfTus\Event\UploadCreated;
use Lazarini\HyperfTus\Event\ChunkReceived;
use Lazarini\HyperfTus\Event\UploadCompleted;
use Lazarini\HyperfTus\Event\UploadDeleted;
use Lazarini\HyperfTus\Exception\UploadNotFoundException;
use Lazarini\HyperfTus\Exception\InvalidOffsetException;
use Lazarini\HyperfTus\Exception\ChecksumException;
use Lazarini\HyperfTus\Exception\UploadExpiredException;
use Lazarini\HyperfTus\Exception\UploadLengthException;
use Lazarini\HyperfTus\Exception\ProtocolException;
use Lazarini\HyperfTus\Exception\SizeLimitExceededException;
use Lazarini\HyperfTus\Exception\ConcatenationException;
use Lazarini\HyperfTus\Utils\MetadataHelper;

/**
 * Main TUS server service.
 * Handles all TUS protocol logic.
 */
class TusServer
{
    private const TUS_VERSION = '1.0.0';
    private const EXTENSIONS = ['creation', 'creation-with-upload', 'checksum', 'expiration', 'concatenation', 'termination'];

    public function __construct(
        private readonly ConfigInterface $config,
        private readonly StorageDriverInterface $storageDriver,
        private readonly UploadRepositoryInterface $repository,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    /**
     * Get TUS server capabilities for OPTIONS response.
     */
    public function getCapabilities(): array
    {
        $tusConfig = $this->config->get('tus', []);
        $extensions = [];

        $enabledExtensions = $tusConfig['extensions'] ?? [];
        
        if ($enabledExtensions['creation'] ?? true) {
            $extensions[] = 'creation';
        }
        if ($enabledExtensions['creation_with_upload'] ?? true) {
            $extensions[] = 'creation-with-upload';
        }
        if ($enabledExtensions['checksum'] ?? true) {
            $extensions[] = 'checksum';
        }
        if ($enabledExtensions['expiration'] ?? true) {
            $extensions[] = 'expiration';
        }
        if ($enabledExtensions['concatenation'] ?? true) {
            $extensions[] = 'concatenation';
        }
        if ($enabledExtensions['termination'] ?? true) {
            $extensions[] = 'termination';
        }

        return [
            'version' => self::TUS_VERSION,
            'extensions' => $extensions,
            'max_size' => $tusConfig['max_size'] ?? null,
            'checksum_algorithms' => $tusConfig['checksum_algorithms'] ?? ['sha1', 'md5', 'sha256'],
        ];
    }

    /**
     * Create a new upload.
     */
    public function create(
        ?int $length,
        array $metadata = [],
        bool $deferLength = false,
        ?string $concatMode = null,
        ?array $concatPartialIds = null,
        ?ChecksumDTO $checksum = null
    ): UploadDTO {
        $tusConfig = $this->config->get('tus', []);
        
        // Validate max size
        $maxSize = $tusConfig['max_size'] ?? null;
        if ($maxSize !== null && $length !== null && $length > $maxSize) {
            throw new SizeLimitExceededException("Upload size exceeds maximum allowed ({$maxSize} bytes)");
        }

        // Validate length requirement
        if (!$deferLength && $length === null && $concatMode !== 'final') {
            throw new UploadLengthException('Upload-Length or Upload-Defer-Length header is required');
        }

        // Generate unique ID
        $uploadId = $this->generateUploadId();

        // Handle concatenation
        if ($concatMode === 'final' && !empty($concatPartialIds)) {
            // Verify all partial uploads exist and are complete
            foreach ($concatPartialIds as $partialId) {
                if (!$this->repository->exists($partialId)) {
                    throw new ConcatenationException("Partial upload not found: {$partialId}");
                }
                
                $partialData = $this->repository->find($partialId);
                if (!$partialData['completed']) {
                    throw new ConcatenationException("Partial upload not completed: {$partialId}");
                }
            }
            
            // Calculate total length from partials
            if ($length === null) {
                $length = 0;
                foreach ($concatPartialIds as $partialId) {
                    $partialData = $this->repository->find($partialId);
                    $length += $partialData['offset'] ?? 0;
                }
            }
        }

        // Set expiration
        $expiresAt = null;
        $expirationSeconds = $tusConfig['expiration'] ?? null;
        if ($expirationSeconds !== null) {
            $expiresAt = new \DateTimeImmutable('+' . $expirationSeconds . ' seconds');
        }

        // Create in repository
        $this->repository->create($uploadId, $length, $metadata, $expiresAt);

        // Create in storage driver
        $this->storageDriver->create($uploadId, $length);

        // Store metadata
        if (!empty($metadata)) {
            $this->storageDriver->metadata($uploadId, $metadata);
        }

        // Set expiration
        if ($expiresAt !== null) {
            $this->storageDriver->expires($uploadId, $expiresAt);
        }

        // Handle concatenation metadata
        if ($concatMode === 'final' && !empty($concatPartialIds)) {
            $this->repository->setConcatParts($uploadId, $concatPartialIds);
        }

        // Create UploadDTO
        $upload = new UploadDTO(
            id: $uploadId,
            length: $length,
            offset: 0,
            metadata: $metadata,
            expiresAt: $expiresAt,
            completed: false,
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable(),
            finalId: null,
            partialIds: $concatPartialIds ?? []
        );

        // Dispatch event
        $this->eventDispatcher->dispatch(new UploadCreated($upload));

        return $upload;
    }

    /**
     * Get upload information (HEAD request).
     */
    public function getUploadInfo(string $uploadId): UploadDTO
    {
        $data = $this->repository->find($uploadId);
        
        if ($data === null) {
            throw new UploadNotFoundException("Upload not found: {$uploadId}");
        }

        // Check expiration
        if (isset($data['expires_at']) && is_int($data['expires_at'])) {
            if ($data['expires_at'] < time() && !$data['completed']) {
                throw new UploadExpiredException("Upload has expired: {$uploadId}");
            }
        }

        return new UploadDTO(
            id: $data['id'],
            length: $data['length'],
            offset: $data['offset'],
            metadata: $data['metadata'] ?? [],
            expiresAt: isset($data['expires_at']) && is_int($data['expires_at']) 
                ? new \DateTimeImmutable('@' . $data['expires_at']) 
                : null,
            completed: $data['completed'] ?? false,
            createdAt: new \DateTimeImmutable('@' . ($data['created_at'] ?? time())),
            updatedAt: new \DateTimeImmutable('@' . ($data['updated_at'] ?? time())),
            finalId: $data['final_id'] ?? null,
            partialIds: $data['partial_ids'] ?? []
        );
    }

    /**
     * Append data to an upload (PATCH request).
     */
    public function patch(
        string $uploadId,
        string $data,
        int $offset,
        ?ChecksumDTO $checksum = null
    ): int {
        $upload = $this->getUploadInfo($uploadId);

        // Validate offset
        if ($offset !== $upload->offset) {
            throw new InvalidOffsetException(
                "Invalid offset. Expected: {$upload->offset}, Got: {$offset}"
            );
        }

        // Validate length
        if ($upload->length !== null) {
            $newOffset = $offset + strlen($data);
            if ($newOffset > $upload->length) {
                throw new UploadLengthException(
                    "Upload would exceed declared length ({$upload->length})"
                );
            }
        }

        // Validate checksum if provided
        if ($checksum !== null && !$checksum->validate($data)) {
            throw new ChecksumException('Checksum validation failed');
        }

        // Append data
        $bytesWritten = $this->storageDriver->append($uploadId, $data, $offset);

        if ($bytesWritten === 0) {
            throw new ProtocolException('Failed to write data');
        }

        // Update offset in repository
        $newOffset = $offset + $bytesWritten;
        $this->repository->updateOffset($uploadId, $newOffset);

        // Check if upload is complete
        $isComplete = false;
        if ($upload->length !== null && $newOffset >= $upload->length) {
            $this->repository->markCompleted($uploadId);
            $this->storageDriver->finish($uploadId);
            $isComplete = true;
        }

        // Get updated upload info
        $updatedUpload = $this->getUploadInfo($uploadId);

        // Dispatch event
        $this->eventDispatcher->dispatch(new ChunkReceived($updatedUpload, $bytesWritten, $newOffset));

        if ($isComplete) {
            $this->eventDispatcher->dispatch(new UploadCompleted($updatedUpload));
        }

        return $bytesWritten;
    }

    /**
     * Delete an upload.
     */
    public function delete(string $uploadId): void
    {
        $upload = $this->getUploadInfo($uploadId);

        // Delete from storage
        $this->storageDriver->delete($uploadId);

        // Delete from repository
        $this->repository->delete($uploadId);

        // Dispatch event
        $this->eventDispatcher->dispatch(new UploadDeleted($upload));
    }

    /**
     * Clean up expired uploads.
     */
    public function cleanExpired(): int
    {
        $expiredIds = $this->repository->getExpired();
        $count = 0;

        foreach ($expiredIds as $uploadId) {
            try {
                $this->storageDriver->delete($uploadId);
                $this->repository->delete($uploadId);
                $count++;
            } catch (\Throwable $e) {
                // Log error but continue cleaning
                // In production, use proper logging
            }
        }

        if ($count > 0) {
            $this->eventDispatcher->dispatch(new UploadExpired($expiredIds, $count));
        }

        return $count;
    }

    /**
     * Parse Upload-Metadata header.
     */
    public function parseMetadata(string $header): array
    {
        return MetadataHelper::decode($header);
    }

    /**
     * Encode metadata to header format.
     */
    public function encodeMetadata(array $metadata): string
    {
        return MetadataHelper::encode($metadata);
    }

    /**
     * Get the location URL for an upload.
     */
    public function getLocation(string $uploadId, string $baseUrl): string
    {
        $route = rtrim($this->config->get('tus.route', '/files'), '/');
        return rtrim($baseUrl, '/') . $route . '/' . $uploadId;
    }

    /**
     * Generate a unique upload ID.
     */
    private function generateUploadId(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Get TUS version.
     */
    public function getVersion(): string
    {
        return self::TUS_VERSION;
    }

    /**
     * Get supported extensions.
     */
    public function getExtensions(): array
    {
        return self::EXTENSIONS;
    }
}
