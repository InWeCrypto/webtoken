<?php
/**
 * Created by PhpStorm.
 * User: zhaoyadong
 * Date: 2017/8/9
 * Time: 下午5:09
 */

namespace App\Services;


class OssService
{
	public function gmt_iso8601($time)
	{
		$dtStr = date("c", $time);
		$mydatetime = new \DateTime($dtStr);
		$expiration = $mydatetime->format(\DateTime::ISO8601);
		$pos = strpos($expiration, '+');
		$expiration = substr($expiration, 0, $pos);
		return $expiration . "Z";
	}

	public function getOssInfo()
	{
		$config = config('mq');
		$now = time();
		$expire = 300000000; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
		$end = $now + $expire;
		$expiration = $this->gmt_iso8601($end);

		$dir = 'cryptobox/';

		//最大文件大小.用户可以自己设置
		$condition = array(0 => 'content-length-range', 1 => 0, 2 => 1048576000);
		$conditions[] = $condition;

		//表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
		$start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
		$conditions[] = $start;
		$arr = array('expiration' => $expiration, 'conditions' => $conditions);
		//echo json_encode($arr);
		//return;
		$policy = json_encode($arr);
		$base64_policy = base64_encode($policy);
		$string_to_sign = $base64_policy;
		$signature = base64_encode(hash_hmac('sha1', $string_to_sign, $config['Sk'], true));
		$response = [];
		$response['accessid'] = $config['Ak'];
		$response['host'] = $config['OssHost'];
		$response['policy'] = $base64_policy;
		$response['signature'] = $signature;
		$response['expire'] = $end;
		//这个参数是设置用户上传指定的前缀
		$response['dir'] = $dir;
		return $response;
	}
}