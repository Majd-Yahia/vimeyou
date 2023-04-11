<?php

namespace Awesomchu\Vimeo\Exceptions;

use Exception;
use Illuminate\Http\Response;

class GeneralException extends Exception
{
    /**
     * @param  array  $data
     * @param  string  $message
     * @param  int|string  $code
     */
    public function __construct(
        protected array $data = [],
        string $message = 'Unknown Error !',
        int|string $code = Response::HTTP_INTERNAL_SERVER_ERROR
    ) {
        parent::__construct(message: $message, code: $code);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
