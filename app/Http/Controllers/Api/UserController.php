<?php

namespace App\Http\Controllers\Api;


use App\Model\User;
use Illuminate\Http\Request;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UserController extends BaseController
{
	/**
	 * 修改用户信息
	 *
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'nickname' => 'required',
			'img' => 'required',
		], [
			'nickname.required' => '请填写昵称',
			'img.required' => '请上传头像',
		]);

		return $this->user->fill($request->all())->save() ? success() : fail();
	}

	/**
	 * @return array
	 */
	public function create()
	{
		$user = $this->user;
		return success(compact('user'));
	}
}
