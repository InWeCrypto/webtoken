<?php

namespace App\Http\Controllers\Api;


use App\Services\Mq\HttpConsumer;

class MqConsumerController extends BaseController
{
	public function index()
	{
		set_time_limit(0);
		HttpConsumer::process();
    }
}