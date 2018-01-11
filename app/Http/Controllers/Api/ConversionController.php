<?php

namespace App\Http\Controllers\Api;


use App\Model\Pricecoinmarketcap;
use App\Model\Wallet;
use Illuminate\Http\Request;

/**
 * Class IndexController
 * @package App\Http\Controllers\Api
 */
class ConversionController extends BaseController
{
	/**
	 * 获取钱包余额
	 *
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		// \PriceCoinmarketcap::test();
		$this->validate($request, [
			'wallet_ids' => 'required|json'
		]);
		$wallets = json_decode($request->get('wallet_ids'), true);
		$list = Wallet::with('category.icoInfo')
			->ofUserId($this->user->id)
			->whereIn('id', $wallets)
			->get()->each(function ($val){
				if(! $ico_name = !empty($val->category->icoInfo) ? $val->category->icoInfo->name : null){
					\Log::info('获取'.$val->category->name.'的API名称失败,请检查ico_list表中是否存在!');
				}
				$val->category->cap = \PriceCoinmarketcap::getPrice($ico_name) ?: null;
				//钱包余额
				$val->balance = $this->getWalletBalance($val->category->name, $val->address);
		});

		return success(compact('list'));
	}

	/**
	 * @param $walletId
	 * @return array
	 */
	public function show($walletId)
	{
		// $record = Wallet::with('gnt.gntCategory.icoInfo')->findOrFail($walletId);
		$record = Wallet::with('gnt.gntCategory.icoInfo')->ofUserId($this->user->id)->findOrFail($walletId);

		//测算价值
		switch(strtolower($record->category->name)){
			case 'eth':
				$list = $record->gnt->each(function ($val) use ($record) {
					if(! $ico_name = !empty($val->gntCategory->icoInfo) ? $val->gntCategory->icoInfo->name : null){
						\Log::info('获取'.$val->gntCategory->name.'的API名称失败,请检查ico_list表中是否存在!');
					}
					$val->gntCategory->cap = \PriceCoinmarketcap::getPrice($ico_name) ?: null;
					$uri   = env('API_URL',config('user_config.unichain_url')) . '/eth/tokens/balanceOf';
					$param = [
						'contract' => $val->gntCategory->address,
						'address' => $record->address
					];
					$res = sendCurl($uri, $param, null, 'POST');
					$val->balance = $res['value'];
				})->sortByDesc('updated_at')->values()->all();
			break;
			case 'neo':
				// neo 余额
				$record->balance = $this->getWalletBalance('neo', $record->address);
				$record->cap = \PriceCoinmarketcap::getPrice('neo') ?: null;
                $list = $record->gnt->each(function ($val) use ($record) {
                    if(! $address_hash160 = $record->address_hash160){
                        \Log::info('NEO 钱包ID:'.$record->id.',address_hash160 为空!');
                        $val->balance = 0;
                    }else{
                        $val->balance = $this->getNeoGntBalance($val->gntCategory->address, $address_hash160);
                    }

                    // 获取代币通用信息
                    $val->decimals = $this->getNeoGntDecimals($val->gntCategory->address);
                    $val->symbol   = $this->getNeoGntSymbol($val->gntCategory->address);

					if(! $ico_name = !empty($val->gntCategory->icoInfo) ? $val->gntCategory->icoInfo->name : null){
                        \Log::info('获取'.$val->gntCategory->name.'的API名称失败,请检查ico_list表中是否存在!');
					}
					$val->gntCategory->cap = \PriceCoinmarketcap::getPrice($ico_name) ?: null;
				})->sortByDesc('updated_at')->values()->all();
			case 'gas':
				$uri = env('TRADER_URL_NEO',config('user_config.unichain_url')) . '/extend';
				$param = [
					'jsonrpc' => '2.0',
					'method' => 'claim',
					'method' => 'claim',
					'params' => [$record->address],
					'id' => 0
				];
				$res = sendCurl($uri, $param, null, 'POST');
				// neo 默认代币 gas
				$gnt = [
					'name' => 'gas',
					'unavailable' => $res['result']['Unavailable'] ?: 0,
					'available' => $res['result']['Available'] ?: 0,
					'balance' => $this->getWalletBalance('neo', $record->address, \Request::header('neo-gas-asset-id')),
					'cap' => \PriceCoinmarketcap::getPrice('gas') ?: null
				];
                // neo 钱包没有代币,默认为gas
                unset($record->gnt);
				$record->gnt = [collect($gnt)];
			break;
		}

		return success(compact('record','list'));
	}

	// 钱包余额
	public function getWalletBalance($category_name, $address, $asset_id = null){
		$return = 0;
		switch(strtolower($category_name)){
			case 'eth':
				$address= strtolower($address);
				$uri    = env('TRADER_URL_ETH', config('user_config.unichain_url')) . '/eth/getBalance';
				$res    = sendCurl($uri, compact('address'), null, 'POST');
				$return = $res['value'];
			break;
			case 'neo':
				$neo_asset_id = $asset_id ?: \Request::header('neo-asset-id');
				$uri          = env('TRADER_URL_NEO', config('user_config.unichain_url'));
				$param        = [
					'jsonrpc' => '2.0',
					'method' => 'getaccountstate',
					'params' => [$address],
					'id' => 1
				];
				$res = sendCurl($uri, $param, null, 'POST');
				if(!empty($res['result']['balances'])){
					foreach($res['result']['balances'] as $val){
						if(strcasecmp($val['asset'], $neo_asset_id) == 0){
							$return = $val['value'];
							break;
						}
					}
				}
			break;
		}
		return $return;
	}

    /**
    * 获取neo代币余额信息
    * @param string $c_address 合约地址
    * @param string $w_address 钱包地址
    * @return array
    */
    public function getNeoGntBalance($c_address, $w_address){
        $return = 0;

        $uri = env('TRADER_URL_NEO',config('user_config.unichain_url'));
        $param = [
            'jsonrpc' => "2.0",
            'method' => "invokefunction",
            'params' => [
                ltrim($c_address, '0x'), // 合约地址
                'balanceOf',
                [
                    [
                        'type' => 'Hash160',
                        'value' => $w_address
                    ]
                ]
            ],
            'id' =>3
        ];
        try {
            $res = sendCurl($uri, $param, null, 'POST');
            $return = $res['result']['stack'][0]['value'] ?: 0;
        } catch (\Exception $e) {
            \Log::info('获取neo代币余额失败!合约地址:'.$c_address.',钱包地址:'.$w_address);
            // throw new \Exception();
            $return = 0;
        }
        return $return;
    }

    // 单独获取NEO通用信息
    public function getNeoGntInfo(Request $request){
        $this->validate($request, [
			'address' => 'required'
		]);
        $address = $request->get('address');
        $return = [];
        $return['decimals'] = $this->getNeoGntDecimals($address);
        $return['symbol'] = $this->getNeoGntSymbol($address);
        return success($return);
    }
    /**
    * 缓存neo代币小数位数
    * @param string $address 合约地址
    */
    public function getNeoGntDecimals($address){
        $return = null;
        $address = ltrim($address, '0x'); // 合约地址,
        $cache_key = 'KEY:NEO_GNT:DECIMALS:'.$address;
        try {
            if(\Redis::exists($cache_key)){
                return \Redis::get($cache_key);
            }
            $uri = env('TRADER_URL_NEO',config('user_config.unichain_url'));
            $param = [
                'jsonrpc' => '2.0',
                'method' => 'invokefunction',
                'params' => [
                    $address,
                    'decimals',
                    []
                ],
                'id' => 2
            ];
            $res = sendCurl($uri, $param, null, 'POST');
            if(! $return = $res['result']['stack'][0]['value']){
                throw new \Exception("获取neo代币小数位数失败!".$address);
            }
            // 写入到redis缓存
            \Redis::set($cache_key, $return);
            \Redis::expire($cache_key, 60 * 5 ); // 5分钟
        } catch (\Exception $e) {
            \Log::info('获取neo代币小数位数失败!'.$address);
        }
        return $return;
    }

    /**
    * 缓存neo代币符号
    * @param string $address 合约地址
    */
    public function getNeoGntSymbol($address){
        $return = null;
        $address = ltrim($address, '0x'); // 合约地址,
        $cache_key = 'KEY:NEO_GNT:SYMBOL:'.$address;
        try {
            if(\Redis::exists($cache_key)){
                return \Redis::get($cache_key);
            }
            $uri = env('TRADER_URL_NEO',config('user_config.unichain_url'));
            $param = [
                'jsonrpc' => '2.0',
                'method' => 'invokefunction',
                'params' => [
                    $address,
                    'symbol',
                    []
                ],
                'id' => 1
            ];
            $res = sendCurl($uri, $param, null, 'POST');
            if(! $return = $res['result']['stack'][0]['value']){
                throw new \Exception("获取neo代币符号失败!".$address);
            }
            $return = Hex2String($return);
            // 写入到redis缓存
            \Redis::set($cache_key, $return);
            \Redis::expire($cache_key, 60 * 5 ); // 5分钟
        } catch (\Exception $e) {
            \Log::info('获取neo代币符号失败!'.$address);
        }
        return $return;
    }
}
