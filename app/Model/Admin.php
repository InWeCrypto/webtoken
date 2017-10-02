<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;

/**
 * Class Admin
 * @package App\Model
 */
class Admin extends Authenticatable implements AuthenticatableUserContract
{
	/**
	 * @var array
	 */
	protected $hidden = [
		"password",
		"p_id",
		"is_valid",
	];
	/**
	 * @var array
	 */
	protected $fillable = [
		"name",
		"nickname",
		"password",
		"img",
		"ip",
		"p_id",
		"role_id",
		"is_valid",
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function role()
	{
		return $this->belongsTo(Role::class, 'role_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function hasUser()
	{
		return $this->hasMany(self::class, 'p_id');
	}

	/**
	 * @param $value
	 */
	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = password_hash($value,PASSWORD_DEFAULT);
	}

	/**
	 * @return mixed
	 */
	public function getJWTIdentifier()
	{
		return $this->getKey(); // Eloquent model method
	}

	/**
	 * @return array
	 */
	public function getJWTCustomClaims()
	{
		return [];
	}
}
