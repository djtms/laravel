<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialUserDeviceInfo extends Model
{
    protected $table ='social_user_device_info';
    protected $fillable  = array('id','suid','campaign_id','location_id','session_id','plan','user_agent','brower_type','browser_name','browser_version','browser_engine','os_name','os_version','device','brand','model','client_ip','client_mac','device_mac','created_at');
    
    public $timestamps = false;
}
