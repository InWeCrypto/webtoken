<?php

namespace App\Http\Controllers\Back;


use App\Model\Role;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
	/**
	 * @return array
	 */
	public function index()
	{
		$list = Role::get();
		return success(compact('list'));
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'name' => 'unique:roles,name',
		]);
		return Role::create($request->all()) ? success() : fail();
	}

	/**
	 * @param Role $role
	 * @param Request $request
	 * @return array
	 */
	public function update(Role $role, Request $request)
	{
		$this->validate($request, [
			'name' => 'unique:roles,name,' . $role->id
		]);
		return $role->fill($request->all())->save() ? success() : fail();
	}

	/**
	 * @param Role $role
	 * @return array
	 */
	public function destroy(Role $role)
	{
		if ($role->hasUser()->count()) {
			return fail('', '该角色下有用户,请先删除用户');
		}

		return $role->delete() ? success() : fail();

	}
}
