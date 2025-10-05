<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MemberGroup extends Pivot {
    protected $table = 'member_group';
    protected $guarded = [];

    public function member() {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function group() {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
