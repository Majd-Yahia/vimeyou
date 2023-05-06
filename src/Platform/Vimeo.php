<?php

namespace Awesomchu\Vimeo\Platform;

use Awesomchu\Vimeo\Exceptions\GeneralException;
use Awesomchu\Vimeo\Platform\Interface\VideoInterface;
use Awesomchu\Vimeo\Platform\Jobs\StreamVideoJob;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

class Vimeo implements VideoInterface
{
    /**
     * Prefix for configuration
     *
     * @param string
     */
    protected const PREFIX = 'vimeoyou';

    /**
     * Setup (contains configuration)
     *
     * @param array
     */
    protected array $setup;

    /**
     * Constructor
     *
     * @param ClientService $client
     */
    public function __construct(protected Client $client)
    {
        /**
         * Authentication for vimeo must be in services
         */
        $this->setup = config('services.vimeo');
    }

    /**
     * Get a list of videos from the specified channel
     *
     * @param int|string $identifier The ID of the channel to get videos from
     * @param int $maxResults The maximum number of results to return
     * 
     * @return array An array of video objects
     * 
     * @throws GeneralException
     */
    public function getVideos(int|string $identifier, int $maxResults = 10)
    {
    }

    /**
     * Get details for a specific video
     *
     * @param int|string $videoId The ID of the video to get details for
     * 
     * @return object A video object with details
     * 
     * @throws GeneralException
     */
    public function getVideoDetails(int|string $videoId)
    {
    }

    /**
     * Search for a specific video on platforms.
     *
     * @param string $query The query to search in the platform
     * @param int $maxResults max results
     * 
     * @return array An array of video objects
     * 
     * @throws GeneralException
     */
    public function searchVideos(string $query, int $maxResults = 10)
    {
    }

    /**
     * Upload a video to the platform
     *
     * @param string $filePath the location of the file on the system
     * @param string $title the title of the video uploaded
     * @param string $description the description of the video.
     * 
     * @return Response $response of the transaction made.
     * 
     * @throws GeneralException
     */
    public function uploadVideo(string $filePath, string $title, string $description, $options = [])
    {
        // Step 1: Generate required data for the video placeholder
        $body = array_merge([
            'name' => $title,
            'description' => $description,
            'upload' => [
                'size' => Storage::size($filePath),
                'approach' => 'tus',
            ]
        ], $options);

        $headers = ['Content-Type' => 'application/json'];

        // Step 2: Send an API request to reserve a placeholder for the video and get the video ID
        $response = $this->createVideoPlaceHolder(body: $body, headers: $headers);

        // Step 4: Start uploading the video (streaming)
        dd($response);
        $uri = $chunkSize = $offset = 0;

        // Create an instance of the StreamVideoJob
        $streamVideoJob = new StreamVideoJob($uri, $filePath, $chunkSize, $offset);

        // Dispatch the job to the queue system for background processing
        dispatch($streamVideoJob);
    }

    private function createVideoPlaceHolder(array $body = [], $headers = [])
    {
        try {
            // Send a POST request to the Vimeo API to create a new video placeholder
            $attempt = $this->client->post($this->setup['uri'] . 'me/videos', [
                "headers" => array_merge($headers),
                'json' => $body,
            ]);

            // Get the upload link from the response
            return json_decode($attempt->getBody(), true);
        } catch (RequestException $e) {
            throw new GeneralException(message: $e->getMessage(), code: $e->getCode());
        }
    }
}
