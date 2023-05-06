<?php
namespace Awesomchu\Vimeo\Core;

use Awesomchu\Vimeo\Core\Interface\VideoInterface;
use Awesomchu\Vimeo\Exceptions\GeneralException;
use Awesomchu\Vimeo\Services\ClientService;
use GuzzleHttp\Client;

abstract class BasePlatform implements VideoInterface
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
    public function __construct(protected ClientService $client)
    {
        $this->setup = config(self::PREFIX . '.' . $this->getNameSpace());

        $client->setURI($this->getEndPiont());
    }

    /**
     * Namespace to which service it should connect
     *
     * @return string
     */
    public function getNameSpace(): string
    {
        return strtolower(class_basename($this::class));
    }

    /**
     * The base endpoint to which an api is pointing at.
     *
     * @return string
     */
    abstract public function getEndPiont(): string;
}
