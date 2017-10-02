<?php

namespace App\Http\Controllers\Back;


use App\Model\IcoOrder;
use Illuminate\Http\Request;

class IcoOrderController extends BaseController
{
	public function index(Request $request)
	{
		$list=IcoOrder::with('ico','user')->ofKeyword($request->get('keyword'))->paginate($request->get('per_page'));
		return success(compact('list'));
    }
}
