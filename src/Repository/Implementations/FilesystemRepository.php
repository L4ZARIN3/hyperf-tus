<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Repository\Implementations;

use Lazarini\HyperfTus\Contracts\UploadRepositoryInterface;
use Hyperf\Contract\ConfigInterface;

/**
 * Filesystem-based upload repository.
 * Stores upload metadata in JSON files.
 */
class FilesystemRepository implements UploadRepositoryInterface
{
    private string $storagePath;

    public function __construct(
        private readonly ConfigInterface $config
    ) {
        $tusConfig = $config->get('tus', []);
        $repoConfig = $tusConfig['repository_config']['filesystem'] ?? [];
        $this->storagePath = rtrim($repoConfig['path'] ?? BASE_PATH . '/runtime/tus/uploads', '/');
        
        // Ensure storage directory exists
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
    }

    /**
     * Get the file path for an upload record.
     */
    private function getFilePath(string $id): string
    {
        return $this->storagePath . '/' . $id . '.json';
    }

    public function create(
        string $id,
        ?int $length,
        array $metadata = [],
        ?\DateTimeInterface $expiresAt = null
    ): bool {
        if ($this->exists($id)) {
            return false;
        }

        $data = [
            'id' => $id,
            'length' => $length,
            'offset' => 0,
            'metadata' => $metadata,
            'expires_at' => $expiresAt?->getTimestamp(),
            'completed' => false,
            'created_at' => time(),
            'updated_at' => time(),
            'final_id' => null,
            'partial_ids' => [],
        ];

        return file_put_contents(
            $this->getFilePath($id),
            json_encode($data, JSON_PRETTY_PRINT)
        ) !== false;
    }

    public function find(string $id): ?array
    {
        $filePath = $this->getFilePath($id);
        
        if (!file_exists($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            return null;
        }

        // Convert timestamps to DateTime objects for consistency
        if (isset($data['expires_at']) && is_int($data['expires_at'])) {
            $data['expires_at_object'] = new \DateTimeImmutable('@' . $data['expires_at']);
        }
        if (isset($data['created_at']) && is_int($data['created_at'])) {
            $data['created_at_object'] = new \DateTimeImmutable('@' . $data['created_at']);
        }
        if (isset($data['updated_at']) && is_int($data['updated_at'])) {
            $data['updated_at_object'] = new \DateTimeImmutable('@' . $data['updated_at']);
        }

        return $data;
    }

    public function updateOffset(string $id, int $offset): bool
    {
        $data = $this->find($id);
        
        if ($data === null) {
            return false;
        }

        $data['offset'] = $offset;
        $data['updated_at'] = time();

        return file_put_contents(
            $this->getFilePath($id),
            json_encode($data, JSON_PRETTY_PRINT)
        ) !== false;
    }

    public function updateLength(string $id, int $length): bool
    {
        $data = $this->find($id);
        
        if ($data === null) {
            return false;
        }

        $data['length'] = $length;
        $data['updated_at'] = time();

        return file_put_contents(
            $this->getFilePath($id),
            json_encode($data, JSON_PRETTY_PRINT)
        ) !== false;
    }

    public function markCompleted(string $id): bool
    {
        $data = $this->find($id);
        
        if ($data === null) {
            return false;
        }

        $data['completed'] = true;
        $data['updated_at'] = time();

        return file_put_contents(
            $this->getFilePath($id),
            json_encode($data, JSON_PRETTY_PRINT)
        ) !== false;
    }

    public function delete(string $id): bool
    {
        $filePath = $this->getFilePath($id);
        
        if (!file_exists($filePath)) {
            return false;
        }

        return unlink($filePath);
    }

    public function exists(string $id): bool
    {
        return file_exists($this->getFilePath($id));
    }

    public function getExpired(): array
    {
        $expired = [];
        $now = time();
        
        $files = glob($this->storagePath . '/*.json');
        
        if ($files === false) {
            return [];
        }

        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $data = json_decode($content, true);
            if (!is_array($data)) {
                continue;
            }

            // Check if expired
            if (isset($data['expires_at']) && is_int($data['expires_at'])) {
                if ($data['expires_at'] < $now && !$data['completed']) {
                    $expired[] = $data['id'];
                }
            }
        }

        return $expired;
    }

    public function getPartials(string $finalId): array
    {
        $data = $this->find($finalId);
        
        if ($data === null) {
            return [];
        }

        return $data['partial_ids'] ?? [];
    }

    public function setConcatParts(string $finalId, array $partialIds): bool
    {
        $data = $this->find($finalId);
        
        if ($data === null) {
            return false;
        }

        $data['partial_ids'] = $partialIds;
        $data['updated_at'] = time();

        // Also update each partial to reference this final ID
        foreach ($partialIds as $partialId) {
            $partialData = $this->find($partialId);
            if ($partialData !== null) {
                $partialData['final_id'] = $finalId;
                $partialData['updated_at'] = time();
                file_put_contents(
                    $this->getFilePath($partialId),
                    json_encode($partialData, JSON_PRETTY_PRINT)
                );
            }
        }

        return file_put_contents(
            $this->getFilePath($finalId),
            json_encode($data, JSON_PRETTY_PRINT)
        ) !== false;
    }

    public function cleanExpired(int $olderThan): int
    {
        $count = 0;
        $files = glob($this->storagePath . '/*.json');
        
        if ($files === false) {
            return 0;
        }

        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $data = json_decode($content, true);
            if (!is_array($data)) {
                continue;
            }

            // Check if expired
            if (isset($data['expires_at']) && is_int($data['expires_at'])) {
                if ($data['expires_at'] < $olderThan) {
                    if (unlink($file)) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }
}
