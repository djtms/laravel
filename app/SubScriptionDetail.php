<?php

namespace App;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SubScriptionDetail extends Model
{
   protected $table = 'subscription_detail';
   
   protected $fillable = array('id','user_id','account_code','account_status','subscription_id','plan_code','allowed_quantity','setup_fee','plan_price','plan_interval','activated_at','current_period_started_at','current_period_ends_at','created_at');
   public $timestamps = false;
   public static function countAllowedDevice(){
   	 $allowed_device = 0;
   	 $owner = Session::get('USER_TYPE') == '3'? Session::get('USER_CREATED_BY') : Session::get('USER_ID');
   	 $record = SubScriptionDetail::where('user_id',$owner)->select('allowed_quantity as allowed_device')->first();
   	 if($record){
   	 	$allowed_device = $record->allowed_device;
   	 }   	 
   	 return $allowed_device;
   }
   
   
}
