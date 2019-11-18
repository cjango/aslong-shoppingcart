<?php

namespace AsLong\Cart\Facades;

use Illuminate\Support\Facades\Facade;

class Cart extends Facade
{

    public static function getFacadeAccessor()
    {
        return Cart::class;
    }

}
