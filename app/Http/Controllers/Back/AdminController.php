<?php

namespace App\Http\Controllers\Back;


use App\Model\Admin;
use Illuminate\Http\Request;

class AdminController extends BaseController
{

	/**
	 * @return array
	 */
	public function index()
	{
		$list = Admin::with('role')->where('id', '<>', $this->user->id)->where('role_id', '<>', 0)->get();
		return success(compact('list'));
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'nickname' => 'required',
			'name' => 'numeric|required|unique:admins',
			'password' => 'required|confirmed',
		]);
		$data = $request->all();
		return $this->user->hasUser()->create($data) ? success() : fail();
	}

	/**
	 * @param Admin $admin
	 * @param Request $request
	 * @return array
	 */
	public function update(Admin $admin, Request $request)
	{
		$this->validate($request, [
			'nickname' => 'required',
			'name' => 'numeric|required|unique:admins,name,' . $admin->id,
		]);
		$data = $request->except('password');
		if ($request->has('password')) {
			$this->validate($request, [
				'password' => 'required|min:6|max:32|confirmed'
			]);
			$data = $request->all();
		}
		return $admin->fill($data)->save() ? success() : fail();
	}


}
