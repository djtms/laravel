<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class DeviceStatus extends Model
{
    protected $table = 'device_status';
    protected $fillable  = array("id","device_id","mac","mac1","mac2","mac3","mac4","device_status_details","status_created_on");
    public $timestamps = false;
    public static function getDeviceStatus($mac){
    	$output = array(
    		'ssid' => 'None',
    	    'status_mode'=>'<span class="circle circle_notconnected" data-toggle="tooltip" data-placement="right" title="Never Connected"></span>',
    	    'status_class'=>'primary',
            'status_text'=>'never_connected'
    	);
    	if($mac != ""){    		
    		$sql = "SELECT `device_status_details`, `status_created_on`, NOW() AS 'current_datetime' FROM `device_status` WHERE `mac` = '$mac' OR `mac1` = '$mac' OR `mac2` = '$mac' OR `mac3` = '$mac' OR `mac4` = '$mac' LIMIT 1";
    		$result = DB::select(DB::raw($sql));	
    		$device_status_details = '';     		
    		if($result){
    			$device_status_details = $result[0]->device_status_details;
    		}    		    		
    		if($device_status_details !=''){
    			$device_data = json_decode($device_status_details);     		
    			
    			$output['ssid'] = isset($device_data->ssid) && $device_data->ssid != ''? $device_data->ssid: "None";
    			if($result[0]->status_created_on !=""){
    				$uptime = strtotime($result[0]->status_created_on);
    				$current_time = strtotime($result[0]->current_datetime);
    				$subtract_time = strtotime(date('Y-m-d H:i:s',strtotime('-7 minutes',$current_time)));
    				if(($uptime > $subtract_time) && ($uptime < $current_time)){
    					$output['status_mode'] = "<span class='circle circle_online' data-toggle='tooltip' data-placement='right' title='Online'></span>";
    					$output['status_class'] = "primary";
    					$output['status_text'] = "online";
    				}else{
    					$output['status_mode'] = "<span class='circle circle_offline' data-toggle='tooltip' data-placement='right' title='Offline'></span>";
    					$output['status_class'] = "primary";
    					$output['status_text'] = "offline";
    				}
    			}else{
    				$output['status_mode']="<span class='circle circle_notconnected' data-toggle='tooltip' data-placement='right' title='Never Connected'></span>";
    				$output['status_class'] = 'primary';
    				$output['status_text'] = 'never_connected';
    			}
    		}
    	}
    	
    	return $output;
    }
}
