<?php

use Awesomchu\Vimeo\Exceptions\GeneralException;
use Illuminate\Http\Response;

if (!function_exists('getFileInfo')) {
    /**
     * Get file information for resumable uploads
     *
     * @param string $filePath The path to the file
     * @param int $chunkSize The size of each chunk
     * @param int $offset The offset for resuming the upload (default: 0)
     * @param string $mode The file mode (default: 'rb')
     * 
     * @return array File information including stream, size, and total chunks
     * @throws GeneralException
     */
    function getFileInfo(string $filePath, int $chunkSize, int $offset = 0, string $mode = 'rb'): array
    {
        $stream = fopen($filePath, $mode);
        if (!$stream) {
            throw new GeneralException(message: 'Could not open the file.', code: Response::HTTP_NOT_FOUND);
        }

        fseek($stream, $offset);
        $fileSize = filesize($filePath) - $offset;
        $totalChunks = ceil($fileSize / $chunkSize);

        return [
            'stream' => $stream,
            'size' => $fileSize,
            'chunks' => $totalChunks,
        ];
    }
}

if (!function_exists('getChunkOffset')) {
    /**
     * Get the chunk and offset information for resumable uploads
     *
     * @param resource $stream The file stream obtained from get_file_info()
     * @param int $currentChunk The current chunk number
     * @param int $chunkSize The size of each chunk
     * 
     * @return array The chunk data and offset
     */
    function getChunkOffset($stream, int $currentChunk, int $chunkSize): array
    {
        $chunk = fread($stream, $chunkSize);
        $offset = $currentChunk * $chunkSize;

        return [
            'chunk' => $chunk,
            'offset' => $offset,
        ];
    }
}

