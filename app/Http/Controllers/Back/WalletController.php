<?php

namespace App\Http\Controllers\Back;


use App\Model\Wallet;
use Illuminate\Http\Request;

class WalletController extends BaseController
{
	public function index(Request $request)
	{
		$list = Wallet::with('user','category')->ofKeyword($request->get('keyword'))->paginate($request->get('per_page'));

		return success(compact('list'));
    }
}
