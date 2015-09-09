<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'message';
    protected $fillable = ['id','time','user_id','message','status'];
    public $timestamps = false;
}
