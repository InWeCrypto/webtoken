<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PriceCoinmarketcap extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'price_coinmarketcap';
    }
}