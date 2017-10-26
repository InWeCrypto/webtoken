<?php

namespace App\Http\Controllers\Back;


use App\Model\WalletOrder;
use Illuminate\Http\Request;

class WalletOrderController extends BaseController
{
	public function index(Request $request)
	{
		$list = WalletOrder::with('user','receiver.user','category')->ofKeyword($request->get('keyword'))->ofStatus($request->get('status',0))->paginate($request->get('per_page'));
		return success(compact('list'));
    }
}
