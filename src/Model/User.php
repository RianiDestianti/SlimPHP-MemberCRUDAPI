<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	protected $guarded = [];

	public function role()
	{
		return $this->belongsTo(Role::class, 'role_id');
	}
}
