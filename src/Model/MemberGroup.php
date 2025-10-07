<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class MemberGroup extends Eloquent
{
    protected $table = 'member_group';
    public $timestamps = false; 

    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
