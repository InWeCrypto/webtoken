<?php
require 'app/Services/Mq/HttpConsumer.php';
//use App\Services\Mq\HttpConsumer;
set_time_limit(0);
HttpConsumer::process();