<?php

namespace App\Http\Controllers\Back;


use App\Model\Module;
use App\Model\Role;
use Illuminate\Http\Request;

class ModuleController extends BaseController
{
	/**
	 * @return array
	 */
	public function index()
	{
		$list = Module::get()->keyBy('id')->toArray();
		$list = toTree($list, 'p_id');
		return success(compact('list'));
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return array
	 */
	public function update(Request $request, $id)
	{
		$role = Role::find($id);
		return $role->update(['module_ids' => implode(',', $request->all())]) ? success() : fail();
	}
}
