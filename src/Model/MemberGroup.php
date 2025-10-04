<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MemberGroup extends Pivot {
    protected $table = 'member_group';
    protected $guarded = [];
}
