<?php

namespace Awesomchu\Vimeo\Services;

use Awesomchu\Vimeo\Exceptions\GeneralException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;

class ClientService extends Client
{
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
    public function streamFile(string $uri, string $filePath, int $chunkSize, int $offset = 0, array $headers = []): array
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
                $this->patch($uri, [
                    'headers' => array_merge($headers, [
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
