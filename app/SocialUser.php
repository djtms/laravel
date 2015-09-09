<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialUser extends Model
{
    protected $table = 'social_user';
    protected $fillable = array('social_user_id','user_id','social_network_id','social_network','username','email','full_name','gender','age','timezone','device_mac','client_mac','location_id','picture_url','campaign_id','app_info_id','return','added_datetime');
    public $timestamps = false;
}
