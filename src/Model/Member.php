<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Member extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'members';

    protected $guarded = [];
}