<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleFunction extends Model
{
    protected $table  = 'role_function';
    protected $fillable = ['id','role_id','function_id'];
    public $timestamps = false;
}
