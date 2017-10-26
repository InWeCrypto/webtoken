<?php
/**
 * mqtt config
 * Created by PhpStorm.
 * User: zhaoyadong
 * Date: 2017/8/18
 * Time: 下午5:48
 */

return [
	#您在控制台创建的Topic
	'Topic'=>'ethtx',
	#公测环境的URL
//	'URL'=>'https://cryptobox.oss-cn-shenzhen.aliyuncs.com',
	'URL'=>'http://publictest-rest.ons.aliyun.com',
	#oss host
	'OssHost'=>'cryptobox.oss-cn-shenzhen.aliyuncs.com',
	#阿里云身份验证码
	'Ak'=>'LTAITYEbsi2WOSCd',
	#阿里云身份验证密钥
	'Sk'=>'d9MuUqH9rN8ctF6AklWsXcxI3dyEVP',
	#appkey
	'AppKey'=>'24535336',
	#MQ控制台创建的Producer ID
	'ProducerID'=>'CID-ETH-TX',
	#MQ控制台创建的Consumer ID
	'ConsumerID'=>'CID-ETH-TX',
];
