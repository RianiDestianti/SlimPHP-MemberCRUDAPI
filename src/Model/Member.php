<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Member extends Eloquent {
    protected $table = 'members';
    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class, 'member_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'member_group', 'member_id', 'group_id')
                    ->withPivot('role', 'joined_at');
    }
}
