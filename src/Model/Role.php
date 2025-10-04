<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Role extends Eloquent {
    protected $table = 'roles';
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
