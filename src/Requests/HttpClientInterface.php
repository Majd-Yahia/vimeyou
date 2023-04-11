<?php

namespace Awesomchu\Vimeo\Requests;

interface HttpClientInterface
{
    public function sendRequest(string $method, string $url, array $options = []): array;
}
