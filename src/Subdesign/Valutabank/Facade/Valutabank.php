<?php

namespace Subdesign\Valutabank\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Valutabank.hu parser Facade
 */
class Valutabank extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'valutabank';
    }
}
