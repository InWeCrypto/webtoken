<?php
/**
 * 消息发布者者
 * Created by PhpStorm.
 * User: zhaoyadong
 * Date: 2017/8/18
 * Time: 下午5:56
 */

namespace App\Services\Mq;


class HttpProducer
{
	//签名
	private static $signature = "Signature";
	//在MQ控制台创建的Producer ID
	private static $producerid = "ProducerID";
	//阿里云身份验证码
	private static $aks = "AccessKey";
	
	//计算md5
	private function md5($str)
	{
		return md5($str);
	}

	//发布消息流程
	public function process()
	{
		$configs = config('mq');
		//获取Topic
		$topic = $configs["Topic"];
		//获取保存Topic的URL路径
		$url = $configs["URL"];
		//读取阿里云访问码
		$ak = $configs["Ak"];
		//读取阿里云密钥
		$sk = $configs["Sk"];
		//读取Producer ID
		$pid = $configs["ProducerID"];
		//HTTP请求体内容
		$body = "我自己发布";
		$newline = "\n";
		//构造工具对象
		$util = new Util();
		for ($i = 0; $i < 10; $i++) {
			//计算时间戳
			$date = time() * 1000;
			//POST请求url
			$postUrl = $url . "/message/?topic=" . $topic . "&time=" . $date . "&tag=http&key=http";
			//签名字符串
			$signString = $topic . $newline . $pid . $newline . $this->md5($body) . $newline . $date;
			//计算签名
			$sign = $util->calSignature($signString, $sk);
			//初始化网络通信模块
			$ch = curl_init();
			//构造签名标记
			$signFlag = $this::$signature . ":" . $sign;
			//构造密钥标记
			$akFlag = $this::$aks . ":" . $ak;
			//标记
			$producerFlag = $this::$producerid . ":" . $pid;
			//构造HTTP请求头部内容类型标记
			$contentFlag = "Content-Type:text/html;charset=UTF-8";
			//构造HTTP请求头部
			$headers = array(
				$signFlag,
				$akFlag,
				$producerFlag,
				$contentFlag,
			);
			//设置HTTP头部内容
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			//设置HTTP请求类型,此处为POST
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			//设置HTTP请求的URL
			curl_setopt($ch, CURLOPT_URL, $postUrl);
			//设置HTTP请求的body
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
			//构造执行环境
			ob_start();
			//开始发送HTTP请求
			curl_exec($ch);
			//获取请求应答消息
			$result = ob_get_contents();
			//清理执行环境
			ob_end_clean();
			//关闭连接
			curl_close($ch);
			var_dump($result);

		}
	}
}