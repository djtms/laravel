<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use DB;
use App\Http\Requests;

class AppInfo extends Model
{
    protected $table = 'app_info';
    
    protected $fillable = array('id','app_id','app_name','app_secrect','status','type','connections','date_added','is_default','owner','remove');    
    public $timestamps = false;
    public static function getByAppType($type){
    	$user_type = Session::get('USER_TYPE');
    	$result = null;
    	if($user_type == '1'){
    		$result = AppInfo::where('remove',0)->where('type',$type)->get();
    	}else if($user_type == '2'){
    		$result = AppInfo::where('remove',0)->where('type',$type)->where('owner',Session::get('USER_ID'))->get();
    	}else if($user_type == '3'){
    		$result  = AppInfo::where('remove',0)->where('type',$type)->where('owner',Session::get('USER_CREATED_BY'))->get();
    	}
    	
    	if(!$result){
    		return array();
    	}
    	return $result;
    }
    
    
    public static function RetrieveAll($start = 0,$limit = 10){
    	$result = AppInfo::select('id','app_name','app_id','app_secrect','status',DB::raw("IFNULL( (SELECT COUNT( social_user_id ) FROM `social_user` WHERE `app_info_id` = id ) , 0) AS connections"),'date_added','type','is_default','owner');
    	if(Session::get('USER_TYPE') == '1'){
    		$result = $result->where('remove',0)->orderby('id','desc');
    	}else if(Session::get('USER_TYPE') == '2'){
    		$result = $result->where('remove',0)->where('owner',Session::get('USER_ID'))->orderBy('id','desc');
    	}elseif(Session::get('USER_TYPE') == '3'){
    		$result = $result->where('remove',0)->where('owner',Session::get('USER_CREATED_BY'))->orderBy('id','desc');
    	}
    	
    	$result = $result->get();
    	if($result){
    		return $result;
    	}else{
    		return array();
    	}
    }
    
    public static function SetDefault($id,$type_id){
    	$num = AppInfo::where('type',$type_id)->update(array('is_default'=>0));
    	if($num > 0){
    		$num = AppInfo::where('id',$id)->update(array('is_default'=>1));
    		if($num > 0){
    			return true;
    		}else{
    			return null;
    		}
    	}else{
    		return null;
    	}
    }
}
