<?php

namespace App;

use Illuminate\Support\Facades\Session;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'location';
    protected $fillable = array('id','identifier','name','location','address','country','state','website','time_zone','status','owner','url');
    public $timestamps = false;
    public static function RetrieveAll($start = 0,$limit = 10){
    	if(Session::get('USER_TYPE') != null && Session::get('USER_TYPE') == '1'){
    		$result = Location::where('remove',0)->orderBy('id','desc')->get();
    	}else if(Session::get('USER_TYPE') != null && Session::get('USER_TYPE') == '2'){
    		$result= Location::where('remove',0)->where('owner',Session::get('USER_ID'))->orderBy('id','desc');
    	}else if(Session::get('USER_TYPE') && Session::get('USER_TYPE') == '3'){
    		$result = Location::where('remove',0)->whereIn('id',explode(',',Session::get('LOCATION_IDS')))->orderBy('id','desc');
    	}
    	if($result){
    		return $result;
    	}else{
    		return array();
    	}
    }
    
    public static function RetrieveLocationDropdown(){
    	 $result = Location::where('remove',0)->where('status',1)->select('id','name');
    	 if(Session::get('USER_TYPE') == '1'){
    	 	$result = $result->orderBy('name','asc');
    	 }elseif(Session::get('USER_TYPE') == '2'){
    	 	$result = $result->where('owner',Session::get('USER_ID'))->orderBy('name','asc');
    	 }elseif(Session::get('USER_TYPE') == '3'){
    	 	$result = $result->whereIn('id',explode(Session::get('LOCATION_IDS')))->orderBy('name','asc');
    	 }
    	 $result = $result->get();
    	 if(count($result) > 0){
    	 	return $result;
    	 }else{
    	 	return array();
    	 }
    }
    
    public static function getNasId($location_id = '',$device_mac = ''){
    	$nasid = '';
    	if($location_id != ''){
    		$result = Location::select('identifier')->where('id',$location_id)->first();
    	}else{
    		$result = Location::join('device','location.id','=','device.location_id')->select('location.identifier')->where('device.mac_address',$device_mac)->first();
    	}
    	
    	if($result){
    		$nasid = $result->identifier;
    	}
    	return $nasid;
    }
}
