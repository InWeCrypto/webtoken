<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
	return redirect(env('APP_URL') . '/index.html');
});


Route::get('/doc', function () {
	$file = file_get_contents('api.html');
	echo $file;
});

function addModuleRecords()
{
	$modules = [
		[
			'title' => '用户管理',
			'uri' => '',
			'child' => [
				[
					'title' => '角色管理',
					'uri' => 'role'
				],
				[
					'title' => '员工管理',
					'uri' => 'admin'
				],
				[
					'title' => '个人信息',
					'uri' => 'user'
				],
			]
		],
		[
			'title' => 'Ico管理',
			'uri' => '',
			'child' => [
				[
					'title' => 'Ico分类',
					'uri' => 'ico-category'
				],
				[
					'title' => 'Ico列表',
					'uri' => 'ico'
				],
				[
					'title' => 'Ico订单',
					'uri' => 'ico-order'
				],
			]
		],
		[
			'title' => '钱包管理',
			'uri' => '',
			'child' => [
				[
					'title' => '钱包分类',
					'uri' => 'wallet-category'
				],
				[
					'title' => 'Gnt分类',
					'uri' => 'gnt-category'
				],
				[
					'title' => '钱包列表',
					'uri' => 'wallet'
				],
				[
					'title' => '钱包订单',
					'uri' => 'wallet-order'
				],
			]
		],
		[
			'title' => '货币单位管理',
			'uri' => 'monetary-unit',
		],
		[
			'title' => '文章管理',
			'uri' => 'article',
		],
		[
			'title' => '行情分类管理',
			'uri' => 'market-category',
		]

	];
	foreach ($modules as $v) {
		$parent = \App\Model\Module::create(['name' => $v['title'], 'p_id' => 0, 'uri' => $v['uri']]);
		if (isset($v['child'])) {
			foreach ($v['child'] as $val)
				\App\Model\Module::create(['name' => $val['title'], 'p_id' => $parent->id, 'uri' => $val['uri']]);
		}
	}
	return success();
}