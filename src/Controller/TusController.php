<?php

declare(strict_types=1);

namespace Lazarini\HyperfTus\Controller;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PatchMapping;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Lazarini\HyperfTus\Services\TusServer;
use Lazarini\HyperfTus\DTO\ChecksumDTO;
use Lazarini\HyperfTus\Exception\ProtocolException;
use Lazarini\HyperfTus\Exception\InvalidContentTypeException;
use Lazarini\HyperfTus\Utils\MetadataHelper;

/**
 * TUS Protocol Controller.
 * Handles all TUS HTTP endpoints.
 */
#[Controller(prefix: '')]
class TusController
{
    private const TUS_VERSION = '1.0.0';
    private const CONTENT_TYPE = 'application/offset+file';

    public function __construct(
        protected readonly TusServer $tusServer,
    ) {}

    /**
     * OPTIONS - Server capabilities.
     */
    #[RequestMapping(path: '{route:.+}', methods: ['OPTIONS'])]
    public function options(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $capabilities = $this->tusServer->getCapabilities();

        return $response
            ->withStatus(204)
            ->withHeader('Tus-Resumable', self::TUS_VERSION)
            ->withHeader('Tus-Version', self::TUS_VERSION)
            ->withHeader('Tus-Extension', implode(',', $capabilities['extensions']))
            ->withHeader('Tus-Max-Size', $capabilities['max_size'] ?? '0')
            ->withHeader('Tus-Checksum-Algorithm', implode(',', $capabilities['checksum_algorithms']));
    }

    /**
     * POST - Create a new upload.
     */
    #[PostMapping(path: '{route}')]
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Parse headers
        $uploadLength = $request->getHeaderLine('Upload-Length');
        $uploadDeferLength = $request->getHeaderLine('Upload-Defer-Length');
        $uploadMetadata = $request->getHeaderLine('Upload-Metadata');
        $uploadConcat = $request->getHeaderLine('Upload-Concat');
        $uploadChecksum = $request->getHeaderLine('Upload-Checksum');

        // Parse Upload-Length
        $length = null;
        if ($uploadLength !== '') {
            $length = (int) $uploadLength;
            if ($length < 0) {
                throw new ProtocolException('Invalid Upload-Length');
            }
        }

        // Parse Upload-Defer-Length
        $deferLength = $uploadDeferLength === '1';

        // Parse Upload-Metadata
        $metadata = [];
        if ($uploadMetadata !== '') {
            $metadata = $this->tusServer->parseMetadata($uploadMetadata);
        }

        // Parse Upload-Concat
        $concatMode = null;
        $concatPartialIds = null;
        if ($uploadConcat !== '') {
            if ($uploadConcat === 'partial') {
                $concatMode = 'partial';
            } elseif (str_starts_with($uploadConcat, 'final;')) {
                $concatMode = 'final';
                $partialsStr = substr($uploadConcat, 6);
                $concatPartialIds = explode(';', $partialsStr);
            } else {
                throw new ProtocolException('Invalid Upload-Concat header');
            }
        }

        // Parse Upload-Checksum
        $checksum = null;
        if ($uploadChecksum !== '') {
            try {
                $checksum = ChecksumDTO::fromHeader($uploadChecksum);
            } catch (\InvalidArgumentException $e) {
                throw new ProtocolException('Invalid Upload-Checksum header format');
            }
        }

        // Create upload
        $upload = $this->tusServer->create(
            length: $length,
            metadata: $metadata,
            deferLength: $deferLength,
            concatMode: $concatMode,
            concatPartialIds: $concatPartialIds,
            checksum: $checksum
        );

        // Build response
        $baseUrl = (string) $request->getUri()->withPath('')->withQuery('');
        $location = $this->tusServer->getLocation($upload->id, $baseUrl);

        $response = $response
            ->withStatus(201)
            ->withHeader('Location', $location)
            ->withHeader('Tus-Resumable', self::TUS_VERSION)
            ->withHeader('Upload-Offset', '0');

        if ($upload->length !== null) {
            $response = $response->withHeader('Upload-Length', (string) $upload->length);
        }

        if (!empty($upload->metadata)) {
            $response = $response->withHeader('Upload-Metadata', $this->tusServer->encodeMetadata($upload->metadata));
        }

        return $response;
    }

    /**
     * HEAD - Get upload information.
     */
    #[GetMapping(path: '{route}/{id}')]
    public function head(ServerRequestInterface $request, ResponseInterface $response, string $id): ResponseInterface
    {
        $upload = $this->tusServer->getUploadInfo($id);

        $response = $response
            ->withStatus(200)
            ->withHeader('Tus-Resumable', self::TUS_VERSION)
            ->withHeader('Upload-Offset', (string) $upload->offset)
            ->withHeader('Content-Type', self::CONTENT_TYPE);

        if ($upload->length !== null) {
            $response = $response->withHeader('Upload-Length', (string) $upload->length);
        }

        if (!empty($upload->metadata)) {
            $response = $response->withHeader('Upload-Metadata', $this->tusServer->encodeMetadata($upload->metadata));
        }

        if ($upload->isComplete()) {
            $response = $response->withHeader('Upload-Complete', 'true');
        }

        if ($upload->expiresAt !== null) {
            $response = $response->withHeader(
                'Upload-Expires',
                $upload->expiresAt->format('D, d M Y H:i:s T')
            );
        }

        return $response;
    }

    /**
     * PATCH - Append data to upload.
     */
    #[PatchMapping(path: '{route}/{id}')]
    public function patch(ServerRequestInterface $request, ResponseInterface $response, string $id): ResponseInterface
    {
        // Validate Content-Type
        $contentType = $request->getHeaderLine('Content-Type');
        if ($contentType !== self::CONTENT_TYPE) {
            throw new InvalidContentTypeException(
                'Content-Type must be ' . self::CONTENT_TYPE
            );
        }

        // Parse Upload-Offset
        $offset = $request->getHeaderLine('Upload-Offset');
        if ($offset === '') {
            throw new ProtocolException('Missing Upload-Offset header');
        }
        $offset = (int) $offset;

        // Parse Upload-Checksum (optional)
        $checksum = null;
        $uploadChecksum = $request->getHeaderLine('Upload-Checksum');
        if ($uploadChecksum !== '') {
            try {
                $checksum = ChecksumDTO::fromHeader($uploadChecksum);
            } catch (\InvalidArgumentException $e) {
                throw new ProtocolException('Invalid Upload-Checksum header format');
            }
        }

        // Get request body
        $body = $request->getBody()->getContents();

        // Patch upload
        $bytesWritten = $this->tusServer->patch($id, $body, $offset, $checksum);

        // Get updated upload info
        $upload = $this->tusServer->getUploadInfo($id);

        // Build response
        $response = $response
            ->withStatus(204)
            ->withHeader('Tus-Resumable', self::TUS_VERSION)
            ->withHeader('Upload-Offset', (string) $upload->offset);

        if ($upload->length !== null && $upload->isComplete()) {
            $response = $response->withHeader('Upload-Complete', 'true');
        }

        return $response;
    }

    /**
     * DELETE - Cancel upload.
     */
    #[DeleteMapping(path: '{route}/{id}')]
    public function delete(ServerRequestInterface $request, ResponseInterface $response, string $id): ResponseInterface
    {
        $this->tusServer->delete($id);

        return $response
            ->withStatus(204)
            ->withHeader('Tus-Resumable', self::TUS_VERSION);
    }

    /**
     * GET - Optional upload information endpoint.
     */
    #[GetMapping(path: '{route}/info/{id}')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, string $id): ResponseInterface
    {
        $upload = $this->tusServer->getUploadInfo($id);

        $data = [
            'id' => $upload->id,
            'length' => $upload->length,
            'offset' => $upload->offset,
            'metadata' => $upload->metadata,
            'completed' => $upload->isComplete(),
            'created_at' => $upload->createdAt->format(\DateTimeInterface::ATOM),
            'updated_at' => $upload->updatedAt->format(\DateTimeInterface::ATOM),
        ];

        if ($upload->expiresAt !== null) {
            $data['expires_at'] = $upload->expiresAt->format(\DateTimeInterface::ATOM);
        }

        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Tus-Resumable', self::TUS_VERSION)
            ->withBody(new \Hyperf\HttpMessage\Stream\SwooleStream(json_encode($data)));
    }
}
