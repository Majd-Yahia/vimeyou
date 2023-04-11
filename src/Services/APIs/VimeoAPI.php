<?php

namespace Awesomchu\Vimeo\Services\APIs;

use Awesomchu\Vimeo\Services\VideoAPIInterface;

class VimeoAPI implements VideoAPIInterface
{
    public function __construct()
    {
    }

     /**
     * Get a list of videos from the specified channel
     *
     * @param int|string $identifier The ID of the channel to get videos from
     * @param int $maxResults The maximum number of results to return
     * @return array An array of video objects
     */
    public function getVideos(int|string $identifier, int $maxResults = 10)
    {
    }

    /**
     * Get details for a specific video
     *
     * @param int|string $videoId The ID of the video to get details for
     * @return object A video object with details
     */
    public function getVideoDetails(int|string $videoId)
    {
    }

    /**
     * Search for a specific video on platforms.
     *
     * @param string $query The query to search in the platform
     * @param int $maxResults max results
     * @return array An array of video objects
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
     * @return Response $response of the transaction made.
     */
    public function uploadVideo(string $filePath, string $title, string $description)
    {
    }
}
