<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * ico交易记录模型
 *
 * Class WalletOrder
 * @package App\Model
 */
class IcoOrder extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		"user_id",
		"ico_id",
		"wallet_id",
		"trade_no",
		"pay_address",
		"receive_address",
		"own_address",
		"block_number",
		"remark",
		"fee",
		"handle_fee",
		"hash",
		"status",
		"finished_at",
	];
	/**
	 * @var array
	 */
	protected $dates = ['finished_at'];


	/**
	 * 关联ico信息
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function ico()
	{
		return $this->belongsTo(Ico::class, 'ico_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function relationWallet()
	{
		return $this->belongsTo(Wallet::class, 'own_address', 'address');
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
	 * @param $walletId
	 * @return mixed
	 */
	public function scopeOfWalletId($query, $walletId)
	{
		return $walletId ? $query->where('wallet_id', $walletId) : $query;
	}


	/**
	 * @param $query
	 * @param $keyword
	 * @return mixed
	 */
	public function scopeOfKeyword($query, $keyword)
	{
		return $keyword ? $query->where(function ($query) use ($keyword) {
			$query->where('trade_no','like',"%$keyword%")->orWhere('address','like',"%$keyword%")->orWhere('hash','like',"%$keyword%")->orWhereHas('user',function ($query) use ($keyword){
				$query->where('nickname','like',"%$keyword%")->orWhere('open_id','like',"%$keyword%");
			})->orWhereHas('ico',function ($query) use ($keyword){
				$query->where('title','like',"%$keyword%");
			});
		}) : $query;
	}
}
