<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
require_once app_path().'/helper/helper.php';

class LocationSchedule extends Model
{
    protected $table = 'location_schedule';
    protected $fillable = array('id','location_id','campaign_id','start_date','end_date','repeat_type','repeat_data','repeat_until','is_default','ownder','create_at','update_at');
    public $timestamps = false;
    public static function RetrieveByLocationId($id){
    	$result = LocationSchedule::where('location_id',$id)->orderBy('id','desc')->get();
    	if($result){
    		return $result;
    	}else{
    		return array();
    	}
    }
    
    public static function getActiveCampaignId($nasid,$return_type){
    	$campaign_id = 0;
    	$ssid = "None";
    	$campaign_name = "<label class='badge badge-red'>No campaign assigned</label>";
    	
    	if($nasid != ''){
    		$sql = "SELECT id, IF( LENGTH( time_zone ) > 0, time_zone,  @@system_time_zone ) AS timezone, @@system_time_zone AS server_timezone FROM location WHERE identifier = '$nasid' LIMIT 1";
    		
    		$result = DB::select(DB::raw($sql));    		
    		if($result){
    			$location_id = $result[0]->id;
    			$server_timezone = ini_get('date.timezone');//change
    			$location_timezone = $result[0]->timezone;
    			
    			$sql = "SELECT `campaign_id`, `start_date`, `end_date`, NOW() as current_datetime, `repeat_type`, `repeat_until`, `repeat_data` FROM `location_schedule` WHERE `location_id` = $location_id AND is_default = 0";
    			$result = DB::select(DB::raw($sql));
    			if(count($result) > 0){
    				$all_day = $daily  = $weekly = $monthly = array();
    				foreach($result as $row){
	    				switch ($row->repeat_type) {
							case 'all_day' :
								$all_day [] = $row;
								break;
							case 'daily' :
								$daily [] = $row;
								break;
							case 'weekly' :
								$weekly [] = $row;
								break;
							case 'monthly' :
								$monthly [] = $row;
								break;
						}
    				}  

    				if(count($all_day) > 0 && $campaign_id == 0){
    					foreach ($all_day as $row){
    						$current_time = convertTimeBasedOnTimezone($server_timezone,$location_timezone,$row->current_datetime,'Y-m-d H:i:s',true);
    						$start_time = strtotime($row->start_date);
    						$end_time = strtotime($row->end_date);
    						if($current_time >= $start_time && $current_time < $end_time){
    							$campaign_id = $row->campaign_id;
    							break;
    						}
    					}
    				}
    				
    				if(count($daily) > 0 && $campaign_id == 0){
    					foreach ($daily as $row){
    						$current_date =  convertTimeBasedOnTimezone($server_timezone, $location_timezone, $row->current_datetime, 'Y-m-d',true);
    						$until = strtotime(date('Y-m-d',strtotime($row->repeat_until)));
    						if($current_date <= $until){
    							$current_time = convertTimeBasedOnTimezone($server_timezone, $location_timezone, $row->current_datetime, 'H:i:s',true);
    							$start_time  = strtotime(date('H:i:s',strtotime($row->start_date)));
    							$end_time = strtotime(date('H:i:s',strtotime($row->end_date)));
    							
    							if($current_time >= $start_time && $current_time < $end_time){
    								$campaign_id = $row->campaign_id;
    								break;
    							}
    						}
    					}
    				}
    				
    			if (count ( $weekly ) > 0 && $campaign_id == 0) {
					foreach ( $weekly as $row ) {
						$current_time = convertTimeBasedOnTimezone ( $server_timezone, $location_timezone, $row->current_datetime, 'Y-m-d H:i:s', true );
						$until = strtotime ( $row->repeat_until);
						if ($current_time < $until) {
							$repeat_data = $row->repeat_data;
							if ($repeat_data != "") {
								$repeat_data_array = explode ( ',', $repeat_data );
								$todays_name = convertTimeBasedOnTimezone ( $server_timezone, $location_timezone, $row->current_datetime, 'D', false );
								$todays_name = strtolower ( $todays_name );
								if (in_array ( $todays_name, $repeat_data_array )) {
									$campaign_id = $row->campaign_id;
									break;
								}
							}
						}
					}
				 }
				 
    			if (count ( $monthly ) > 0 && $campaign_id == 0) {
					foreach ( $monthly as $row ) {
						$current_time = convertTimeBasedOnTimezone ( $server_timezone, $location_timezone, $row->current_datetime, 'Y-m-d H:i:s', true );
						$until = strtotime ( $row->repeat_until);
						if ($current_time < $until) {
							$repeat_data = $row->repeat_data;
							if ($repeat_data != "") {
								$repeat_data_array = explode ( ',', $repeat_data );
								$todays_name = convertTimeBasedOnTimezone ( $server_timezone, $location_timezone, $row->current_datetime, 'D', false );
								$todays_name = strtolower ( $todays_name );
								if (in_array ( $todays_name, $repeat_data_array )) {
									$campaign_id = $row->campaign_id;
									break;
								}
							}
						}
					}
				}				 
    			}
    		}else{
    			return "<label class='badge badge-red'>No Location Found!!</label>";
    		}	
    		
    	}else{
    		return "<label class='badge badge-red'>Identifier Missing</label>";
    	}
    	
    	if($campaign_id == 0){
    		$sql = "SELECT `campaign_id` FROM `location_schedule` WHERE `location_id` = $location_id AND `is_default` = 1 LIMIT 1";
    		$result = DB::select(DB::raw($sql));
    		if($result){
    			$campaign_id = $result[0]->campaign_id;
    		}
    	}
    	
    	if($campaign_id == 0){
    		
    	}
    	
    	$sql = "SELECT `id`, `name`, `ssid` FROM `campaign` WHERE `id` = $campaign_id LIMIT 1";
    	$result = DB::select(DB::raw($sql));
    	if($result){
    		$campaign_id = $result[0]->id;
    		$ssid = $result[0]->ssid;
    		$campaign_name = "<a class='badge badge-blue' data-toggle='tooltip' data-placement='top' data-original-title='".$result[0]->name."' href='" . url ( 'campaign/view?camp_id=' ) . $campaign_id . "'>" . trimSentence ( $result[0]->name, 14 ) . "</a>";
    	}
    	
    	if($return_type == 'id'){
    		return $campaign_id;
    	}else if($return_type == 'ssid'){
    		return $ssid;
    	}else if($return_type == 'campaign_name'){
    		return $campaign_name;
    	}
    }
    
    public static function IsDefaultSchedule($location_id){
    	$row = LocationSchedule::where('location_id',$location_id)->where('is_default',1)->select(DB::raw('IFNULL(id,0) AS id'))->first();
    	return $row->id;
    }    
    
}
