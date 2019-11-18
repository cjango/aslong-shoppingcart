<?php

namespace AsLong\Cart;

use Illuminate\Support\Facades\Facade;

class CartFacade extends Facade
{

    public static function getFacadeAccessor()
    {
        return Cart::class;
    }

}
