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
		$iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", env('Ak'), env('Sk'));
		$client = new \DefaultAcsClient($iClientProfile);
		$BUCKET_NAME = env('BucketName');
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
		$request->setRoleSessionName(env('RoleSessionName'));
		// 角色资源描述符，在RAM的控制台的资源详情页上可以获取
		$request->setRoleArn(env('RoleArn'));
		$request->setPolicy($policy);
		$request->setDurationSeconds(env('DurationSeconds'));
		$response = $client->doAction($request);

		return success(json_decode($response->getBody(), true));
	}
}