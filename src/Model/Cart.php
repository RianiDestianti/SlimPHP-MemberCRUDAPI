<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Cart extends Eloquent {
    protected $table = 'carts';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
