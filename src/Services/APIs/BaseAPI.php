

<?php

namespace Awesomchu\Vimeo\Services\APIs;

use Awesomchu\Vimeo\Exceptions\GeneralException;
use Awesomchu\Vimeo\Services\VideoAPIInterface;
use GuzzleHttp\Client;

abstract class BaseAPI implements VideoAPIInterface
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
     * @param string
     */
    protected string $setup;

    /**
     * Constructor
     *
     * @param Client $client
     */
    public function __construct(protected Client $client)
    {
        $this->setup = config(self::PREFIX . $this->getNameSpace());
    }

     /**
     * The base endpoint to which an api is pointing at.
     *
     * 
     * @return string
     */
    abstract public function getEndPiont(): string;

    /**
     * Namespace to which service it should connect
     *
     * 
     * @return string
     */
    public function getNameSpace(): string
    {
        return str_replace('API', '', $this::class);
    }
}
