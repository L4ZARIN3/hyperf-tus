<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus;

use Lazarini\HyperfTus\Command\CleanCommand;
use Lazarini\HyperfTus\Controller\TusController;
use Lazarini\HyperfTus\Middleware\TusMiddleware;

/**
 * ConfigProvider for Hyperf auto-discovery.
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'commands' => $this->getCommands(),
            'annotations' => $this->getAnnotations(),
            'publish' => $this->getPublish(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            \Lazarini\HyperfTus\Contracts\StorageDriverInterface::class => \Lazarini\HyperfTus\Storage\Drivers\LocalStorageDriver::class,
            \Lazarini\HyperfTus\Contracts\UploadRepositoryInterface::class => \Lazarini\HyperfTus\Repository\Implementations\FilesystemRepository::class,
            \Lazarini\HyperfTus\Services\TusServer::class => \Lazarini\HyperfTus\Services\TusServer::class,
        ];
    }

    public function getCommands(): array
    {
        return [
            CleanCommand::class,
        ];
    }

    public function getAnnotations(): array
    {
        return [
            'scan' => [
                'paths' => [
                    __DIR__ . '/Controller',
                    __DIR__ . '/Middleware',
                    __DIR__ . '/Listener',
                    __DIR__ . '/Command',
                ],
            ],
        ];
    }

    public function getPublish(): array
    {
        return [
            [
                'id' => 'config',
                'description' => 'TUS configuration file',
                'source' => __DIR__ . '/publish/tus.php',
                'destination' => 'config/autoload/tus.php',
            ],
        ];
    }
}
