<?php

namespace Tinkeshwar\Imager\Facades;

use Illuminate\Support\Facades\Facade;

class Imager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'imager';
    }
}
