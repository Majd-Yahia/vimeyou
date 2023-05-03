<?php
namespace Awesomchu\Vimeo\Core\Platforms;

use Awesomchu\Vimeo\Services\APIs\BasePlatform;

class Vimeo extends BasePlatform
{
    /**
     * The base endpoint to which an api is pointing at.
     *
     * @return string
     */
    public function getEndPiont(): string
    {
        return "https://api.vimeo.com/";
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
    public function uploadVideo(string $filePath, string $title, string $description)
    {

        dd($this, $this->setup);
        
        /*
            Vimeo steps needed to upload a video:
            1. Generate the required data for the video placeholder
                - Size
                - Approach
            2. Send an api to reserve a placeholder for the video that returns the video ID
            3. Tag the video if you want.
            4. Start uploading the video (Via the different approaches)
        */
    }
}
