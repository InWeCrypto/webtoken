<?php

namespace App\Model;

use App\Services\Push;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 *
 * Class IcoList
 * @package App\Model
 */
class IcoList extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		"symbol",
		"name",
	];

	protected $table = 'ico_list';

}
