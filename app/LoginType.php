<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginType extends Model
{
    protected $table = 'login_type';
    protected $fillable = ['id','language'];
    public $timestamps = false;
}
