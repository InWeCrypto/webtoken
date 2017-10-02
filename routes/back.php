<?php

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
$router->group(['namespace' => 'Back'], function ($router) {
	$router->resource('auth', 'LoginController');

	$router->group(['middleware' => 'check.back'], function ($router) {
		$router->any('upload', 'FileController@postUploadTemp');
		$router->resource('role', 'RoleController');
		$router->resource('module', 'ModuleController');
		$router->resource('admin', 'AdminController');
		$router->resource('config', 'ConfigController');
		$router->resource('user', 'UserController');

		//用户管理
		$router->resource('custom', 'CustomController');

		//ico
		$router->resource('ico-category', 'IcoCategoryController');
		$router->get('get-all-category', 'IcoController@getCategory');
		$router->resource('ico', 'IcoController');
		$router->resource('ico-order', 'IcoOrderController');
		//钱包
		$router->resource('wallet-category', 'WalletCategoryController');
		$router->resource('wallet', 'WalletController');
		$router->resource('wallet-order', 'WalletOrderController');
		$router->resource('gnt-category', 'GntCategoryController');

		$router->resource('monetary-unit', 'MonetaryUnitController');
		$router->resource('market-category', 'MarketCategoryController');
		$router->resource('article', 'ArticleController');
	});
});