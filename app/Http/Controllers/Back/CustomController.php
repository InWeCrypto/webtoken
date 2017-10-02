<?php

namespace App\Http\Controllers\Back;


use App\Model\User;
use Illuminate\Http\Request;

/**
 * Class CustomController
 * @package App\Http\Controllers\Back
 */
class CustomController extends BaseController
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		$list = User::paginate($request->get('per_page'));
		return success(compact('list'));
	}

	/**
	 * @param Request $request
	 * @param User $user
	 * @return array
	 */
	public function update(Request $request, User $user)
	{
		return $user->fill($request->only('nickname', 'img', 'sex'))->save() ? success() : fail();
	}
}
