<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $table = 'user_permission';
    protected $fillable = array('user_permission_id','user_id','module_ids','location_ids','campaign_ids');
    public $timestamps = false;
}
