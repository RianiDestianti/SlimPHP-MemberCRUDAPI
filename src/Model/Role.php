<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Role extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'roles';

	protected $guarded = [];
}
