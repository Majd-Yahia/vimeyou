<?php

namespace Awesomchu\Vimeo\Services;

use Awesomchu\Vimeo\Exceptions\GeneralException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;

class ClientService extends Client
{
    /**
     * Prefix for configuration
     *
     * @param string ALLOWED_METHODS
     */
    private const ALLOWED_METHODS = ['get', 'post', 'put', 'delete', 'patch'];

    /**
     * Set Method of the request
     *
     * @param string $method
     */
    private string $method;

    /**
     * Set URI for of request
     *
     * @param string $uri
     */
    private string $uri;

    /**
     * Set the body of the request
     *
     * @param string $body
     */
    private array $body;

    /**
     * Set the headers of the request
     *
     * @param string $headers
     */
    private array $headers;

    /**
     * Constructor
     *
     * @param Client $client
     */
    public function __construct(protected Client $client)
    {
    }

    public static function create(): self
    {
        return new self;
    }

    public function setURI(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    public function setMethod(string $method): self
    {
        if (!in_array(strtolower($method), self::ALLOWED_METHODS)) {
            throw new GeneralException(message: 'Method should be of the following type: ' . implode(', ', self::ALLOWED_METHODS));
        }

        $this->method = $method;
        return $this;
    }

    public function setHeaders(array $headers = []): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function setBoody(array $body = []): self
    {
        $this->body = $body;
        return $this;
    }

    public function call()
    {
        try {
            $attempt = $this->client->{$this->method}($this->uri, [
                'headers' => $this->headers,
                'json' => $this->body
            ]);

            return json_decode($attempt->getBody(), true);
        } catch (RequestException $e) {
            throw new GeneralException(message: $e->getMessage(), code: $e->getCode());
        }
    }


    // TODO:: Add events for the streaming
    /**
     * Stream the file in chunks and upload it to Vimeo.
     *
     * @param string $uri The Vimeo upload URI.
     * @param string $filePath The path to the file to be uploaded.
     * @param int $chunkSize The size of each chunk.
     * @param int $offset The number of bytes already uploaded (default: 0).
     * @throws GeneralException
     * @return array The upload status and progress.
     */
    public function streamFile(string $uri, string $filePath, int $chunkSize, int $offset = 0): array
    {
        $log = '';
        $progress = 0;
        $status = 'error';
        $bytesUploaded = $offset;

        $fileInfo = getFileInfo($filePath, $chunkSize, $offset);
        $stream = $fileInfo['stream'];

        try {
            for ($currentChunk = 0; $currentChunk < $fileInfo['chunks']; $currentChunk++) {
                $chunkData = getChunkOffset($stream, $currentChunk, $chunkSize);
                $this->client->patch($uri, [
                    'headers' => array_merge($this->headers, [
                        'Content-Type' => 'application/offset+octet-stream',
                        'Upload-Offset' => $chunkData['offset'],
                        'Tus-Resumable' => '1.0.0',
                    ]),
                    'body' => \GuzzleHttp\Psr7\Utils::streamFor($chunkData['chunk']),
                ]);

                $bytesUploaded += $chunkSize;
                $progress = round($bytesUploaded / $fileInfo['size'] * 100);
            }

            $status = 'success';
        } catch (RequestException $exception) {
            $log = $exception;
        } finally {
            fclose($stream); // Close the file stream to free memory.
        }

        return [
            'status' => $status,
            'size' => $fileInfo['size'],
            'total_chunks' => $fileInfo['chunks'],
            'bytes_uploaded' => $bytesUploaded,
            'progress' => $progress,
            'offset' => [
                'start_offset' => $offset,
                'last_offset' => $chunkData['offset'],
            ],
            'log' => $log,
        ];
    }
}
