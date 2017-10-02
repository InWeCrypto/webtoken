<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $fillable = [
		'name',
		'is_valid',
		'module_ids'
	];


	public function hasUser()
	{
		return $this->hasMany(Admin::class, 'role_id');
	}
}
