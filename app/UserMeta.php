<?php

namespace App;

use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    protected $table = 'user_meta';
    
    protected $fillable = array('id','user_id','meta_key','meta_value');
    public $timestamps = false;
    
    public static function addUserMeta($meta_key,$meta_value,$user_id =''){
    	if($user_id == ''){
    		$user_id = Session::get('USER_ID');
    	}
    	$record = UserMedta::where('user_id',$user_id)->where('meta_key',$meta_key)->first();
    	if(!$record){
	    	$record->meta_value = $meta_value;
	    	$record->save();
    	}else{
    		$record = new UserMeta;
    		$record->user_id = $user_id;
    		$record->meta_key = $meta_key;
    		$record->meta_value = $meta_value;
    		$record->save();
    	}
    }
    
    public static function getUserMeta($meta_key,$user_id = ''){
    	$output = '';
    	if($user_id == ''){
    		$user_id = Session::get('USER_ID');
    	}
    	$record = UserMeta::where('user_id',$user_id)->where('meta_key',$meta_key)->first();
    	if($record){
    		$output = $record->meta_value;
    	}
    	return $output;
    }
}
