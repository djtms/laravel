<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array('id', 'email_address', 'password','full_name','is_active','token','created_at','is_admin','modified',
                           'first_name','last_name','mobile_phone','company_name','location_phone_number','venue_address1','venue_address2',
                           'state','town_city','zip','country','industry','website_url','time_zone','user_type_id','created_by','site_id');
    public $timestamps = false;
    public static function RetriveByEmailAddress($email_address){
    	$result = User::where('email_address',$email_address)->where('remove',0)->first();
    	if($result){
    		return $result;
    	}else{
    		return null;
    	}
    }
    
    public static function guid() {
        mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $uuid = chr(123)// "{"
                . substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12)
                . chr(125); // "}"
        return $uuid;
    }
}
