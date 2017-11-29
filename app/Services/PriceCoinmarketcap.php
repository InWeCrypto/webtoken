<?php

namespace App\Services;

class PriceCoinmarketcap{
    public function getPrice($ico_type, $convert = 'CNY'){
        if(!$ico_type){
            return [];
        }
        $return = [];
        $url    = 'https://api.coinmarketcap.com/v1/ticker/' . strtolower($ico_type) . '/?convert=' . $convert;
        $key    = 'KEY:PRICECOINMARKETCAP:'.$url;
        if(\Redis::exists($key)){
            return json_decode(\Redis::get($key));
        }
        $res     = sendCurl($url);
        $return  = reset($res) ?: $res;
        \Redis::set($key, json_encode($return));
        \Redis::expire($key, 60);
        return $return;
    }
}