<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Order extends Eloquent {
    protected $table = 'orders';
    protected $guarded = [];
    
    public $timestamps = false;

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}