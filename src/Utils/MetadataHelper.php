<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Utils;

/**
 * Utility class for metadata encoding/decoding.
 * TUS metadata is Base64 encoded key-value pairs.
 */
class MetadataHelper
{
    /**
     * Encode metadata array to TUS header format.
     * 
     * @param array $metadata Associative array of metadata
     * @return string Encoded metadata string
     */
    public static function encode(array $metadata): string
    {
        $parts = [];

        foreach ($metadata as $key => $value) {
            if ($value === null || $value === '') {
                // Key without value
                $parts[] = base64_encode($key);
            } else {
                // Key with value
                $parts[] = base64_encode($key) . ' ' . base64_encode((string) $value);
            }
        }

        return implode(',', $parts);
    }

    /**
     * Decode metadata from TUS header format.
     * 
     * @param string $encoded Encoded metadata string
     * @return array Decoded associative array
     */
    public static function decode(string $encoded): array
    {
        if (trim($encoded) === '') {
            return [];
        }

        $metadata = [];
        $pairs = self::splitMetadata($encoded);

        foreach ($pairs as $pair) {
            $pair = trim($pair);
            
            if ($pair === '') {
                continue;
            }

            $parts = explode(' ', $pair, 2);
            
            if (count($parts) === 1) {
                // Key without value
                $key = base64_decode($parts[0], true);
                if ($key !== false) {
                    $metadata[$key] = null;
                }
            } elseif (count($parts) === 2) {
                // Key with value
                $key = base64_decode($parts[0], true);
                $value = base64_decode($parts[1], true);
                
                if ($key !== false && $value !== false) {
                    $metadata[$key] = $value;
                }
            }
        }

        return $metadata;
    }

    /**
     * Split metadata string respecting spaces within values.
     */
    private static function splitMetadata(string $encoded): array
    {
        $pairs = [];
        $current = '';
        $spaceCount = 0;

        for ($i = 0, $len = strlen($encoded); $i < $len; $i++) {
            $char = $encoded[$i];

            if ($char === ',') {
                if ($current !== '') {
                    $pairs[] = $current;
                }
                $current = '';
                $spaceCount = 0;
            } elseif ($char === ' ') {
                $spaceCount++;
                $current .= $char;
                
                // Reset space count if we have more than 2 spaces (likely in base64)
                if ($spaceCount > 2) {
                    $spaceCount = 0;
                }
            } else {
                // If we had spaces but now have other chars, reset
                if ($spaceCount > 0 && $char !== ' ') {
                    // Check if we're at a key-value boundary (exactly one space between two base64 strings)
                    // This is a simplified approach - base64 doesn't contain spaces
                    $trimmed = rtrim($current);
                    if (self::isValidBase64Chunk($trimmed)) {
                        // Might be end of key, check next chunk
                    }
                }
                $current .= $char;
            }
        }

        if ($current !== '') {
            $pairs[] = $current;
        }

        // Better approach: split by comma first, then handle each pair
        return array_map('trim', explode(',', $encoded));
    }

    /**
     * Simple validation for base64 chunk.
     */
    private static function isValidBase64Chunk(string $chunk): bool
    {
        $chunk = trim($chunk);
        return preg_match('/^[A-Za-z0-9+\/]+=*$/', $chunk);
    }

    /**
     * Get a specific metadata value.
     * 
     * @param array $metadata Decoded metadata array
     * @param string $key Key to retrieve
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get(array $metadata, string $key, mixed $default = null): mixed
    {
        return $metadata[$key] ?? $default;
    }

    /**
     * Get filename from metadata.
     * 
     * @param array $metadata Decoded metadata array
     * @return string|null
     */
    public static function getFilename(array $metadata): ?string
    {
        return self::get($metadata, 'filename');
    }

    /**
     * Get file type from metadata.
     * 
     * @param array $metadata Decoded metadata array
     * @return string|null
     */
    public static function getFiletype(array $metadata): ?string
    {
        return self::get($metadata, 'filetype');
    }
}
