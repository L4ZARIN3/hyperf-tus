<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Storage\Drivers;

use Lazarini\HyperfTus\Contracts\StorageDriverInterface;
use Hyperf\Contract\ConfigInterface;

/**
 * Local filesystem storage driver.
 * Stores uploaded files on the local filesystem.
 */
class LocalStorageDriver implements StorageDriverInterface
{
    private string $storagePath;
    private array $uploads = [];

    public function __construct(
        private readonly ConfigInterface $config
    ) {
        $tusConfig = $config->get('tus', []);
        $this->storagePath = rtrim($tusConfig['storage_path'] ?? BASE_PATH . '/runtime/tus', '/');
        
        // Ensure storage directory exists
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
    }

    /**
     * Get the file path for an upload.
     */
    private function getFilePath(string $uploadId): string
    {
        return $this->storagePath . '/' . $uploadId . '.part';
    }

    /**
     * Get the info file path for an upload.
     */
    private function getInfoFilePath(string $uploadId): string
    {
        return $this->storagePath . '/' . $uploadId . '.info';
    }

    public function create(string $uploadId, ?int $length): bool
    {
        $filePath = $this->getFilePath($uploadId);
        
        // Create empty file
        $result = file_put_contents($filePath, '');
        
        if ($result === false) {
            return false;
        }

        // Store metadata in info file
        $infoPath = $this->getInfoFilePath($uploadId);
        $info = [
            'length' => $length,
            'created_at' => time(),
        ];
        
        return file_put_contents($infoPath, json_encode($info)) !== false;
    }

    public function append(string $uploadId, string $data, int $offset): int
    {
        $filePath = $this->getFilePath($uploadId);
        
        if (!file_exists($filePath)) {
            return 0;
        }

        // Open file for writing at offset
        $handle = fopen($filePath, 'c+b');
        
        if ($handle === false) {
            return 0;
        }

        try {
            // Seek to offset
            fseek($handle, $offset);
            
            // Write data
            $written = fwrite($handle, $data);
            
            // Flush to ensure data is written
            fflush($handle);
            
            return $written !== false ? $written : 0;
        } finally {
            fclose($handle);
        }
    }

    public function offset(string $uploadId): int
    {
        $filePath = $this->getFilePath($uploadId);
        
        if (!file_exists($filePath)) {
            return 0;
        }

        clearstatcache(true, $filePath);
        return (int) filesize($filePath);
    }

    public function exists(string $uploadId): bool
    {
        return file_exists($this->getFilePath($uploadId));
    }

    public function delete(string $uploadId): bool
    {
        $filePath = $this->getFilePath($uploadId);
        $infoPath = $this->getInfoFilePath($uploadId);
        
        $deleted = true;
        
        if (file_exists($filePath)) {
            $deleted = unlink($filePath);
        }
        
        if (file_exists($infoPath)) {
            $deleted = $deleted && unlink($infoPath);
        }
        
        return $deleted;
    }

    public function finish(string $uploadId): bool
    {
        $filePath = $this->getFilePath($uploadId);
        $finalPath = $this->storagePath . '/' . $uploadId;
        
        if (!file_exists($filePath)) {
            return false;
        }

        // Rename from .part to final name
        return rename($filePath, $finalPath);
    }

    public function metadata(string $uploadId, array $metadata): bool
    {
        $infoPath = $this->getInfoFilePath($uploadId);
        
        $info = [];
        if (file_exists($infoPath)) {
            $content = file_get_contents($infoPath);
            if ($content !== false) {
                $info = json_decode($content, true) ?: [];
            }
        }
        
        $info['metadata'] = $metadata;
        
        return file_put_contents($infoPath, json_encode($info)) !== false;
    }

    public function length(string $uploadId): ?int
    {
        $infoPath = $this->getInfoFilePath($uploadId);
        
        if (!file_exists($infoPath)) {
            return null;
        }

        $content = file_get_contents($infoPath);
        if ($content === false) {
            return null;
        }

        $info = json_decode($content, true);
        return $info['length'] ?? null;
    }

    public function expires(string $uploadId, \DateTimeInterface $expiresAt): bool
    {
        $infoPath = $this->getInfoFilePath($uploadId);
        
        $info = [];
        if (file_exists($infoPath)) {
            $content = file_get_contents($infoPath);
            if ($content !== false) {
                $info = json_decode($content, true) ?: [];
            }
        }
        
        $info['expires_at'] = $expiresAt->getTimestamp();
        
        return file_put_contents($infoPath, json_encode($info)) !== false;
    }

    public function getPath(string $uploadId): ?string
    {
        $filePath = $this->storagePath . '/' . $uploadId;
        
        if (file_exists($filePath)) {
            return $filePath;
        }

        $partPath = $this->getFilePath($uploadId);
        if (file_exists($partPath)) {
            return $partPath;
        }

        return null;
    }
}
