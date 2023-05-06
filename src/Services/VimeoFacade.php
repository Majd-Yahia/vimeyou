<?php
namespace Awesomchu\Vimeo\Services;

use Illuminate\Support\Facades\Facade;
use Vimeo\Vimeo;

class VimeoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Vimeo::class;
    }
}