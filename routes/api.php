<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$router->group(['namespace' => 'Api'], function ($router) {
	$router->resource('auth', 'LoginController');
	$router->any('ali-oss', 'FileController@getOssInfo');
	$router->any('mq-get', 'MqConsumerController@index');
	$router->resource('wallet-category', 'WalletCategoryController');
	$router->resource('min-block', 'BlockController');
	$router->resource('article', 'ArticleController');
	$router->resource('sts', 'StsController');

	$router->group(['middleware' => 'check.api'], function ($router) {
		$router->group(['prefix' => 'extend'], function ($router) {//扩展接口的那一堆
			$router->any('getGasPrice', 'ExtendController@getGasPrice');
			$router->any('getTransactionCount', 'ExtendController@getTransactionCount');
			$router->any('sendRawTransaction', 'ExtendController@sendRawTransaction');
			$router->any('getTransaction', 'ExtendController@getTransaction');
			$router->any('getBalance', 'ExtendController@getBalance');
			$router->any('balanceOf', 'ExtendController@balanceOf');
			$router->any('getEstimateGas', 'ExtendController@getEstimateGas');
			$router->any('totalSupply', 'ExtendController@totalSupply');
			$router->any('transferABI', 'ExtendController@transferABI');
			$router->any('priceList', 'ExtendController@priceList');
			$router->any('blockNumber', 'ExtendController@blockNumber');
			$router->any('blockPerSecond', 'ExtendController@blockPerSecond');
			$router->any('getTransactions', 'ExtendController@getTransactions');
			$router->any('getTransactionById', 'ExtendController@getTransactionById');
			$router->any('estimateFee', 'ExtendController@estimateFee');
			$router->any('address', 'ExtendController@address');
			$router->any('getNeoUtxo', 'ExtendController@getNeoUtxo');
			$router->any('getNeoClaimUtxo', 'ExtendController@getNeoClaimUtxo');
            $router->any('getNeoOrderStatus', 'ExtendController@getNeoOrderStatus');

			$router->any('getNeoGntInfo', 'ConversionController@getNeoGntInfo');
		});
		$router->resource('message', 'MessageController');
		$router->resource('conversion', 'ConversionController');
		// $router->resource('sts', 'StsController');
		$router->resource('user-wallet', 'UserWalletCategoryController');
		$router->resource('gnt-category', 'GntCategoryController');
		$router->resource('user-gnt', 'UserGntCategoryController');
		$router->resource('find', 'FindController');
		$router->resource('contact', 'UserContactController');
		$router->resource('wallet', 'WalletController');
		$router->resource('wallet-order', 'WalletOrderController');
		$router->resource('monetary-unit', 'MonetaryUnitController');
		$router->resource('user', 'UserController');
		$router->resource('market-category', 'MarketCategoryController');
		$router->resource('market-notification', 'MarketNotificationController');
		$router->resource('monetary-unit', 'MonetaryUnitController');
		$router->resource('ico', 'IcoController');
		$router->resource('ico-order', 'IcoOrderController');
		$router->resource('gas', 'GasController');
	});
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
	return $request->user();
});
