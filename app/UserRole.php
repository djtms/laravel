<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_role';
    protected $fillable = ['id','project_id','user_id','role_id','is_project_default','page','rows','closed_hide','orderby','sort'];
    public $timestamps = false;
}
