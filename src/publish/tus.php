<?php

declare(strict_types=1);

/**
 * TUS Protocol Configuration for Hyperf.
 */
return [
    /**
     * Base route path for TUS uploads.
     */
    'route' => '/files',

    /**
     * Storage driver: 'local', 'memory', or custom driver class.
     */
    'driver' => 'local',

    /**
     * Path where uploaded files will be stored (for local driver).
     */
    'storage_path' => BASE_PATH . '/runtime/tus',

    /**
     * Upload expiration time in seconds.
     * Null means no expiration.
     */
    'expiration' => 86400,

    /**
     * Maximum chunk size per PATCH request.
     * Null means no limit (client decides).
     */
    'chunk_size' => null,

    /**
     * Maximum total upload size in bytes.
     * Null means no limit.
     */
    'max_size' => null,

    /**
     * Repository implementation for upload metadata persistence.
     * Options: 'filesystem', 'redis', 'database', or custom class.
     */
    'repository' => 'filesystem',

    /**
     * Repository configuration.
     */
    'repository_config' => [
        'filesystem' => [
            'path' => BASE_PATH . '/runtime/tus/uploads',
        ],
        'redis' => [
            'connection' => 'default',
            'prefix' => 'tus:',
        ],
        'database' => [
            'connection' => 'default',
            'table' => 'tus_uploads',
        ],
    ],

    /**
     * Supported checksum algorithms.
     */
    'checksum_algorithms' => ['sha1', 'md5', 'sha256'],

    /**
     * Enable/disable specific TUS extensions.
     */
    'extensions' => [
        'creation' => true,
        'creation_with_upload' => true,
        'checksum' => true,
        'expiration' => true,
        'concatenation' => true,
        'termination' => true,
    ],
];
