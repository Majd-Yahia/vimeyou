<?php

namespace Awesomchu\Vimeo\Requests;

use GuzzleHttp\Client;

class HttpClientFacade implements HttpClientInterface
{
    public function __construct(protected Client $client)
    {
    }

    public function sendRequest(string $method, string $uri, array $headers = [], array $body = []): array
    {
        $response = $this->client->{$method}($uri, [
            'headers' => $headers,
            'json' => $body
        ]);

        return [
            'status_code' => $response->getStatusCode(),
            'body' => $response->json()
        ];
    }
}
