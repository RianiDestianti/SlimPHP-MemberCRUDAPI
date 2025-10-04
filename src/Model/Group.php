<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Group extends Eloquent {
    protected $table = 'groups';
    protected $guarded = [];

    public function members()
    {
        return $this->belongsToMany(Member::class, 'member_group', 'group_id', 'member_id')
                    ->withPivot('role', 'joined_at');
    }
}
