<?php

namespace App\Http\Controllers;

use App\UserPermission;

use App\LocationMeta;

use App\AppInfo;

use App\Campaign;

use App\SocialUser;

use App\RadpostAuth;

use App\LocationSchedule;

use App\LocationUser;

use App\Location;

use App\SubScriptionDetail;
use App\User;
use App\Device;
use DB;
use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
require_once app_path().'/helper/helper.php';
require_once app_path().'/helper/deviceinfo.php';

class LocationController extends Controller
{
  
  public function __construct(){
  	view()->share('controller','location');
  	$this->middleware('auth');
  	$this->middleware('boot');
  }
  
  public function view(){
  	 view()->share('action','view');
  	 $data['timezones'] = get_timezones();
  	 return view('location.view',$data);
  }
  
  public function overview(Request $request){
  	view()->share('action','overview');
  	$loca = $request->has('loca')?$request->input('loca'):'';
  	$owner = Session::get('USER_TYPE') == '3'?Session::get('USER_CREATED_BY') : Session::get('USER_ID');
  	$flag = $this->checkAuthorizedLocation($request);
  	if($flag == false){
  		return redirect('location/unauthorizedaccess');
  	}
  	$device_limit_msg = $message = '';
  	$active_device = Device::countActiveDevice();
  	$allowed_device = SubScriptionDetail::countAllowedDevice();
  	if(Session::get('USER_TYPE') != '1' && $active_device >=  $allowed_device){
  		$message = "You are currently using $allowed_device out of the $allowed_device active devices included in your plan.";
  		if(Session::get('USER_TYPE') == '2'){
  			$message .= " To add more active devices to your account, <a href=".url('user/editprofile&get=device').">click here</a>";
  		}
  	}
  	if($message != ""){
  		$device_limit_msg = GenerateConfirmationMessage('danger', $message);
  	}
  	
  	$location = new Location;
  	if($request->isMethod('post')){
  		if($request->input('submit') == "Save"){
  			if($request->has('action') && $request->input('action') == "edit_device"){
  				$device = Device::where('id',$request->input('hdn_device_id'))->first();
  				$device->name = $request->input('device_name');  				
  				if($device->save()){
  					$message = GenerateConfirmationMessage('success', 'Device has been successfully updated.');
  				}else{
  					$message = GenerateConfirmationMessage('danger', 'Device has not been successfully updated.');
  				}
  				Session::put('TAB','hardware');
  				Session::put('SESSION_MESSAGE',$message);
  				return redirect('location/view?loca='.$loca);
  			}
  			
  			if($request->has('action') && $request->input('action') == 'add_user'){
  				$first =$request->input('first_name');
  				$last =$request->input('last_name');
  				$email_username = $request->input('email_username');
  				$mobile_phone = $request->input('mobile_phone');
  				$access_level = $request->input('access_level');
  				$location_id = $request->input('location_id');
  				$newrecord = new LocationUser;
  				$newrecord->first_name = $first;
  				$newrecord->last_name = $last;
  				$newrecord->email_username = $email_username;
  				$newrecord->mobile_phone = $mobile_phone;
  				$newrecord->access_level = $access_level;
  				$newrecord->location_id = $location_id;
  				if(!$newrecord->save()){
  					die('Data Error,Pls cehck again!');
  				}
  			}
  			
  			if($request->has('action') && $request->input('action') == 'edit_user')
  			{
  				$id = $request->input('user_id');
  				$first_name = $request->input('first_name');
  				$last_name = $request->input('last_name');
  				$email_username = $request->input('email_username');
  				$mobile_phone = $request->input('mobile_phone');
  				$access_level = $request->input('access_level');
  				$location_id = $request->input('location_id');
  				User::where('id',$id)->update(
  				  array(
  				     'first_name'=>$first_name,
  				     'last_name'=>$last_name,
  				     'email_username'=>$email_username,
  				     'mobile_phone'=>$mobile_phone,
  				     'access_level'=>$access_level,
  				     'location_id'=>$location_id
  				  )
  				);  				
  			}
  			
  			if($request->has('action') && $request->input('action') == 'create_schedule'){
  				if($request->has('ddl_active_campaign') && !empty($request->input('ddl_active_campaign'))){
  					if($request->has('all_day')){
  						$all_day = '1';
  					}else{
  						$all_day = '0';
  					}
  					$start = $request->input('start_date')." ".$request->input('start_time');
  					$end = $request->input('end_date')." ".$request->input('end_time');
  					
  					$start_date = new DateTime($start);
  					$end_date = new DateTime($end);
  					
  					$location_schedule = new LocationSchedule;
  					$location_schedule->location_id = $loca;
  					$location_schedule->campaign_id = $request->input('ddl_active_campaign');
  					$location_schedule->start_date = $start_date->format('Y-m-d H:i:s');
  					$location_schedule->end_date = $end_date->format('Y-m-d H:i:s');
  					$location_schedule->create_at = gmdate('Y-m-d H:i:s',time() + 3600 *(-4 + date('I')));
  					$location_schedule->is_default = 0;
  					$location_schedule->repeat_data = $request->input('repeat');
  					$location_schedule->owner = $owner;
  					$location_schedule->repeat_type = $all_day;  					
  					
  					if($request->input('repeat') != "no"){
  						$location_schedule->repeat_data = $request->input('repeat_every');
  						$location_schedule->repeat_until = $request->input('until');

  						if($request->input('repeat') == "weekly"){
  							$array_date = '';
  						}
  						if($request->input('week_show') != 8){
  							$i = 0;
  							$array_data = explode(',',$request->input('week_show'));
  							$c = count($array_data);
  							foreach($array_data as $data){
  								switch($data){
  									case 1:
  										$array_date = $array_date."Sunday";
  										break;
  									case 2:
  										$array_date = $array_date."Monday";
  										break;
  									case 3:
  										$array_date = $array_date."Tuesday";
  										break;
  									case 4:
  										$array_date = $array_date."Wednesday";
  										break;
  									case 5:
  										$array_date = $array_date."Thursday";
  										break;
  									case 6:
  										$array_date = $array_date."Friday";
  										break;
  									case 7:
  										$array_date = $array_date."Saturday";
  										break;
  								}
  								$i++;
  								if($i < $c){
  									$array_date = $array_date.',';
  								}
  							}
  						}else{
  							$array_date ='All Week';
  						}
  						
  						$location_schedule->repeat_data = $array_date;
  						if($request->input('repeat') =="monthly"){
  							$location_schedule->repeat_data = $request->input('month_show');
  						}
  					}
  					if($request->has('chk_default_campaign') && $request->input('chk_default_campaign') == "on"){
  						LocationSchedule::where('location_id',$loca)->update(array('is_default'=>0));
  					}
  					
  					if(!$location_schedule->save()){
  						Session::put('SESSION_MESSAGE',"<div class=\"alert alert-danger\"><strong>Error: </strong>Can not save.</div>");
  						return view('location.view',$location_schedule);
  					}else{
  						Session::put('SESSION_MESSAGE',"<div class=\"alert alert-success\"><strong>Success: </strong>Save schudule successfully!</div>");
  					}
  				}
  				$location_schedule = new LocationSchedule;
  			}
  			if($request->has('action') && $request->input('action') == 'edit_schedule'){
  				if($request->has('all_day')){
  					$all_day = '1';
  					$start = explode('/',$request->input('start_date_2'));
  					$start = implode('-',$start);
  					$start = $start." 00:00:00";
  					
  					$end = explode('/',$request->input('end_date_2'));
  					$end = implode('-',$end);
  					$end = $end." 00:00:00";
  				}else{
  					    $all_day = '0';
						$start = explode ( '/', $request->input("start_date"));
						$start = implode ( '-', $start );
						$start_time = explode ( ' ', $request->input('start_time'));
						$start_time_hour = explode ( ':', $start_time [0] );
						if ($start_time [1] == 'PM')
							$start_time_hour [0] = $start_time_hour [0] + 12;
						$start_time [0] = implode ( ':', $start_time_hour );
						$start = $start . " " . $start_time [0];
						
						$end = explode ( '/', $request->input("end_date"));
						$end = implode ( '-', $end );
						$end_time = explode ( ' ', $request->input('end_time'));
						$end_time_hour = explode ( ':', $end_time [0] );
						if ($end_time [1] == 'PM')
							$end_time_hour [0] = $end_time_hour [0] + 12;
						$end_time [0] = implode ( ':', $end_time_hour );
						$end = $end . " " . $end_time [0];
  				}
  				
  				$start_date = new DateTime($start);
  				$end_date = new DateTime($end);
  				
  				$location_schedule = new LocationSchedule;
  				$location_schedule->id = $request->input('id_schedule');
  				$location_schedule->location_id = $loca;
  				$location_schedule->campaign_id = $request->input('campaign_id');
  				$location_schedule->start_date = $start_date->format('Y-m-d H:i:s');
  				$location_schedule->end_date = $end_date->format('Y-m-d H:i:s');
  				$location_schedule->repeat_data  = $request->input('repeat');
  				$location_schedule->owner = $owner; 

  				if($request->input('repeat') != "no"){
  					$location_schedule->repeat_type = $request->input('repeat_every');
  					$location_schedule->repeat_until = $request->input('until');
  					
  					if($request->input('repeat') == 'weekly')
  						$array_date  = '';
  					if($request->input('week_show') != 8){
  						$i = 0;
  						$array_data = explode(',',$request->input('week_show'));
  						$c = count($array_data);
  						foreach ($array_data as $data){
  							switch($data){
  								case 1:
  									$array_date = $array_date."Sunday";
  									break;
  								case 2:
  									$array_date = $array_date.'Monday';
  									break;
  								case 3:
  									$array_date = $array_date.'Tuesday';
  									break;
  								case 4:
  									$array_date = $array_date."wednesday";
  									break;
  								case 5:
  									$array_date = $array_date."Thurday";
  									break;
  								case 6:
  									$array_date = $array_date."Friday";
  									break;
  								case 7:
  									$array_date = $array_date."Saturday";
  									break;
  							}
  							$i++;
  							if($i < $c)
  							  $array_date = $array_date.',';
  						}
  					}else{
  						$array_date = 'All Week';
  					}
  					
  					$location_schedule->repeat_data = $array_date;
  					if($request->input('repeat') == 'monthly')
  						$location_schedule->repeat_data = $request->input('month_show');
  				}
  				
  				if(!$location_schedule->save()){
  					Session::put('SESSION_MESSAGE',"<div class=\"alert alert-danger\"><strong>Error: </strong>Can not save.</div>");
  					return view('location.view',$location_schedule);
  				}else{
  					Session::put('SESSION_MESSAGE',"<div class=\"alert alert-success\"><strong>Success: </strong>Edit schudule successfully!</div>");
  				}
  				$location_schedule = new LocationSchedule;
  			}
  		}
  	}
  	if(isset($loca)){
  		$data['device_mac'] = '';
  		$record = Device::select('mac_address')->where('location_id',$loca)->where('status',1)->first();
  		if($record){
  			$data['device_mac'] = $record->mac_address;
  		}
  		
  		$location = Location::where('id',$loca)->first();  		
  		$data['online_user'] = $this->getOnlineUser($location->identifier);
  		$location_edit = Location::RetrieveAll();
  		$campaign = Campaign::RetrieveCampaignDropdown();
  		$campaign_loca_status = Campaign::GetActiveCampaign($loca);
  		$device_all = Device::RetriveAll();
  		$device_loca = Device::RetrieveByLocationID($loca);
  		$device_loca_on = Device::RetrieveByLocationIdAndStatus($loca,'1');
  		$device_status = Device::RetrieveByStatusOff(0);
  		$loca_schedule =  LocationSchedule::RetrieveByLocationId($loca);
  		$facebook = AppInfo::getByAppType(1);
  		$twitter = AppInfo::getByAppType(2);
  		$google = AppInfo::getByAppType(3);
  		$linkedin = AppInfo::getByAppType(4);
  		$facebooklike = AppInfo::getByAppType(1);
  		
  	    $sql = "SELECT ls.id, c.name, ls.start_date, ls.end_date, ls.repeat_type, ls.repeat_until,
					ls.repeat_data, IF( LENGTH( l.time_zone ) >0, l.time_zone,  @@system_time_zone ) AS timezone, @@system_time_zone AS server_timezone FROM location_schedule AS ls
					INNER JOIN campaign AS c ON ls.campaign_id = c.id
					LEFT JOIN location AS l ON  l.id = ls.location_id
					WHERE ls.location_id = " . $loca . " AND ls.is_default = 0";
			$scheduledCampaign = array();
			$result = DB::select(DB::raw($sql));
			$calendar_data ='';
			if(count($result) > 0){
				foreach($result as $record){
					$temp['id'] = $record->id;
					$temp['campaign_name'] = $record->name;
					$temp['repeat_type']  =$record->repeat_type;
					if($record->repeat_type == 'all_day'){
						$temp['start_date'] = date('d M, Y h:i a',strtotime($record->start_date));
						$temp['end_date'] = date('d M, Y h:i a',strtotime($record->end_date));
					}else if($record->repeat_type =='daily'){
						$temp['start_date'] = date('h:i a',strtotime($record->start_date));
						$temp['end_date'] = date('h:i a',strtotime($record->end_date));
					}
					
					$temp['repeat_data'] = ucfirst(str_replace('_', ' ', $record->repeat_data));
					
					if($record->repeat_until != '0000-00-00 00:00:00'){
						$temp['repeat_until'] = date('d M, Y', strtotime($record->repeat_until));
					}else{
						$temp['repeat_until'] = "N/A";
					}
					
					$scheduledCampaign[] = $temp;
					
					$start_date = date('Y-m-d', strtotime($record->start_date));
					$start_time = date('h:i:s', strtotime($record->start_date));
					$start_datetime = $start_date.'T'.$start_time;
					
					$end_date = date('Y-m-d', strtotime($record->end_date));
					$end_time = date('h:i:s', strtotime($record->end_date));
					$end_datetime = $end_date.'T'.$end_time;
					
					$calendar_data .= "{title:'".$record->name."',start:'$start_datetime',end:'$end_datetime',allDay:false},";
					
				}
			}
			$calendar_data = rtrim($calendar_data,',');
			
		 $data['device_details'] = get_device_info();
		 $data['loca'] = $loca;
		 $data['nasid']  = '';
		 $data['location']  =$location;
		 if($location){
		 	Session::put('current_location_status',$location->status);
		 	$data['nasid'] = $location->identifier;
		 }		 
		 if(count($loca_schedule) > 0){
		 	foreach($loca_schedule as $row){
		 		$data['first_cam'] = $row->campaign_id;
		 		break;
		 	}
		 }
		 $data['scheduledCampaign'] = $scheduledCampaign;
		 $data['location_list'] = Location::RetrieveLocationDropdown();
		 
		 $male = $female = 0;
		 $ouput =  array();
		 $sql = "SELECT SUM( gender = 'male' ) AS male, SUM( gender = 'female' ) AS female FROM social_user WHERE location_id = " . $loca;
		 $results =  DB::select(DB::raw($sql));
		 if(count($results) > 0){
		 	foreach($results as $result){
		 		$output[] = $result;
		 		$male = $male +$result->male;
		 		$female = $female + $result->female;
		 	}
		 }
		 
		 $data['male'] = $male;
		 $data['female'] = $female;
		 $data['ouput'] = $ouput;
		 
		 $sql  = "SELECT "
		        . "SUM( social_network = 'FBuser' ) AS fb, "
		        . "SUM( social_network = 'LIuser' ) AS li, "
		        . "SUM( social_network = 'TWuser' ) AS tw, "
		        . "SUM( social_network = 'GPuser' ) AS gp, "
		        . "SUM( social_network = 'IGuser' ) AS ig, "
		        . "SUM( social_network = 'Cuser' ) AS cu "
		        . "FROM social_user WHERE location_id = " . $loca;
		        
		 $fbuser = $twuser = $liuser = $gpuser = $iguser = $customer = 0;
		 
		 $results = DB::select(DB::raw($sql));
		 if(count($results) > 0){
		 	foreach($results as $row){
		 		$fbuser +=$row->fb;
		 		$twuser +=$row->tw;
		 		$liuser +=$row->li;
		 		$gpuser +=$row->gp;
		 		$iguser +=$row->ig;
		 		$customer +=$row->cu;
		 	}
		 }
		 
		 $data['fbuser'] = $fbuser;
		 $data['twuser'] = $twuser;
		 $data['liuser'] = $liuser;
		 $data['gpuser'] = $gpuser;
		 $data['iguser'] = $iguser;
		 $data['customer'] = $customer;
		 
		 $data['active_device'] = Device::countActiveDevice();
		 $data['allowed_device'] = SubScriptionDetail::countAllowedDevice();
		 $tab = Session::get('TAB') && Session::get('TAB') != "" ? Session::get('TAB') : "overview";
		 $data['tab'] = $tab;
		 $data['campaign'] = $campaign;
		 $data['campaign_loca_status']  =$campaign_loca_status;
		 $data['campaign_name'] = LocationSchedule::getActiveCampaignId($data['nasid'], 'campaign_name');
		 
		 $selected_device = array();
		 $sql = "SELECT d.id, d.name, d.model, d.mac_address, ds.device_status_details as device_status FROM device AS d "
                                    . "LEFT JOIN device_status AS ds ON ds.device_id = d.id "
                                    . "WHERE d.location_id = " . $request->input('loca') . " ORDER BY d.name ASC";
                                    
          $query = DB::select(DB::raw($sql));
          if($query){
          	$data['device_rows'] = $query;
          	foreach($query as $row){
          		$selected_device[] = $row->id;
          	}
          }else{
          	$data['device_rows'] = array();
          }
          
         $data['selected_device'] = $selected_device;
		 $data['max_bandwidth'] = LocationMeta::getLocationMeta($request->input('loca'), 'max_bandwidth',25600);
		 $data['session_time_limit'] = LocationMeta::getLocationMeta($request->input('loca'), 'session_time_limit',24);
		 
		 $sql = "SELECT u.id, u.full_name, u.email_address FROM `user` AS u INNER JOIN `user_permission` AS up ON up.user_id = u.id WHERE up.location_ids LIKE '%" . $request->input('loca') . "%' AND u.remove = 0";
         $sub_user_list = array();
         $query = DB::select(DB::raw($sql));
         if($query){
         	foreach($query as $user){
         		$sub_user_list[$user->id] = $user->full_name;
         	}
         }   
         
         $data['sub_user_list'] = $sub_user_list;
         $data['users'] =$query;    
         $data['time_zones'] = get_timezones();        
         $data['device_list'] = Device::RetrieveDeviceDropdownAll();
         $subusers = User::where('user_type_id',3)->where('remove',0)->select('id','full_name');
         if(Session::get('USER_TYPE') == '2'){
         	$subusers = $subusers->where('created_by',Session::get('USER_ID'));
         }else if(Session::get('USER_TYPE') == '3'){
         	$subusers = $subusers->where('created_by',Session::get('USER_CREATED_BY'));
         }    
         $subusers = $subusers->get();    
         $data['sub_users'] = $subusers; 
         $data['week_array'] = array (
									'mon' => 'Monday',
									'tue' => 'Tuesday',
									'wed' => 'Wednesday',
									'thu' => 'Thursday',
									'fri' => 'Friday',
									'sat' => 'Saturday',
									'sun' => 'Sunday' 
							  ); 
		$data['days_of_month'] = array(
				'mon' => 'First day of week',
				'tue' => 'Second day of week',
				'wed' => 'Third day of week',
				'sun' => 'Last day of week' 
		);
        $data['device_limit_msg'] = $device_limit_msg;							                         
  	}  	
  	return view('location.overview',$data);
  }   
  
  public function getOnlineUser($nasid){
  	$online_user = DB::connection('radius')->table('radpostauth')->where('Nas_Id',$nasid)->orderBy('id','desc')->take(12)->get();
  	if($online_user){
  		foreach ($online_user as $my_user){
  			$user_id = $my_user->user;
  			$data = SocialUser::where('user_id',$user_id)->select(DB::raw("social_user_id, social_network, SUBSTRING_INDEX('full_name', ' ', 1) AS full_name, picture_url"))->first();
  			if($data){
  				$online_user .= "<div class='col-md-3 text-center'>
                                         	<a href='javascript:GetSocialUserDetail(" . $data->social_user_id . ");'>
                                            	<p>
                                                	<img width='50px' src='" . $data->picture_url . "' alt='profile-picture' class='img-circle user-avatar img-responsive img-center'>
                                                </p>
                                                <p>" . $data->full_name . "</p>
                                            </a>
                                        </div>";
  			}else{
  				$online_user = '<div class="col-md-10 col-md-offset-1"> <div class="alert alert-warning" style="margin-top:13px;"><strong><i class="fa fa-frown-o"></i> No users are online now.</strong></div></div>';
  			}
  		}
  	}else{
  		$online_user = '<div class="col-md-10 col-md-offset-1"> <div class="alert alert-warning" style="margin-top:13px;"><strong><i class="fa fa-frown-o"></i> No users are online now.</strong></div></div>';
  	}
  	return $online_user;
  }
  
  public function checkAuthorizedLocation(Request $request){
  	$flag = true;
  	$loca = $request->has('loca')?$request->input('loca'):'';
  	$owner = Session::get('USER_TYPE') == '3'?Session::get('USER_CREATED_BY') : Session::get('USER_ID');  	
  	if(Session::get('USER_TYPE') != '1'){
  		$location = DB::table('location')->where('owner',$owner);
  		if(Session::get('USER_TYPE') == '2'){
  			$location = $location->where('id',$loca);
  		}else{
  			$location = $location->whereIn('id',explode(',', Session::get('LOCATION_IDS')));
  		}
  		$location = $location->count();
  		if($location <= 0){
  			$flag = false;
  		}
  	}
  	
  	return $flag;
  }
  
  public function update(Request $request){
  	$message = "";
  	$location = Location::find($request->input('id_loca'));
  	$identifier = $location->identifier;
  	if($identifier == ''){
  		$current_datetime = strtotime(date('Y-m-d h:i:s'));
  		$identifier = strtotime(substr(md5($current_datetime), 0 ,6));
  		$location->identifier = $identifier;
  	}
  	
  	$location->name = $request->input('name');
  	$location->address = $request->input('formatted_address');
  	$location->state = $request->input('administrative_area_level_1');
  	$location->url = $request->input('url');
  	$location->phone_number = $request->input('international_phone_number');
  	$location->website = $request->input('website');
  	$location->time_zone = $request->input('time_zone');
  	if(!$location->save()){
  		$message = GenerateConfirmationMessage('danger', '<i class="entypo-cancel-circled"></i> Location has not been successfully updated.', true);
  	}else{
  		$message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> Location has been successfully updated.', true);
  	}
  	Session::put('TAB','overview');
  	Session::put('SESSION_MESSAGE',$message);
  	return redirect(url('location/overview?loca='.$location->id));
  }
  
  public function saveschedule(Request $request){
  	$lid = $request->input('lid');
  	if($request->input('hdn_location_schedule_id') != ''){
  		$location_schedule = LocationSchedule::find($request->input('hdn_location_schedule_id'));
  		$location_schedule->update_at = date('Y-m-d H:i:s');
  	}else{
  		$owner = Session::get('USER_TYPE') == '3'? Session::get('USER_CREATED_BY'):Session::get('USER_ID');
  		$location_schedule =  new LocationSchedule;
  		$location_schedule->owner = $owner;
  		$location_schedule->is_default = 0;
  		$location_schedule->location_id = $lid;
  		$location_schedule->create_at = date('Y-m-d H:i:s');  		
  	}
  	$location_schedule->campaign_id = $request->input('ddl_active_campaign');
  	$location_schedule->repeat_type = $request->input('repeat_type');
  	if($request->input('repeat_type') == 'all_day'){
  		$all_day = $request->has('chk_allday')?'all_day':'';
  		$start = $request->input('start_date');
  		$end = $request->input('end_date');
  		if($all_day == ''){
  			$start .=" ".date('H:i',strtotime($request->input('start_time')));
  			$end .=" ".date('H:i',strtotime($request->input('end_time')));
  		}
  		
  		$location_schedule->repeat_data = $all_day;
  		$location_schedule->start_date = $start;
  		$location_schedule->end_date = $end;
  		$location_schedule->repeat_until = '0000-00-00 00:00:00';
  	}
  	
  	$until = $request->input('until') == ''?date('Y-m-d',strtotime('+5 years')):$request->input('until');
  	if($request->input('repeat_type') == 'daily'){
  		$location_schedule->repeat_data = 'daily';
  		$location_schedule->start_date = date('Y-m-d').' '.date('H:i',strtotime($request->input('daily_start_time')));
  		$location_schedule->end_date = $until.' '.date('H:i',strtotime($request->input('daily_end_time')));
  		$location_schedule->repeat_until = $until;
  	}
  	
  	if($request->input('repeat_type') == 'weekly'){
  		if(is_array($request->input('days_of_week'))){
  			$days_of_week = implode(',',$request->input('days_of_week'));
  			$location_schedule->repeat_data = $days_of_week;
  			$location_schedule->repeat_until  =$until;
  		}
  	}
  	
  	if($request->input('repeat_type') == 'monthly'){
  		$location_schedule->repeat_data = $request->input('days_of_month');
  		$location_schedule->repeat_until = $until;
  	}
  	
  	if($location_schedule->save()){
  		$message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> Schedule has been successfully added.', true);
  	}else{
  		$message = GenerateConfirmationMessage('danger', '<i class="entypo-cancel-circled"></i> Schedule has not been successfully added.', true);
  	}
  	
  	Session::put('TAB','campaigns');
  	Session::put('SESSION_MESSAGE',$message);
  	return redirect(url('location/overview?loca='.$lid));
  }
  
  public function addlocationoptions(Request $request){
  	 $max_bandwidth = explode(' ',$request->input('max_bandwidth'));
  	 $session_time_limit = explode(' ',$request->input('session_time_limit'));
  	 $loca = $request->has('loca')?$request->input('loca'):'';
  	 LocationMeta::addLocationMeta($loca, 'max_bandwidth', $max_bandwidth[0]);
  	 LocationMeta::addLocationMeta($loca, 'session_time_limit', $session_time_limit[0]*3600);
  	 $message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> Location option has been successfully updated.', true);
	 Session::set('SESSION_MESSAGE', $message);
	 Session::put('TAB','options');	
	 return redirect(url( "location/overview?loca=$loca" ));
  }
  
  public function assignsubuser(Request $request){
  	$message = '';
  	if(count($request->input('sub_user_list')) > 0){
  		$count = 0;
  		$location_id = $request->input('hdn_location_id');
  		foreach($request->input('sub_user_list') as $id){
  			$result = UserPermission::where('user_id',$id)->where('location_ids','like','%$location_id%')->get();
  			if($request){
  				$user  = UserPermission::where('user_id',$id)->select('module_ids','location_ids')->first();
  				$location_ids  =$location_id;
  				if(strlen($user->location_ids) > 0){
  					$location_ids = $user->location_ids.",".$location_id;
  				}
  				
  			    $module = explode ( ',', $user->module_ids );
				if (! in_array ( 'location', $module )) {
					$module_ids = $user->module_ids . ",location";
				} else {
					$module_ids = $user->module_ids;
				}
				UserPermission::where('user_id',$id)->update(array('module_ids'=>$module_ids,'location_ids'=>$location_ids));
				$count++;
  			}
  		}
  		if($count > 0){
  			$message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> Sub user has been successfully assigned.', true);
  		}else{
  			$message = GenerateConfirmationMessage('danger', '<i class="entypo-cancel-circled"></i> Sub user has not been successfully assigned.', true);
  		}
  		
  		Session::put('SESSION_MESSAGE',$message);
  		Session::put('TAB','users');
  		return redirect(url('location/overview?loca='.$location_id));
  	}
  }
  
  public function removeuserfromlocation(Request $request){
  	$messge = "";
  	$lid = $request->input('lid');
  	$uid = $request->input('uid');
  	$user = UserPermission::where('user_id',$uid)->select('module_ids','location_ids')->first();
  	
  	$locations  =explode(',',$user->location_ids);
  	
  	if(($key = array_search($lid, $locations)) !== false){
  		unset($locations[$key]);
  	}
  	$location_ids = implode(',',$locations);
  	
  	$nums = UserPermission::where('user_id',$uid)->update(array('location_ids'=>$location_ids));
  	if($location_ids == ''){
  		$modules = explode(',',$user->module_ids);
  		if (($key = array_search ( 'location', $modules )) !== false) {
				unset ( $modules [$key] );
		}
		$modules_ids = implode ( ',', $modules );
		$nums = UserPermission::where('user_id',$uid)->update(array('module_ids'=>$modules_ids,'location_ids'=>$location_ids));
  	}
  	
  	if($nums > 0){
  		$message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> Sub user has been successfully removed.', true);
  	}else{
  		$message = GenerateConfirmationMessage('danger', '<i class="entypo-cancel-circled"></i> Sub user has not been successfully removed.', true);
  	}
  	
  	Session::put('TAB','users');
  	Session::put('SESSION_MESSAGE',$message);
  	return redirect(url('location/overview?loca='.$lid));
  }
  
  public function delete(Request $request){
  	  $message = "";
  	  $id  = $request->input('location_id');
  	  $nums = Location::where('id',$id)->update(array('remove'=>1));
  	  if($nums > 0){
  	  	$message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> Location has been successfully removed.', true);
  	  }else{
  	  	$message = GenerateConfirmationMessage('danger', '<i class="entypo-cancel-circled"></i> Location has not been successfully removed.', true);
  	  }
  	  
  	  Session::put('SESSION_MESSAGE',$message);
  	  return redirect(url('location/view'));
  }
  
  public function create(Request $request){
  	 $message  ="";
  	 $location = new Location;
  	 $latlong = $request->has('location')? trim($request->input('location')) :'';
  	 if($latlong == ''){
  	 	$address  =trim(str_replace(' ', '+', $request->input('formatted_address')));
  	 	$region = trim($request->input('country'));
  	 	$latlong = $this->getLatitudeLongitude($address, $region);
  	 }
  	 
  	 $current_datetime = strtotime(date('Y-m-d h:i:s'));
  	 $identifier = strtolower(substr(md5($current_datetime) , 0,6));
  	 $location->name = trim($request->input('name'));
  	 $location->identifier = $identifier;
  	 $location->location = $latlong;
  	 $location->address = trim($request->input('formatted_address'));
  	 $location->country = trim($request->input('country'));
  	 $location->state = trim($request->input('administrative_area_level_1'));
  	 $location->url =  trim($request->input('url'));
  	 $location->phone_number = trim($request->input('international_phone_number'));
  	 $location->website = trim($request->input('website'));
  	 $location->time_zone = $request->input('time_zone');
  	 $location->owner = Session::get ('USER_TYPE') == '3' ? Session::get ( 'USER_CREATED_BY' ) : Session::get ( 'USER_ID' );
  	 if(!$location->save()){
  	 	$message = GenerateConfirmationMessage('danger', '<i class="entypo-cancel-circled"></i> Location has not been successfully added.', true);
  	 }else{
  	 	$message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> Location has been successfully added.', true);
  	 }
  	 
  	 Session::put('message',$message);
  	 return redirect(url('location/view'));
  }
  
  public function getLatitudeLongitude($address,$region){
  	$latlong = "";
		$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region";
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_PROXYPORT, 3128 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		$response = curl_exec ( $ch );
		curl_close ( $ch );
		$location = json_decode ( $response );
		if (isset ( $location->status ) && $location->status == 'OK') {
			$latlong = $location->results [0]->geometry->location->lat . ',' . $long = $location->results [0]->geometry->location->lng;
		}
		return $latlong;
  }
}
