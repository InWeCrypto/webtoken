<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
	protected $fillable = [
		'name',
		'p_id',
		'uri'
	];
}
