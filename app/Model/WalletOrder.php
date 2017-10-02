<?php

namespace App\Model;

use App\Services\Push;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * 钱包交易记录模型
 *
 * Class WalletOrder
 * @package App\Model
 */
class WalletOrder extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		"user_id",
		"wallet_id",
		"trade_no",
		"pay_address",
		"receive_address",
		"remark",
		"fee",
		"handle_fee",
		"hash",
		"status",
		"finished_at",
		"flag",
		"block_number",
		"own_address",//所属地址
	];

	/**
	 * WalletOrder constructor.
	 * @param array $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
		date_default_timezone_set('PRC');

	}

	/**
	 *
	 */
	public static function boot()
	{
		parent::boot();
		self::created(function (WalletOrder $order) {
			try {
				Log::info('created order :' . $order->trade_no);
				$order->fee = $order->fee ? round($order->fee / config('user_config.eth_ary'), 4) : 0;
				if ($order->status == 2) {
					if ($wallets = Wallet::ofAddress($order->pay_address)->get()) {
						foreach ($wallets as $wallet) {
							if (!Message::where('user_id', $wallet->user_id)->where('hash', $order->hash)->where('ext', $order->status)->count()) {
								$content = "交易转账{$order->fee} {$order->flag}， 接收方（{$order->receive_address}),开始进行打包";
								Push::doAction('交易信息', $content, ['resource_type' => 'WALLET_ORDER', 'resource_id' => $order->id], 'ACCOUNT', $wallet->user->open_id);
								Message::create(['user_id' => $wallet->user_id, 'title' => '系统消息', 'content' => $content, 'resource_type' => 'WALLET_ORDER', 'resource_id' => $order->id, 'ext' => $order->status, 'hash' => $order->hash]);
							}
						}
					}
					if ($wallets = Wallet::ofAddress($order->receive_address)->get()) {
						foreach ($wallets as $wallet) {
							if (!Message::where('user_id', $wallet->user_id)->where('hash', $order->hash)->where('ext', $order->status)->count()) {
								$content = "交易转账{$order->fee} {$order->flag}，发送方（{$order->pay_address}),开始进行打包";
								Push::doAction('交易信息', $content, ['resource_type' => 'WALLET_ORDER', 'resource_id' => $order->id], 'ACCOUNT', $wallet->user->open_id);
								Message::create(['user_id' => $wallet->user_id, 'title' => '系统消息', 'content' => $content, 'resource_type' => 'WALLET_ORDER', 'resource_id' => $order->id, 'ext' => $order->status, 'hash' => $order->hash]);
							}
						}
					}
				}

			} catch (\Exception $e) {
				Log::info('create order event error:' . $e->getMessage());
			}
		});

		self::updated(function (WalletOrder $order) {
			try {
				Log::info('updated order :' . $order->trade_no);
				$order->fee = $order->fee ? round($order->fee / config('user_config.eth_ary'), 4) : 0;
				if ($wallets = Wallet::ofAddress($order->pay_address)->get()) {
					foreach ($wallets as $wallet) {
						if ($order->status == 0) {
							$content = "交易转账{$order->fee} {$order->flag}， 接收方（{$order->receive_address}),已失败";
						}
						if ($order->status == 2) {
							$content = "交易转账{$order->fee} {$order->flag}， 接收方（{$order->receive_address}),开始进行打包";
						}
						if ($order->status == 3) {
							$content = "您已成功转账{$order->fee} {$order->flag}， 接收方（{$order->receive_address})";
						}
						if (!Message::where('user_id', $wallet->user_id)->where('hash', $order->hash)->where('ext', $order->status)->count()) {
							Push::doAction('交易信息', $content, ['resource_type' => 'WALLET_ORDER', 'resource_id' => $order->id], 'ACCOUNT', $wallet->user->open_id);
							Message::create(['user_id' => $wallet->user_id, 'title' => '系统消息', 'content' => $content, 'resource_type' => 'WALLET_ORDER', 'resource_id' => $order->id, 'ext' => $order->status, 'hash' => $order->hash]);
						}
					}
				}
				if ($wallets = Wallet::ofAddress($order->receive_address)->get()) {
					foreach ($wallets as $wallet) {
						if ($order->status == 0) {
							$content = "交易收款{$order->fee} {$order->flag}，发送方（{$order->pay_address}),已失败";
						}
						if ($order->status == 2) {
							$content = "交易转账{$order->fee} {$order->flag}，发送方（{$order->pay_address}),开始进行打包";
						}
						if ($order->status == 3) {
							$content = "您已成功收款{$order->fee} {$order->flag}，发送方（{$order->pay_address})";
						}
						if (!Message::where('user_id', $wallet->user_id)->where('hash', $order->hash)->where('ext', $order->status)->count()) {
							Push::doAction('交易信息', $content, ['resource_type' => 'WALLET_ORDER', 'resource_id' => $order->id], 'ACCOUNT', $wallet->user->open_id);
							Message::create(['user_id' => $wallet->user_id, 'title' => '系统消息', 'content' => $content, 'resource_type' => 'WALLET_ORDER', 'resource_id' => $order->id, 'ext' => $order->status, 'hash' => $order->hash]);
						}
					}
				}
			} catch (\Exception $e) {
				Log::info('update order event error:' . $e->getMessage());
			}
		});
	}

	/**
	 * @var array
	 */
	protected $dates = ['finished_at'];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function relationWallet()
	{
		return $this->belongsTo(Wallet::class, 'own_address', 'address');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\hasOne
	 */
	public function receiver()
	{
		return $this->hasOne(Wallet::class, 'address', 'receive_address');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function category()
	{
		return $this->belongsTo(WalletCategory::class);
	}


	/**
	 * @param $query
	 * @param $userId
	 * @return mixed
	 */
	public function scopeOfUserId($query, $userId)
	{
		return $userId ? $query->where('user_id', $userId) : $query;
	}


	/**
	 * @param $query
	 * @param $hash
	 * @return mixed
	 */
	public function scopeOfHash($query, $hash)
	{
		return $hash ? $query->where('hash', $hash) : $query;
	}

	/**
	 * @param $query
	 * @param $flag
	 * @return mixed
	 */
	public function scopeOfFlag($query, $flag)
	{
		return $query->where('flag', $flag);
	}


	/**
	 * @param $query
	 * @param $walletId
	 * @return mixed
	 */
	public function scopeOfWalletId($query, $walletId)
	{
		return $walletId ? $query->where('wallet_id', $walletId) : $query;
	}

	/**
	 * @param $query
	 * @param $status
	 * @return mixed
	 */
	public function scopeOfStatus($query, $status)
	{
		return $status ? $query->where("status", $status - 1) : $query;
	}

	/**
	 * @param $query
	 * @param $keyword
	 * @return mixed
	 */
	public function scopeOfKeyword($query, $keyword)
	{
		return $keyword ? $query->where(function ($query) use ($keyword) {
			$query->where('trade_no', 'like', "%$keyword%")->orWhere('hash', 'like', "%$keyword%")->orWhere('pay_address', 'like', "%$keyword%")->orWhere('pay_address', 'like', "%$keyword%")->orWhereHas('user', function ($query) use ($keyword) {
				$query->where('nickname', 'like', "%$keyword%");
			});
		}) : $query;
	}

}
