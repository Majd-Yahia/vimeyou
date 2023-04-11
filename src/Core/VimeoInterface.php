<?php

namespace Awesomchu\Vimeo\Core;

interface VimeoInterface
{    
    /**
     * Upload Video
     *
     * @param  array $data
     * @param  array $options
     * @param  string $approach
     * @return mixed
     */
    public function uploadVideo(array $data, array $options, string $approach): mixed;
}
