<?php
/**
 * Created by PhpStorm.
 * User: zhaoyadong
 * Date: 2017/8/29
 * Time: 下午10:43
 */

namespace App\Services;

use Push\Request\V20160801\PushRequest;

include_once app_path() . '/Services/push/aliyun-php-sdk-core/Config.php';

/**
 * Class Push
 * @package App\Services
 */
class Push
{
	/**
	 *
	 */
	public static function process()
	{
		$config = config('mq');
		$iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $config['Ak'], $config['Sk']);
		$client = new \DefaultAcsClient($iClientProfile);
		$request = new PushRequest();

// 推送目标
		$request->setAppKey($config['AppKey']);
		$request->setTarget("ALL"); //推送目标: DEVICE:推送给设备; ACCOUNT:推送给指定帐号,TAG:推送给自定义标签; ALL: 推送给全部
		$request->setTargetValue("ALL"); //根据Target来设定，如Target=device, 则对应的值为 设备id1,设备id2. 多个值使用逗号分隔.(帐号与设备有一次最多100个的限制)
		$request->setDeviceType("ANDROID"); //设备类型 ANDROID iOS ALL.
		$request->setPushType("MESSAGE"); //消息类型 MESSAGE NOTICE
		$request->setTitle("php title"); // 消息的标题
		$request->setBody("php body"); // 消息的内容
// 推送配置: iOS
		$request->setiOSBadge(5); // iOS应用图标右上角角标
		$request->setiOSSilentNotification("false");//是否开启静默通知
		$request->setiOSMusic("default"); // iOS通知声音
		$request->setiOSApnsEnv("DEV");//iOS的通知是通过APNs中心来发送的，需要填写对应的环境信息。"DEV" : 表示开发环境 "PRODUCT" : 表示生产环境
		$request->setiOSRemind("false"); // 推送时设备不在线（既与移动推送的服务端的长连接通道不通），则这条推送会做为通知，通过苹果的APNs通道送达一次(发送通知时,Summary为通知的内容,Message不起作用)。注意：离线消息转通知仅适用于生产环境
		$request->setiOSRemindBody("iOSRemindBody");//iOS消息转通知时使用的iOS通知内容，仅当iOSApnsEnv=PRODUCT && iOSRemind为true时有效
		$request->setiOSExtParameters("{\"k1\":\"ios\",\"k2\":\"v2\"}"); //自定义的kv结构,开发者扩展用 针对iOS设备
		$response = $client->getAcsResponse($request);
		print_r("\r\n");
		dd($response);
	}

	/**
	 * @param string $title 消息头
	 * @param string $message 消息体
	 * @param array $data 扩展消息 ICO_ORDER,WALLET_ORDER,ICO_ARTICLE,SYSTEM,MARKET
	 * @param string $targetType 推送目标类型
	 * @param string $targetValue 推送目标
	 * @param string $deviceType 消息类型
	 * @return array
	 */
	public static function doAction($title, $message, $data = ['resource_type' => 'SYSTEM', 'resource_id' => 0], $targetType = 'ALL', $targetValue = 'ALL', $deviceType = 'ALL')
	{
		$config = config('mq');
		$iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $config['Ak'], $config['Sk']);
		$client = new \DefaultAcsClient($iClientProfile);
		$request = new PushRequest();
		$request->setAppKey($config['AppKey']);
		$request->setTarget($targetType); //推送目标: DEVICE:推送给设备; ACCOUNT:推送给指定帐号,TAG:推送给自定义标签; ALL: 推送给全部
		$request->setTargetValue($targetValue); //根据Target来设定，如Target=device, 则对应的值为 设备id1,设备id2. 多个值使用逗号分隔.(帐号与设备有一次最多100个的限制)
		$request->setDeviceType($deviceType); //设备类型 ANDROID iOS ALL.
		$request->setPushType("NOTICE"); //消息类型 MESSAGE NOTICE
		$request->setTitle($title); // 消息的标题
		$request->setBody($message); // 消息的内容
		if (trim(strtolower(env('IOS_PUSH_TYPE', '')) == 'dev')) {
			$request->setiOSApnsEnv('DEV');
		}
		//设置参数
		$request->setAndroidExtParameters(json_encode($data));
		$request->setiOSExtParameters(json_encode($data));
		$client->getAcsResponse($request);
		return success();
	}
}