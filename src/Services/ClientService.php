<?php

namespace Awesomchu\Vimeo\Services;

use Awesomchu\Vimeo\Exceptions\GeneralException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;

class ClientService extends Client
{
    /**
     * Prefix for configuration
     *
     * @param string ALLOWED_METHODS
     */
    private const ALLOWED_METHODS = ['get', 'post', 'put', 'delete', 'patch'];

    /**
     * Set Method of the request
     *
     * @param string $method
     */
    private string $method;

    /**
     * Set URI for of request
     *
     * @param string $uri
     */
    private string $uri;

    /**
     * Set the body of the request
     *
     * @param string $body
     */
    private array $body;

    /**
     * Set the headers of the request
     *
     * @param string $headers
     */
    private array $headers;

    /**
     * Constructor
     *
     * @param Client $client
     */
    public function __construct(protected Client $client)
    {
    }

    public static function create(): self
    {
        return new self;
    }

    public function setURI(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    public function setMethod(string $method): self
    {
        if(!in_array(strtolower($method), self::ALLOWED_METHODS))
        {
            throw new GeneralException(message: 'Method should be of the following type: ' . implode(', ', self::ALLOWED_METHODS));
        }

        $this->method = $method;
        return $this;
    }

    public function setHeaders(array $headers = []): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function setBoody(array $body = []): self
    {
        $this->body = $body;
        return $this;
    }

    public function call() 
    {
        try {
            $attempt = $this->client->{$this->method}($this->uri, [
                'headers' => $this->headers,
                'json' => $this->body
            ]);

            return json_decode($attempt->getBody(), true);
        } catch (RequestException $e) {
            throw new GeneralException(message: $e->getMessage(), code: $e->getCode());
        }
    }
}
