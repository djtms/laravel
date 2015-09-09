<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocationUser extends Model
{
    protected $table = 'location_user';
    protected $fillable = array('id','first_name','last_name','email_username','mobile_phone','access_level','location_id','user_id','status');
    public $timestamps = false;
    
}
