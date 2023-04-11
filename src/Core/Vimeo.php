<?php

namespace Awesomchu\Vimeo\Core;

use Awesomchu\Vimeo\Core\VimeoInterface;
use Awesomchu\Vimeo\Events\UploadFinishedEvent;
use Awesomchu\Vimeo\Events\UploadProgressEvent;
use Awesomchu\Vimeo\Exceptions\VimeoConfigurationNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Awesomchu\Vimeo\Events\UploadVideoEvent;
use Awesomchu\Vimeo\Exceptions\VimeoGeneralException;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

class Vimeo implements VimeoInterface
{
    protected $domains = [
        'localhost'
    ];

    /**
     * endPoint
     *
     * @var string
     */
    protected $endPoint = "https://api.vimeo.com/";

    /**
     * headers
     *
     * @var array
     */
    protected $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/vnd.vimeo.*+json;version=3.4'
    ];

    /**
     * options
     *
     * @var array
     */
    protected $options = [
        // 'privacy' => [
        //     "view" => "unlisted"
        // ],
    ];

    /**
     * defaultUpload
     *
     * @var array
     */
    protected $defaultUpload;

    /**
     * config
     *
     * @var array
     */
    protected $config = [];

    /**
     * __construct
     *
     * @return void
     * @throws VimeoGeneralException
     */
    public function __construct(protected Client $client)
    {
        $this->config = config('vimeo.connection');
        if (!isset($this->config['client_access'])) {
            throw new VimeoGeneralException(message: 'Configurations not found!', code: Response::HTTP_NOT_ACCEPTABLE);
        }

        $this->headers["Authorization"] = 'Bearer ' . $this->config['client_access'];
    }

    public function TagVideo($id, $tag = 'temp')
    {
        try {
            return $this->client->put($this->endPoint . "videos/$id/tags", [
                "headers" => $this->headers,
                'json' => ['name' => $tag]
            ]);
        } catch (RequestException $e) {
            throw new VimeoGeneralException(message: $e->getMessage(), code: $e->getCode());
        }
    }

    public function uploadVideo(array $data, array $options = [], string $approach = 'tus'): mixed
    {
        // File
        $file = $data['file'];

        // File Size
        $this->defaultUpload = [
            'size' => $file->getSize(),
            'approach' => $approach,
        ];

        $body = array_merge(
            [
                'name' => $data['name'] ?? $file->getClientOriginalName(),
                'description' => $data['description'] ?? "",
                'upload' => $this->defaultUpload
            ],
            (empty($options) ? $this->options : $options)
        );

        // Setup Data.
        $data['options'] = $options;
        $data['response'] = $this->_createPlaceHolder(body: $body);
        $videoId = last(explode('/', $data['response']['uri']));

        $this->tags($videoId, 'put', 'temp');

        // $this->_whitelist_domain($data['response']['uri']);
        event(new UploadVideoEvent(approach: $approach, data: $data));


        return $videoId;
    }

    public function getVideoDetails(string|int $video)
    {
        return $this->client_call(
            method: 'get',
            uri: $this->endPoint . "videos/$video",
        );
    }

    public function deleteVideo(string|int $video)
    {
        return $this->client_call(
            method: 'delete',
            uri: $this->endPoint . "videos/$video",
        );
    }

    /**
     * PUT | Get Tags to/from a video
     *
     * @param  string|int $video
     * @param  string $method = GET | PUT
     * @param  string $tags
     *
     * Tags needs to be a comma seperated value ex: tag1,tag2,...etc
     * @reference https://developer.vimeo.com/api/reference/videos#add_video_tags
     *
     * @return void
     */
    public function tags(string|int $video, string $method = 'put', string $tag = "")
    {
        if (Str::contains('delete', $method)) {
            return $this->client_call(
                method: 'delete',
                uri: $this->endPoint . "videos/$video/tags/$tag",
            );
        }

        return $this->client_call(
            method: $method,
            uri: $this->endPoint . "videos/$video/tags",
            headers: [],
            body: collect(explode(',', $tag))->map(function ($item) {
                return ["name" => $item];
            })->toArray(),
        );
    }


    /**
     * updateVideo
     *
     * @param  string|int $video
     * @param  array $body
     * Checkout the reference to find more about the body content
     * @reference https://developer.vimeo.com/api/reference/videos#edit_video
     *
     * @return array
     */
    public function updateVideo(string|int $video, array $body, array $headers = [])
    {
        return $this->client_call(
            method: 'patch',
            uri: $this->endPoint . "videos/$video",
            headers: $headers,
            body: $body,
        );
    }

    /**
     * Thumbnail (POST | GET | DELETE | EDIT)
     *
     * @param  string|int $video
     * @param  array $body
     * Checkout the reference to find more about the body content
     * @reference https://developer.vimeo.com/api/reference/response/picture
     *
     * @return array
     */
    public function thumbnail(string|int $video, string $method = 'post', array $body, array $headers = [])
    {
        return $this->client_call(
            method: 'post',
            uri: $this->endPoint . "videos/$video/pictures",
            headers: $headers,
            body: $body,
        );
    }

    public function upload_via_tus(array $data)
    {
        // 5 MB default
        $chunk_size = config('vimeo.chunk');
        $uri = $data['response']['upload']['upload_link'];

        $total_bytes = filesize($data['file']);
        $bytes_uploaded = 0;

        try {
            // Open the video file for reading
            $stream = fopen($data['file'], 'rb');
            if (!$stream) {
                throw new VimeoGeneralException(message: 'Could not open video file', code: Response::HTTP_NOT_FOUND);
            }

            // Calculate the number of chunks and the total file size
            $file_size = filesize($data['file']);
            $numChunks = ceil($file_size / $chunk_size);

            // Loop through each chunk of the file and send it to Vimeo
            for ($i = 0; $i < $numChunks; $i++) {
                // Read the next chunk from the file
                $chunk = fread($stream, $chunk_size);
                $offset = $i * $chunk_size;

                // Send a PUT request to Vimeo with the chunk data
                $response = $this->client->patch($uri, [
                    'headers' => array_merge($this->headers, [
                        'Content-Type' => 'application/offset+octet-stream',
                        'Upload-Offset' => $offset,
                        'Tus-Resumable' => '1.0.0',
                    ]),
                    'body' =>  \GuzzleHttp\Psr7\Utils::streamFor($chunk),
                ]);

                // Increment the number of bytes uploaded
                $bytes_uploaded += $chunk_size;

                // Calculate the progress as a percentage
                $progress = round($bytes_uploaded / $total_bytes * 100);

                // Log the progress
                \Log::debug("Progress: $progress%");
                \Log::debug("Response: ");
                \Log::debug(json_encode($response));

                event(new UploadProgressEvent(progress: $progress));
            }

            event(new UploadFinishedEvent(status: true));

            // Close the video file
            fclose($stream);
        } catch (RequestException $e) {
            event(new UploadFinishedEvent(status: false));
            throw new VimeoGeneralException(message: $e->getMessage(), code: $e->getCode());
        }

        return 'success';
    }

    private function _createPlaceHolder(array $body = [])
    {
        try {
            // Send a POST request to the Vimeo API to create a new video placeholder
            $attempt = $this->client->post($this->endPoint . 'me/videos', [
                "headers" => $this->headers,
                'json' => $body,
            ]);

            // Get the upload link from the response
            return json_decode($attempt->getBody(), true);
        } catch (RequestException $e) {
            throw new VimeoGeneralException(message: $e->getMessage(), code: $e->getCode());
        }
    }

    private function _whitelist_domain(string $videoURI)
    {
        $uri = "https://api.vimeo.com$videoURI/privacy/domains/";

        foreach ($this->domains as $domain) {
            $attempt = $this->client->put($uri . $domain);
            dd($attempt);
        }
    }

    private function client_call(string $method, string $uri, array $headers = [], array $body = [])
    {
        try {
            $attempt = $this->client->{$method}($uri, [
                'headers' => array_merge($this->headers, $headers),
                'json' => $body
            ]);

            return json_decode($attempt->getBody(), true);
        } catch (RequestException $e) {
            throw new VimeoGeneralException(message: $e->getMessage(), code: $e->getCode());
        }
    }
}
