<?php

namespace App;

use Illuminate\Support\Facades\Session;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'device';
    protected $fillable = array('id','location_id','name','mac_address','internal_ip','external_ip','update_date','create_date','model','ssid','status','owner');
	public $timestamps = false;
	
    public static function countActiveDevice(){
    	$owner = Session::get('USER_TYPE') == '3'? Session::get('USER_CREATED_BY') : Session::get('USER_ID');
    	return Device::where('status',1)->where('owner',$owner)->count();
    }
    
    public static function RetriveAll($start = 0){
    	$result = Device::select('*');
    	if(Session::get('USER_TYPE') == '1'){
    		$result = $result->orderBy('id','desc');
    	}else if(Session::get('USER_TYPE') == '2'){
    		$result = $result->where('owner',Session::get('USER_ID'))->orderBy('id','desc');
    	}else if(Session::get('USER_TYPE')  == '3'){
    		$result = $result->where('owner',Session::get('USER_CREATED_BY'))->orderBy('id','desc');
    	}
    	$result = $result->get();
    	if($result){
    		return $result;
    	}else{
    		return array();
    	}
    }
    
    public static function RetrieveByLocationID($id){
    	$result = Device::select('*')->where('location_id',$id)->orderBy('id','desc')->get();
    	if($result){
    		return $result;
    	}else{
    		return array();
    	}
    }
    
    
    public static function RetrieveByLocationIdAndStatus($id, $s){
    	$result = Device::where('location_id',$id)->where('status',$s)->orderBy('id','desc')->get();
    	if($result){
    		return $result;
    	}else{
    		return array();
    	}
    }
    
    public static function RetrieveByStatusOff($s){
    	$result = Device::where('status',$s)->orderBy('id','desc')->get();
    	if($result){
    		return $result;
    	}else{
    		return array();
    	}
    }
    
    public static function RetrieveDeviceDropdownAll(){
    	if(Session::get('USER_TYPE') == '1'){
    		$result = Device::select('id','name','mac_address')->where('remove',0)->where('status',1)->orderBy('name','asc')->get();
    	}elseif(Session::get('USER_TYPE') == '2'){
    		$result = Device::select('id','name','mac_address')->where('remove',0)->where('status',1)->where('owner',Session::get('USER_ID'))->orderBy('name','asc')->get();
    	}elseif(Session::get('USER_TYPE') == '3'){
    		$result = Device::select('id','name','mac_address')->where('remove',0)->where('status',1)->where('owner',Session::get('USER_CREATED_BY'))->orderBy('name','asc')->get();
    	}
    	
    	if(!$result){
    		return array();
    	}else if(count($result) <= 0){
    		return array();
    	}else{
    		return $result;
    	}
    }
    
	public static function checkDuplicateMAC($device_id = "", $mac_address) {
		$exist = false;
		$results = Device::select('id');			
		if($device_id != ""){
			$results  = $results->where('device_id','!=','')->where('mac_address',$mac_address);			
		}else{
			$results = $results->where('mac_address',$mac_address);			
		}
		$results = $results->get();
		if(count($results) > 0){
			$exist = true;
		}
		return $exist;
	}
}

























