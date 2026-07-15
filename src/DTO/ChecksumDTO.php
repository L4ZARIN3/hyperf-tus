<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\DTO;

/**
 * Data Transfer Object for Checksum information.
 */
class ChecksumDTO
{
    private const SUPPORTED_ALGORITHMS = [
        'sha1' => 'sha1',
        'md5' => 'md5',
        'sha256' => 'sha256',
    ];

    public function __construct(
        public readonly string $algorithm,
        public readonly string $checksum,
    ) {
        if (!self::isSupportedAlgorithm($algorithm)) {
            throw new \InvalidArgumentException("Unsupported checksum algorithm: {$algorithm}");
        }
    }

    /**
     * Parse checksum from Upload-Checksum header value.
     * Format: <algorithm> <base64-checksum>
     */
    public static function fromHeader(string $header): self
    {
        $parts = explode(' ', trim($header), 2);
        
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException('Invalid checksum header format');
        }

        [$algorithm, $checksum] = $parts;
        $algorithm = strtolower($algorithm);

        return new self($algorithm, $checksum);
    }

    /**
     * Validate data against the checksum.
     */
    public function validate(string $data): bool
    {
        $computed = $this->computeChecksum($data);
        return $computed === $this->checksum;
    }

    /**
     * Compute checksum for given data.
     */
    public function computeChecksum(string $data): string
    {
        return match ($this->algorithm) {
            'sha1' => base64_encode(sha1($data, true)),
            'md5' => base64_encode(md5($data, true)),
            'sha256' => base64_encode(hash('sha256', $data, true)),
            default => throw new \InvalidArgumentException("Unsupported algorithm: {$this->algorithm}"),
        };
    }

    /**
     * Get the expected checksum length in bytes.
     */
    public function getLength(): int
    {
        return match ($this->algorithm) {
            'sha1' => 20,
            'md5' => 16,
            'sha256' => 32,
            default => 0,
        };
    }

    /**
     * Check if algorithm is supported.
     */
    public static function isSupportedAlgorithm(string $algorithm): bool
    {
        return array_key_exists(strtolower($algorithm), self::SUPPORTED_ALGORITHMS);
    }

    /**
     * Get supported algorithms.
     */
    public static function getSupportedAlgorithms(): array
    {
        return array_values(self::SUPPORTED_ALGORITHMS);
    }

    /**
     * Convert to header value format.
     */
    public function toHeader(): string
    {
        return "{$this->algorithm} {$this->checksum}";
    }
}
