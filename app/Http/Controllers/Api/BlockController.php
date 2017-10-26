<?php

namespace App\Http\Controllers\Api;


/**
 * Class BlockController
 * @package App\Http\Controllers\Api
 */
class BlockController extends BaseController
{
	/**
	 * @return array
	 */
	public function index()
	{
		return success(['min_block_num'=>getMinBlockNum()]);
    }
}
