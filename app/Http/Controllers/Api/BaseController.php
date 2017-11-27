<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
	protected $user=null;

	public function __construct()
	{
		$request = request();
		if ($request->hasHeader('ct')) {
			$request['token'] = $request->header('ct');
			$this->user = Auth::guard('api')->user();
		}
		// if($request->has('address')){
		// 	$request['address'] = strtolower($request->address);
		// }
		// if($request->has('pay_address')){
		// 	$request['pay_address'] = strtolower($request->pay_address);
		// }
		// if($request->has('receive_address')){
		// 	$request['receive_address'] = strtolower($request->receive_address);
		// }
//		$this->user = User::find(1);
//		$this->user = new \stdClass();
//		$this->user->id = 1;
	}
}
