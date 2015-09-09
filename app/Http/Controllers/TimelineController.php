<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Location;
use App\SocialUser;
use DB;
use App\SocialUserDeviceInfo;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
require app_path().'/helper/helper.php';

class TimelineController extends Controller
{
    public function __construct(){
    	view()->share('controller','timeline');
    	$this->middleware('auth');
    	$this->middleware('boot');
    }
    
    public function view(Request $request){
    	view()->share('actions','view');
    	$data = array();
    	$sql = "SELECT SQL_CALC_FOUND_ROWS su.social_user_id AS id, su.campaign_id, su.location_id, 
				d.id AS device_id, su.social_network, su.full_name, su.picture_url, c.name AS campaign, IF( LENGTH( l.name ) <= 0,  'N/A', l.name ) AS location, 
				d.name AS device, su.added_datetime, u.time_zone AS owner_timezone, l.time_zone AS location_timezone, 
				@@system_time_zone AS server_timezone
				FROM `social_user` AS su
				LEFT JOIN `location` AS l ON su.location_id = l.id
				LEFT JOIN `device` AS d ON su.device_mac = d.mac_address
				LEFT JOIN `campaign` AS c ON su.campaign_id = c.id
				LEFT JOIN `user` AS u ON u.id = l.owner
				WHERE su.full_name != '' ";
    	switch(Session::get('USER_TYPE')){
    		case '2':
    			$l_ids = '';
    		 $result = Location::select('id')->where('remove',0)->where('owner',Session::get('USER_ID'))->get();
    			if(count($result) > 0){
    				foreach($result as $row){
    				   $l_ids .= $row->id.",";
    				}
    			}
    			$location_ids = $l_ids != "" ? rtrim ( $l_ids, ',' ) : 0;
				$sql .= "AND su.location_id IN($location_ids)";
    			break;
    		case '3':
    			$location_ids = Session::get('LOCATION_IDS') == null?0:Session::get('LOCATION_IDS');
    			$sql .= "AND su.location_id IN($location_ids)";
    			break;
    	}
    	
    	$sql.= " ORDER BY su.social_user_id DESC LIMIT 0, 9";
    	$result = DB::select(DB::raw($sql));
    	$data['currently_showing'] = count($result);
    	$mydata = array();
    	foreach($result as $row){
    		$added_datetime = $row->added_datetime;
    		$server_timezone = ini_get('date.timezone');//change
    		$user_timezone = $row->location_timezone;
    		if($user_timezone == ''){
    			$user_timezone = $row->owner_timezone;
    		}
    		
    		if($user_timezone != ''){
    			$user_timezone = ini_get('date.timezone');//change
    		}
    		
    		$row->added_datetime = convertTimeBasedOnTimezone($server_timezone, $user_timezone, $added_datetime, 'd M, Y @ h:i A (P)');
    		
    		$device_info = SocialUserDeviceInfo::where('suid',$row->id)->select('os_name','model')->orderBy('created_at','desc')->first();
    		if($device_info){
    			$row->os_name = $device_info->os_name;
    		}else{
    			$row->os_name = '';
    		}    		
    		$mydata[] = $row;  		
    	} 
    	$data['results'] = $mydata;
    	$data['total_found']  = SocialUser::count();    	
    	return view('timeline.view',$data);   	
    }
}
