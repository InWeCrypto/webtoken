<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Class BaseController
 * @package App\Http\Controllers\Back
 */
class BaseController extends Controller
{
	/**
	 * @var
	 */
	protected $user;

	/**
	 * BaseController constructor.
	 */
	public function __construct()
	{
		if (request()->hasHeader('ct')) {
			request()['token'] = request()->header('ct');
			$this->user = Auth::guard('back')->user();
		}
	}
}
