<?php
namespace Awesomchu\Vimeo\Services;

use Awesomchu\Vimeo\Platform\Vimeo;
use Illuminate\Support\Facades\Facade;

class VimeoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Vimeo::class;
    }
}