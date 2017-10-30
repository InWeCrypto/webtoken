<?php
/**
 * 消息订阅者
 * Created by PhpStorm.
 * User: zhaoyadong
 * Date: 2017/8/18
 * Time: 下午5:55
 */

namespace App\Services\Mq;


use App\Model\GntCategory;
use App\Model\Message;
use App\Model\Wallet;
use App\Model\WalletCategory;
use App\Model\WalletOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class HttpConsumer
 * @package App\Services\Mq
 */
class HttpConsumer
{
	//订阅流程
	/**
	 *
	 */
	public static function process()
	{
		$config = config('mq');
		//获取Topic
		$topic = env('Topic', $config["Topic"]);
		//获取Topic的URL路径
		$url = env('URL', $config["URL"]);
		//阿里云身份验证码
		$ak = env('Ak', $config["Ak"]);
		//阿里云身份验证密钥
		$sk = env('Sk', $config["Sk"]);
		//Consumer ID
		$cid = env('ConsumerID', $config["ConsumerID"]);
		$newline = "\n";
		//构造工具对象
		$util = new Util();
		while (true) {
			try {
				date_default_timezone_set('GMT');
				//构造时间戳
				$date = time() * 1000;
// 				Log::info($date);
				//签名字符串
				$signString = $topic . $newline . $cid . $newline . $date;
				//计算签名
				$sign = $util->calSignature($signString, $sk);
				//构造签名标记
				$signFlag = "Signature:" . $sign;
				//构造密钥标记
				$akFlag = "AccessKey:" . $ak;
				//标记
				$consumerFlag = "ConsumerID:" . $cid;
				//构造HTTP请求发送内容类型标记
				$contentFlag = "Content-Type:text/html;charset=UTF-8";
				//构造HTTP头部信息
				$headers = array(
					$signFlag,
					$akFlag,
					$consumerFlag,
					$contentFlag,
				);
				//构造HTTP请求URL
				$getUrl = $url . "/message/?topic=" . $topic . "&time=" . $date . "&num=32";
				//初始化网络通信模块
				$ch = curl_init();
				//填充HTTP头部信息
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				//设置HTTP请求类型,此处为GET
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
				//设置HTTP请求URL
				curl_setopt($ch, CURLOPT_URL, $getUrl);
				//构造执行环境
				ob_start();
				//开始发送HTTP请求
				curl_exec($ch);
				//获取请求应答消息
				$result = ob_get_contents();
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// 				Log::info($httpCode);
//				Log::info('原始数据:' . $result);
				//清理执行环境
				ob_end_clean();
				//关闭HTTP网络连接
				curl_close($ch);
				//解析HTTP应答信息
				$res = json_decode($result, true);
				//如果应答信息中的没有包含任何的Topic信息,则直接跳过
				if (count($res) == 0 || $httpCode != 200) {
// 					Log::info($result);
					continue;
				}
				foreach ($res as $v) {
					$body = json_decode($v['body'], true);
					Log::info($body['hash']);
					self::processData($body['hash']);
//					dispatch(new MQChangeStatus($body['hash']));
				}
			} catch (\Exception $e) {
				//打印异常信息
				Log::info($e->getMessage());
			}
		}
	}


	/**
	 * @param $hash
	 * @return bool
	 */
	public static function processData($hash)
	{
		Log::info('process order:' . $hash);

		if (!$eth = DB::table('ethtx')->where('hash', $hash)->first()) {
			return false;
		}
		if (!$wallet = Wallet::ofAddress($eth->addr_from)->orWhere('address', $eth->type ? $eth->addr_token : $eth->addr_to)->first()) {
			return false;
		}
		$res = sendCurl(env('API_URL', config('user_config.api_url')) . '/eth/blockNumber', [], [], 'get');
		try {
			Log::info('start transaction:');
			DB::transaction(function () use ($eth, $wallet, $res) {
				//检测是否存在
				$orders = WalletOrder::where('hash', $eth->hash)->get();
				if ($orders->count()) {
					/**
					 * 存在记录可推断钱包内部流转
					 */
					//内部交易,直接改变状态和通知
					foreach ($orders as $v) {
						Log::info('执行内部流程订单,切换状态为 2:' . $v->hash);
						$v->fill(['status' => 2])->save();
					}
				} else {
					/**
					 * 非内部流转,可推断wallet表存在的记录一定是用户的记录
					 */
					$insert = [
						"trade_no" => $eth->hash,
						"block_number" => hexdec($res['value']),
						"handle_fee" => $eth->gas,
						"hash" => $eth->hash,
						"pay_address" => $eth->addr_from,
						"receive_address" => $eth->type ? $eth->addr_token : $eth->addr_to,
					];
					$changData = [
						"fee" => $eth->value,
						"flag" => 'ETH',
					];
					if ($eth->type) {
						//推导代币类型地址
//						$tokenKey = Wallet::ofAddress($eth->addr_from)->count() ? $eth->addr_to : $eth->addr_from;
//						$category = WalletCategory::where('address', $tokenKey)->first();

						$category = GntCategory::where('address', $eth->addr_to)->first();
						if (!$category) {
							Log::info('记录:' . json_encode($eth) . '转入/转出,查询不到钱包类型');
							throw new \Exception('转入/转出,查询不到钱包类型');
						}
//						$changData = strtoupper($category->name) == 'ETH' ? [
//							"fee" => $eth->value,
//							"flag" => 'ETH',
//						] : [
//							"fee" => $eth->token_value,
//							"flag" => strtoupper($category->name),
//						];
						$changData = [
							"fee" => $eth->token_value,
							"flag" => strtoupper($category->name),
						];
					}
					foreach ([$eth->addr_from, $eth->type ? $eth->addr_token : $eth->addr_to] as $address) {
						if ($wallet = Wallet::ofAddress($address)->first()) {
							//不存在,添加
							WalletOrder::create($insert + $changData + ['status' => 2, 'own_address' => $wallet->address, "user_id" => $wallet->user_id, "wallet_id" => $wallet->id,]);
						}
					}
				}
			});
			return true;
		} catch (\Exception $e) {
			Log::info('roll back,err:' . $e->getMessage());
			return false;
		}
	}


	/**
	 * @param $userId
	 * @param string $content
	 * @param string $resourceType
	 * @param int $resourceId
	 */
	public static function createMessage($userId, $content = '已交易完成', $resourceType = 'SYSTEM', $resourceId = 0)
	{
		Message::create(['user_id' => $userId, 'title' => '系统消息', 'content' => $content, 'resource_type' => $resourceType, 'resource_id' => $resourceId]);
	}

}

