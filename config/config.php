<?php

/*
 * You can place your custom package configuration in here.
 */
return [

    /*
    * Vimeo: Contains Credentials
    * =======================================================
    * This is where vimeo credintials are added.
    * You can visit their website to create a user
    * and grab those values from
    * https://vimeo.com | See their documentation for more information https://developer.vimeo.com/api/guides/start
    * IMPORTANT: if you want to use this package and have not provided any credentials the VideoService provider will return an exceptions
    */
    "vimeo" => [
        "client_id" => env("VIMEO_CLIENT_ID", null),
        "client_secret" => env("VIMEO_CLIENT_SECRET", null),
        "client_access" => env("VIMEO_CLIENT_ACCESS", null),
    ],

    // TODO:: Add the correct credentials to youtube and change the description to match youtube.
    /*
    * Youtube: Contains Credentials
    * =======================================================
    * This is where youtube credintials are added.
    * You can visit their website to create a user
    * and grab those values from
    *
    * IMPORTANT: if you want to use this package and have not provided any credentials the VideoService provider will return an exceptions
    */
    "youtube" => [
        "client_id" => env("VIMEO_CLIENT_ID", null),
        "client_secret" => env("VIMEO_CLIENT_SECRET", null),
        "client_access" => env("VIMEO_CLIENT_ACCESS", null),
    ],

    /*
    * Upload Progress: Controls if the uploaded video returns progress as an SSE (Server Side Event)
    * =======================================================
    * This configuration option enables the return of the progress or percentage of a video being uploaded to any supported platform. 
    * The default value is false, which means that the progress tracking feature is disabled. 
    * If you set it to true, the package will periodically check the upload progress of the video and return 
    * the progress/percentage as part of the upload response. 
    * You can use this information to display a progress bar or status indicator to the user during the upload process.
    * However, note that enabling progress tracking may affect the upload performance and increase the number of API requests to the platform.
    */
    "upload_progress" => env('UPLOAD_PROGRESS', false),

    /*
    * Chunk: Controls the chunking of a video.
    * =======================================================
    * This configuration option determines the chunk size (in bytes) for uploading videos in the package. 
    * The package uses chunked upload to upload large video files in multiple smaller chunks, 
    * and this configuration value sets the size of each chunk. The default value is 5 MB (5 * 1024 * 1024 bytes). 
    * You can adjust this value to optimize the upload performance based on your specific use case and network conditions.
    */
    "chunk" => env('UPLOAD_CHUNKS', 5 * 1024 * 1024),
];
