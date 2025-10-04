<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent {
    protected $table = 'users';
    protected $guarded = [];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }
}
