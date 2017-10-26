<?php

namespace App\Http\Controllers\Back;

use Illuminate\Http\Request;

class UserController extends BaseController
{

	/**
	 * 个人资料
	 * @return array
	 */
	public function index()
	{
		return success(['user' => $this->user]);
	}

	/**
	 * 保存修改信息
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'nickname' => 'required',
			'img' => 'required',
			'name' => 'required|unique:admins,name,' . $this->user->id,
		]);
		$data = $request->except('password');
		if ($request->has('password')) {
			$this->validate($request, [
				'old_password' => 'required|min:6|max:32',
				'password' => 'required|min:6|max:32|confirmed'
			]);
			$data = $request->all();
		}
		if (!password_verify($request->get('old_password'), $this->user->password)) {
			return fail('', '原密码不匹配');
		}
		unset($this->user->modules);
		return $this->user->fill($data)->save() ? success(['user' => $this->user]) : fail();
	}

}
