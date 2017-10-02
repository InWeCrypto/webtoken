<?php
/**
 * Created by PhpStorm.
 * User: zhaoyadong
 * Date: 2017/8/9
 * Time: 下午4:56
 */

namespace App\Http\Controllers\Api;


use Sts\Request\V20150401\AssumeRoleRequest;

include_once app_path() . '/Services/Sts/aliyun-php-sdk-core/Config.php';


class StsController extends BaseController
{

	public function index()
	{

// 你需要操作的资源所在的region，STS服务目前只有杭州节点可以签发Token，签发出的Token在所有Region都可用
// 只允许子用户使用角色
		$config = config('mq');
		$iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $config['Ak'], $config['Sk']);
		$client = new \DefaultAcsClient($iClientProfile);

// 角色资源描述符，在RAM的控制台的资源详情页上可以获取
//		$roleArn = "acs:ram::1847538160312731:role/phpserver";
		$roleArn = "acs:ram::1847538160312731:role/phpserver";

		$BUCKET_NAME = 'cryptobox';
		$policy = <<<POLICY
{
  "Statement": [
    {
      "Action": [
        "oss:GetObject",
        "oss:PutObject",
        "oss:DeleteObject",
        "oss:ListParts",
        "oss:AbortMultipartUpload",
        "oss:ListObjects"
      ],
      "Effect": "Allow",
      "Resource": ["acs:oss:*:*:$BUCKET_NAME/*", "acs:oss:*:*:$BUCKET_NAME"]
    }
  ],
  "Version": "1"
}
POLICY;

		$request = new AssumeRoleRequest();
// RoleSessionName即临时身份的会话名称，用于区分不同的临时身份
// 您可以使用您的客户的ID作为会话名称
		$request->setRoleSessionName("phpserver");
		$request->setRoleArn($roleArn);
		$request->setPolicy($policy);
		$request->setDurationSeconds(3600);
		$response = $client->doAction($request);

		return success(json_decode($response->getBody(), true));
	}
}