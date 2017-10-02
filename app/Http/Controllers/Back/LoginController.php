<?php

namespace App\Http\Controllers\Back;

use App\Model\Admin;
use App\Model\Module;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * 登录模块
 * Class LoginController
 * @package App\Http\Controllers\Back
 */
class LoginController extends BaseController
{
	/**
	 * @param Request $request
	 * @return array
	 * @throws \Exception
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'name' => 'required',
			'password' => 'required',
		]);
		//解析
//		dd($user = Auth::guard('back')->user());
		$user = Admin::where('name', $request->get('name'))->with('role')->firstOrFail();
		if (!password_verify($request->get('password'), $user->password)) {
			throw new \Exception('账号或密码不匹配,请重试');
		}
		if (!$user->is_valid) {
			throw new \Exception('账号被禁用,请联系管理员');
		}
		//获取权限
		if ($user->p_id) {
			$modules = Module::whereIn('id', $user->role->module_ids)->latest()->get()->keyBy('id')->toArray();
		} else {
			$modules = Module::latest()->get()->keyBy('id')->toArray();
		}
		$modulesTree = toTree($modules, 'p_id');
		$user->modules = $modules;
		$token = JWTAuth::fromUser($user);
		return success(compact('token', 'user','modulesTree'));
	}

}
