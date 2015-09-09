<?php
namespace App\Http\Controllers;

use App\Http\Requests\Request;

use App\Device;

use App\SubDomain;

use App\User;

use App\Option;

use App\LocationSchedule;

use App\AppCampaignDetails;

use App\Campaign;

use App\DeviceOnlineOfflineStatus;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Session;

use App\Http\Controllers\Controller;
use App\Http\Requests;
require app_path().'/helper/helper.php';

class AjaxController extends Controller
{
	public function __construct(){
		if(!Session::get('SITE_ID') || Session::get('SITE_ID') == ''){
			Session::put('SITE_ID',0);
		}
	}
	
	public function GetDeviceData(Request $request){
		$output = '';		
		
		$mac = $request->has('mac') ? str_replace ( ":", "-", trim ( $request->input('mac')) ) : "";
		$mac = $request->has('mac1') ? str_replace ( ":", "-", trim ( $request->input('mac1')) ) : "";
		$mac = $request->has('mac2') ? str_replace ( ":", "-", trim ( $request->input('mac2')) ) : "";
		$mac = $request->has('mac3') ? str_replace ( ":", "-", trim ( $request->input('mac3')) ) : "";
		$mac = $request->has('mac4') ? str_replace ( ":", "-", trim ( $request->input('mac4')) ) : "";
		
		if ($mac != "" || $mac1 != "" || $mac2 != "" || $mac3 != "" || $mac4 != "") {
			$device_id = $current_nasid = "";
			$WHERE = "WHERE ";
			if ($mac != "") {
				$WHERE .= "d.mac_address = '$mac' OR ";
			}
			if ($mac1 != "") {
				$WHERE .= "d.mac_address = '$mac1' OR ";
			}
			if ($mac2 != "") {
				$WHERE .= "d.mac_address = '$mac2' OR ";
			}
			if ($mac3 != "") {
				$WHERE .= "d.mac_address = '$mac3' OR ";
			}
			if ($mac4 != "") {
				$WHERE .= "d.mac_address = '$mac4' OR ";
			}
			$WHERE = rtrim ( $WHERE, ' OR ' );
			$sql = "SELECT d.id,  (SELECT `identifier` FROM `location` WHERE `id` = d.location_id) AS identifier FROM `device` AS d $WHERE LIMIT 1";
			$query = DB::select(DB::raw($sql));
			if(count($query) > 0){
				$result = $query[0];
				$device_id = $result->id;
				$current_nasid = $result->identifier;
			}
			
			$WHERE = "WHERE ";
			if ($mac != "") {
				$WHERE .= "`mac` = '$mac' OR ";
			}
			if ($mac1 != "") {
				$WHERE .= "`mac1` = '$mac1' OR ";
			}
			if ($mac2 != "") {
				$WHERE .= "`mac2` = '$mac2' OR ";
			}
			if ($mac3 != "") {
				$WHERE .= "`mac3` = '$mac3' OR ";
			}
			if ($mac4 != "") {
				$WHERE .= "`mac4` = '$mac4' OR ";
			}
			$WHERE = rtrim ( $WHERE, ' OR ' );
			$sql = "SELECT `id` FROM `device_status` $WHERE LIMIT 1";
			
			$query = DB::select(DB::raw($sql));
			if(count($query) > 0){
				$result = $query[0];
				$this->updateDeviceStatus ( $result->id, $device_id, $mac, $mac1, $mac2, $mac3, $mac4, $request );
			}else{
				$this->updateDeviceStatus ( "", $device_id, $mac, $mac1, $mac2, $mac3, $mac4, $request );
			}
			
			$this->printSSID ( $request->input('ssid'), $request->input('nasid'), $current_nasid );
		    if ($mac == "E8:DE:27:EA:19:5A" || $mac1 == "E8:DE:27:EA:19:5B" || $mac2 == "E8:DE:27:EA:19:59") {				
				$data  = array('ssid'=>$request->input('ssid'),'current_nasid'=>$current_nasid,'nasid'=>$request->input('nasid'));
				Mail::send('emails.deviceclone',$data,function($message){
					$message->to('naoshad@smartwebsource.com','Naoshad')->subject('Device Cron Notification');
				});
		    }
		    
		    $send_mail = false;
		    $nasid  = $request->input('nasid');
		    $device_id = $device_id == ''?0:$device_id;
		    $sql = "SELECT id, DATE_FORMAT(DATE_SUB( NOW(), INTERVAL 5 MINUTE ), '%Y-%m-%d %H:%i') AS `time`, 
			DATE_FORMAT(last_heartbeat, '%Y-%m-%d %H:%i') AS last_heartbeat, status FROM `device_online_offline_status` 
			WHERE `device_id` = '$device_id' LIMIT 1";
		    
		    $query = DB::select(DB::raw($sql));
		    if(count($query) > 0){
		    	$result = $query[0];
		    	$id = $result->id;
		    	if ((strtotime ( $result->last_heartbeat ) < strtotime ( $result->time )) && $result->status == 'offline') {
					$send_mail = true;
				}
				
				DeviceOnlineOfflineStatus::where('id',$id)->update(array('nasid'=>$nasid,'device_id'=>$device_id,'status'=>'online','last_heartbeat'=>date("Y-m-d H:i:s")));				
		    }else{
		    	$send_mail = true;
		    	$record = new DeviceOnlineOfflineStatus;
		    	$record->nasid = $nasid;
		    	$record->device_id = $device_id;
		    	$record->status = 'online';
		    	$record->last_heartbeat = date("Y-m-d H:i:s");
		    	$record->save();				
		    }
		    
		    if($send_mail == true){
		    	$sql = "SELECT dofs.id, d.mac_address, dofs.last_heartbeat, d.name AS device_name, u.first_name, u.last_name, 
				u.email_address, u.time_zone, @@system_time_zone AS server_timezone FROM 
				`device_online_offline_status` AS dofs 
				LEFT JOIN `location` AS l ON l.identifier = dofs.nasid 
				LEFT JOIN `device` AS d ON d.id = dofs.device_id 
				LEFT JOIN `user` AS u ON u.id = d.owner
				WHERE dofs.device_id = $device_id LIMIT 1";
		    	$query = DB::select(DB::raw($sql));
		    	if(count($query) > 0){
		    		$result = $query[0];
		    		$data  =array('mac_address'=>$result->mac_address,'device_name'=>$result->device_name,'email_address'=>$result->email_address,'last_hearbeat'=>$result->last_heartbeat);
		    		Mail::send('emails.noreply',$data,function($message){
		    			$message->to('mizanur.rahman@smartwebsource.com','Mizanur Rahman')->subject('Device Online Notification');
		    		});
		    	}
		    }		    
		    
		}	
		
	}
	
	public function Getlandingpageinfo(Request $request){
		if($request->has('location_id') && ! empty($request->input('location_id'))){
			$location_id = $request->input('location_id');
			$campaign_id = $this->GetCampaignID($location_id);
			
			if($campaign_id > 0){
				$campaign = Campaign::where('id',$campaign_id)->first();
				
				$app_campaign_details  = AppCampaignDetails::where('campaign_id',$campaign_id)->select('app_info_id','app_type')->get();
				$social_app_ids = "";
				if(count($app_campaign_details) > 0){
					foreach($app_campaign_details as $social_app){
						$appType=  $social_app->app_type;
						if($appType == 1 || $appType == 2 || $appType == 3 || $appType == 4){
							$social_app_ids.= $social_app->app_info_id.',';
						}
					} 
					$social_app_ids = rtrim($social_app_ids,',');
				}else{
					$social_app_ids = 0;					
				}
				
				$sql = "SELECT IFNULL(at.app_type, 'custom_email') AS app_type, ai.app_id, ai.app_secrect FROM app_info AS ai INNER JOIN app_type AS at ON at.id = ai.type WHERE ai.id IN(" . $social_app_ids . ")";
				$app_info = DB::select(DB::raw($sql));
				$landing_page_info = array();
				
				if($campaign){
					$my_campaign = array (
							'campaign_name' => $campaign->name,
							'text_color' => $campaign->text_color,
							'background_color' => $campaign->background_color,
							'background_image' => $campaign->background_image,
							'header_html' => $campaign->header_html,
							'footer_html' => $campaign->footer_html,
							'custom_terms' => $campaign->custom_term,
							'custom_form_fields' => $campaign->fields_email 
					);
					
					$landing_page_info ['campaign_info'] = $my_campaign;
				}
				
				if(count($app_info)> 0){
					$landing_page_info ['social_info'] = array ();
					foreach ( $app_info as $my_app ) {
						$app_array ['app_id'] = $my_app->app_id;
						$app_array ['app_secrect'] = $my_app->app_secrect;
						$landing_page_info ['social_info'] [strtolower ( $my_app->app_type )] = $app_array;
					}
				}
				return json_encode($landing_page_info);				
			}else{
				return "No Campaign Found";
			}
		}else{
			return "Invalid request!";
		}
	}
	
	public function GetCampaignID($location_id){
		$campaign_id = 0;
		if($campaign_id == 0){
			$default_campaign = LocationSchedule::where('location_id',$location_id)->where('is_default','>',0)->first();
			if($default_campaign){
				$campaign_id = $default_campaign->campaign_id;
			}
		}
		return $campaign_id;
	}
	
	public function Createuser(Request $request){
		if($request->has('identifier')){
			$identifier = Option::getOption('app_secret_key','0');
			if($identifier == strtoupper($request->input('identifier'))){
				if($request->has('email')){
					$email = $request->input('email');
					if($email != ''){
						if(filter_var($email,FILTER_VALIDATE_EMAIL)){
							$record = User::where('email_address',$email)->first();
							if(count($record) <  1){
								$first_name = $last_name = "";
								if($request->has('first_name') && $request->input('first_name') != ''){
									$first_name = $request->input('first_name');
								}
								
								if($request->has('last_name') && $request->input('last_name') != ''){
									$last_name = $request->input('last_name');
								}
								
								if($first_name != ''){
									$subdomain_name = strtolower(str_replace(' ', '', $first_name));
									$status  = CheckSubDomain($subdomain_name);
									if($status == false){
										$subdomain_name = strtolower(str_replace(' ', '', $first_name));
									}
								}else{
									$subdomain_name = date('ymdhms');
								}
								
								$record = new SubDomain;
								$record->title = $subdomain_name;
								$record->status = 1;
								$record->save();
								$ret = SubDomain::where('title',$subdomain_name)->where('status',1)->first();
								$site_id  = $ret->id;
								
								preg_match ( '/[^.]+\.[^.]+$/', $request->server('HTTP_HOST'), $matches );
								$host = $matches [0];
								$subdomain = $subdomain_name . '.' . $host;
								
								$max_bandwidth = Option::getOption('max_bandwidth',0);
								$session_time_limit = Option::getOption('session_time_limit',0);
								$stripe_settings = Option::getOption('stripe_settings',0);
								
								Option::addOption('max_bandwidth', $max_bandwidth,$site_id);
								Option::addOption('session_time_limit', $session_time_limit,$site_id);
								Option::addOption('stripe_settings',$stripe_settings,$site_id);
								
								$token = User::guid();
								$password = generate_strong_password();
								$user = new User;
								
								$user->site_id = $site_id;
								$user->email_address  = $email;
								$user->password = md5($password);
								$user->is_active = false;
								$user->token = $token;
								$user->first_name = $first_name;
								$user->last_name = $last_name;
								$user->full_name = $first_name.' '.$last_name;
								$user->user_type_id = 2;
								$user->created_at = date('Y-m-d H:i:s',time());
								$user->modified =  date ( "Y-m-d H:i:s", time ());
								$user->is_admin = 0;
								
								if(!$user->save()){
									return "Can not save.";
								}else{
									$encoded_data = urlencode(base64_encode($user->id));									
									$data = array('first_name'=>$first_name,'subdomain'=>$subdomain,'encoded_data'=>$encoded_data,'email'=>$email,'password'=>$password);
									Mail::send('emails.newaccount',$data,function($message){
										$message->to($email,'')->subject('New Account Confirmation');
									});
									
									set_user_permission($user->id);
									create_stripe_subscriber($user->id, $email);
									
									return "Thank you. Please check your email to complete your registration.";
								}
							}else{
								return "Invalid Email";
							}
						}else{
							return "Email is required.";
						}
					}else{
						return "Invalid Api Call";
					}
				}else{
					return "Invalid Identifier.";
				}
			}else{
				return "Identifier is required.";
			}
		}
	}
	
	public function Manageusers(Request $request){
		$output = "";
		if($request->has('identifier')){
			$identifier = Option::getOption('app_secret_key','0');
			if($identifier == strtoupper($request->input('identifier'))){
				$flag = true;
				if($request->has('email') && $request->has('status')){
					if($request->input('email') == ''){
						$flag = false;
						$output.= 'Email is required.<br>';
					}else{
						if(!filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)){
							$flag = false;
							$output.= "Invalid Email.<br>";
						}
					}
					
					if($request->input('status') == ''){
						$flag = false;
						$output.= "Status is required.<br>";
					}else{
						if($request->input('status') == 'enable' || $request->input('status') == 'disable'){
							$flag = true;
						}else{
							$flag = false;
							$output.= "Invaild status.<br>";
						}
					}
					
					if($flag == true){
						$email = $request->input('email');
						$user = User::where('email_address',$email)->first();
						if($user){
							if($user->user_type_id != 1){
								$status  = $request->input('status') ? 1 : 0;
								$nums = User::where('email_address',$email)->update(array('is_active'=>1,'modified'=>date('Y-m-d h:i:s')));
								if($nums > 0){
									$output.= "Account has been successfully ".strtoupper($request->input('status'))."D.";
								}else{
									$output.= "Operation has been terminated, try again later";
								}
							}else{
								$output.= "No user found for given email";
							}
						}
					}else{
						$output.= "Invalid API call.";
					}
				}else{
					$output.= "Invalid Identifier";
				}
			}else{
				$output.= "Identifier is required";
			}
		}
		return $output;
	}
	
	public function executeGetnasid(Request $request){
	    if($request->has('mac') && $request->input('mac') != ''){
	    	$mac = $request->input('mac');
	    	$sql = "SELECT l.identifier FROM device AS d LEFT JOIN location AS l ON d.location_id = l.id WHERE d.mac_address = '$mac' LIMIT 1";
	    	$query = DB::select(DB::raw($sql));
	    	if(count($query) > 0){
	    		$data = $query[0];
	    		return $data->identifier;
	    	}else{
	    		return '';
	    	}
	    }
	}
	
	public function Processstripenotification(){
		processstripenotification();
	}
	
	public function Processrecurlynotification(){
		processrecurlynotification();
	}
	
	public function GetFacebookLikeBox(){
		if(Session::get('USER_ID') || Session::get('USER_ID') == ''){
			return 'You are not authorized';
		}else{
			$page_name = Request::input('fb_page_name');
			return "<div id='fb-root'></div>
					<script>(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = '//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3';
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));</script>"
			.'<div class="fb-page" data-href="https://www.facebook.com/' . $page_name . '" data-height="250" data-hide-cover="false" data-show-facepile="true" data-show-posts="false"></div>';
		}
	}
	
	public function updateDeviceStatus($id = "", $device_id = "", $mac = "", $mac1 = "", $mac2 = "", $mac3 = "", $mac4 = "", $request_data){
		$device_id = $device_id == ''?0:$device_id;
		
		$data = json_encode($request_data);
		$d = array('device_id'=>$device_id,'mac'=>$mac,'mac1'=>$mac1,'mac2'=>$mac2,'mac3'=>$mac3,'mac4'=>$mac4,'device_status_details'=>$data);
		if($id != '' && $id > 0){			
			Device::where('id',$id)->update($d);
		}else{
			Device::insert($d);
		}
	}
	
	public function printSSID($requested_ssid,$requested_nasid,$requested_nasid){
	    $current_ssid = "Free Wi-Fi";
		if ($current_nasid != "") {
			$current_ssid = LocationSchedule::getActiveCampaignId($current_nasid, 'ssid' );
		}
		
		$output = "";
		$ssid_changed = false;
		if ($requested_ssid != $current_ssid) {
			$output .= $current_ssid . ",";
			$ssid_changed = true;
		} else {
			$output .= ",";
		}
		$nasid_changed = false;
		if ($requested_nasid != $current_nasid) {
			$output .= $current_nasid;
			$nasid_changed = true;
		}
		if ($ssid_changed == false && $nasid_changed == false) {
			return "";
		} else {
			return $output;
		}
	}
	
	public function DeviceDetail(){
		$sql = "SELECT * FROM `device_status` WHERE `device_id` = 0 ORDER BY `status_created_on` DESC";
		$query = DB::select(DB::raw($sql));
		$table = "<table border='1' width='100%'><thead><tr><td>SL</td><td>MAC</td><td>MAC1</td><td>MAC2</td><td>MAC3</td><td>MAC4</td><td>NASID</td><td>OWNER'S DEVICE</td><td>OWNER'S EMAIL</td><td>LAST CONNECTED</td></tr></thead>";
		if(count($query) > 0){
			$counter = 1;
			foreach($query as $row){
				$nasid = "UNKNOWN";
				$device_status_details = $row->device_statud_details;
				if($device_status_details){
					$data = json_decode($device_status_details);
					$nasid = $data->nasid;
				}
				$sql1 = "SELECT u.email_address, GROUP_CONCAT(d.mac_address) as device_mac FROM user AS u INNER JOIN location AS l ON l.owner = u.id INNER JOIN device as d on d.location_id = l.id WHERE l.identifier = '$nasid' LIMIT 1";
				$query1 = DB::select(DB::raw($sql1));
				$owner_email ='';
				$device_mac = '';
				if(count($query1) > 0){
					$result1 = $query1[0];
					$owner_email = $result1->email_address ? $result1->email_address :'';
					$device_mac = $result1->device_mac ? $result1->device_mac :'';
				}				
				
				$table .= "<tr>";
				$table .= "<td>$counter</td>";
				$table .= "<td>" . $row->mac . "</td>";
				$table .= "<td>" . $row->mac1 . "</td>";
				$table .= "<td>" . $row->mac2 . "</td>";
				$table .= "<td>" . $row->mac3 . "</td>";
				$table .= "<td>" . $row->mac4 . "</td>";
				$table .= "<td>$nasid</td>";
				$table .= "<td>$device_mac</td>";
				$table .= "<td>$owner_email</td>";
				$table .= "<td>" . $row->status_created_on . "</td>";
				$table .= "</tr>";
				
				$counter ++;
			}
		}else{
			$table.= "<tr><td colspan='8'>Nothing found!!</td></tr>";
		}
		
		$table .="</table>";
		
		return $table;
	}
	
	public function returnSSID($requested_ssid,$requsted_nasid,$current_nasid){
	    $current_ssid = "Free Wi-Fi";
		if ($current_nasid != "") {
			$current_ssid = LocationSchedule::getActiveCampaignId( $current_nasid, 'ssid' );
		}
		
		$output = "";
		$ssid_changed = false;
		if ($requested_ssid != $current_ssid) {
			$output .= $current_ssid . ",";
			$ssid_changed = true;
		} else {
			$output .= ",";
		}
		$nasid_changed = false;
		if ($requested_nasid != $current_nasid) {
			$output .= $current_nasid;
			$nasid_changed = true;
		}
		if ($ssid_changed == false && $nasid_changed == false) {
			return "";
		} else {
			return $output;
		}
	}
	
	public function getNasIdSSID(){
		$output = '';
		$mac = Request::has('mac') ? str_replace(":", "-", trim(Request::input('mac'))) :"";
		$mac1 = Request::has('mac1') ? str_replace(":", "-", trim(Request::input('mac1'))) :"";
		$mac2 = Request::has('mac2') ? str_replace(":", "-", trim(Request::input('mac2'))) :"";
		$mac3 = Request::has('mac3') ? str_replace(":", "-", trim(Request::input('mac3'))) :"";
		$mac4 = Request::has('mac4') ? str_replace(":", "-", trim(Request::input('mac4'))) :"";
		
		if ($mac != "" || $mac1 != "" || $mac2 != "" || $mac3 != "" || $mac4 != "") {
			$device_id = $current_nasid = "";
			$WHERE = "WHERE ";
			if ($mac != "") {
				$WHERE .= "d.mac_address = '$mac' OR ";
			}
			if ($mac1 != "") {
				$WHERE .= "d.mac_address = '$mac1' OR ";
			}
			if ($mac2 != "") {
				$WHERE .= "d.mac_address = '$mac2' OR ";
			}
			if ($mac3 != "") {
				$WHERE .= "d.mac_address = '$mac3' OR ";
			}
			if ($mac4 != "") {
				$WHERE .= "d.mac_address = '$mac4' OR ";
			}
			$WHERE = rtrim ( $WHERE, ' OR ' );
			$sql = "SELECT d.id,  (SELECT `identifier` FROM `location` WHERE `id` = d.location_id) AS identifier FROM `device` AS d $WHERE LIMIT 1";
			
			$query = DB::select(DB::raw($sql));
			if(count($query) > 0){
				$result = $query[0];
				$device_id = $result->id;
				$current_nasid = $result->identifier;
			}
			
			$WHERE = "WHERE ";
			if ($mac != "") {
				$WHERE .= "`mac` = '$mac' OR ";
			}
			if ($mac1 != "") {
				$WHERE .= "`mac1` = '$mac1' OR ";
			}
			if ($mac2 != "") {
				$WHERE .= "`mac2` = '$mac2' OR ";
			}
			if ($mac3 != "") {
				$WHERE .= "`mac3` = '$mac3' OR ";
			}
			if ($mac4 != "") {
				$WHERE .= "`mac4` = '$mac4' OR ";
			}
			$WHERE = rtrim ( $WHERE, ' OR ' );
			$sql = "SELECT `id` FROM `device_status` $WHERE LIMIT 1";
			
			$query = DB::select(DB::raw($sql));
			if(count($query) > 0){
				$result = $query[0];
				$this->updateDeviceStatus($result->id,$device_id,$mac,$mac1,$mac2,$mac3,$mac4,Request);				
			}else{
				$this->updateDeviceStatus ( "", $device_id, $mac, $mac1, $mac2, $mac3, $mac4, Request );
			}
			
			$output = $this->returnSSID(Request::input('ssid'), Request::input('nasid'), $current_nasid);
			
			$send_mail = false;
			$nasid = Request::input('nasid');
			$device_id = $device_id == ''? 0 :$device_id;
			$sql = "SELECT id, DATE_FORMAT(DATE_SUB( NOW(), INTERVAL 5 MINUTE ), '%Y-%m-%d %H:%i') AS `time`,
			DATE_FORMAT(last_heartbeat, '%Y-%m-%d %H:%i') AS last_heartbeat, status FROM `device_online_offline_status`
			WHERE `device_id` = '$device_id' LIMIT 1";
			
			$query = DB::select(DB::raw($sql));
			
			if(count($query) > 0){
				$result = $query[0];
				$id = $result->id;
			    if ((strtotime ( $result->last_heartbeat ) < strtotime ( $result->time )) && $result->status == 'offline') {
					$send_mail = true;
				}
				
				DeviceOnlineOfflineStatus::where('id',$id)->update(array('nasid'=>$nasid,'device_id'=>$device_id,'status'=>'online','last_heartbeat'=>date('Y-m-d h:i:s')));
			}else{
				$send_mail = true;
				$record = new DeviceOnlineOfflineStatus();
				$record->nasid = $nasid;
				$record->device_id = $device_id;
				$record->status = $status;
				$record->last_heartbeat = date('Y-m-d h:i:s');
			}
			
			if($send_mail){
				$sql = "SELECT dofs.id, d.mac_address, dofs.last_heartbeat, d.name AS device_name, u.first_name, u.last_name,
				u.email_address, u.time_zone, @@system_time_zone AS server_timezone FROM
				`device_online_offline_status` AS dofs
				LEFT JOIN `location` AS l ON l.identifier = dofs.nasid
				LEFT JOIN `device` AS d ON d.id = dofs.device_id
				LEFT JOIN `user` AS u ON u.id = d.owner
				WHERE dofs.device_id = $device_id LIMIT 1";
				$query = DB::select(DB::raw( $sql));
				
				if(count($query) > 0){
					$result = $query[0];
					$data = array('mac_address'=>$result->mac_address,'device_name'=>$result->device_name,'email_address'=>$result->email_address,'last_hearbeat'=>$result->last_hearbeat);
					Mail::send('emails.deviceinfo',$data,function($message){
						$message->to('mizanur.rahman@smartwebsource.com','Mizanur Rahman')->subject('MyWiFi');
					});
				}
			}
			
			return $output;
		}
	}
	
	public function DeviceConfig(){	
		ini_set ( 'display_errors', 0 );		
		date_default_timezone_set ( 'GMT' );
		$req_details = "";
		$postData = array ();
		$input = Request::all();
		foreach ( $input as $key => $value ) {			
			$req_details .= "Key: " . $key . " || Value: " . $value;
			$postData [$key] = $value;
		}
		$req_details .= " Request URI " . Request::server('REQUEST_URI') . " direct_ssid " . Request::input("ssid");
		
		/*
		 * write the log to db
		 */
		
		$con = DB::connection('ddwrtinfo');
		if (!$con) {
			die ( '# Not connected : ');
		}
		$ret_val = $con->table('temp')->insert(array('info_details'=>$req_details));
		
		if ($ret_val <= 0) {
			die ( '# Could not enter data: ');
		}
		
		// / Cron corrupt , so reset ddwrt
		if ((Request::input('ssid') == '') || (Request::input('lan') == '') || (Request::input('nasid') == '')) 
		
		{
			
			$result = '#!/bin/sh' . PHP_EOL;
			$result .= 'PATH=/bin:/sbin:/usr/bin:/usr/sbin; ' . PHP_EOL;
			$result .= "\n";
			$result .= 'export PATH; ' . PHP_EOL;
			$result .= "\n";
			$result .= 'mtd -r erase nvram ' . PHP_EOL;
			$result .= "\n";
			return $result;			
		}
		
		/*
		 * getting nasid and ssid
		 */
		$output = $this->getNasIdSSID ();
		
		/* cho "# OUTPUT: ". $output; */
		if ($output != '') {
			$resultstr = explode ( ',', $output );
			$ssid = trim ( $resultstr [0] );
			$nasid = trim ( $resultstr [1] );
			
			if ($_GET ['pc'] == 'yes') {
				$result = '#!/bin/sh' . PHP_EOL;
				$result .= 'PATH=/bin:/sbin:/usr/bin:/usr/sbin; ' . PHP_EOL;
				$result .= "\n";
				$result .= 'export PATH; ' . PHP_EOL;
				$result .= "\n";
				$result .= 'export nasid=' . $nasid . PHP_EOL;
				$result .= "\n";
				$result .= 'echo "' . $nasid . '" > /root/chilli' . PHP_EOL;
				$result .= "\n";
				$result .= 'echo "' . $ssid . '" > /root/ssid' . PHP_EOL;
				$result .= "\n";
				$result .= 'sed -i "s/export nasid=\([a-zA-Z0-9_]*\)/export nasid=' . $nasid . '/g" /root/.bashrc' . PHP_EOL;
				$result .= "\n";
				$result .= 'sed -i "s/radiusnasid \([a-zAZ0-9_]*\)/radiusnasid ' . $nasid . '/g" /etc/chilli.conf' . PHP_EOL;
				$result .= "\n";
				$result .= 'service hotspot restart';
				return $result;			
			}
			
			$result = '#!/bin/sh' . PHP_EOL;
			$result .= 'PATH=/bin:/sbin:/usr/bin:/usr/sbin; ' . PHP_EOL;
			$result .= "\n";
			$result .= 'export PATH; ' . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_uamallowed='mywifi.io,www.facebook.com,fbstatic-a.akamaihd.net,connect.facebook.net,fbcdn-profile-a.akamaihd.net,fbexternal-a.akamaihd.net,licdn.com,www.linkedin.com,linkedin.com,googleapis.com,google.com,static.linkedin.com,gstatic.com,api.linkedin.com,static.licdn.com,licdn.com,8.8.8.8,69.16.208.210,www.gstatic.com,facebook.com,ajax.googleapis.com,fonts.googleapis.com,graph.facebook.com,connect.facebook.net,m.facebook.com,mobileupsell.net,twitter.com,www.twitter.com,199.16.156.0/22,199.59.148.0/22,199.96.56.0/21,192.133.76.0/22,199.16.156.0/22,199.59.148.0/22,199.96.56.0/21,192.133.76.0/22,216.239.32.0/19,64.233.160.0/19,66.249.64.0/19,72.14.192.0/18,209.85.128.0/17,66.102.0.0/20,74.125.0.0/16,64.18.0.0/20,207.126.144.0/20,173.194.0.0/16,5.178.40.0/20,195.27.154.0/24,80.150.192.0/24,77.67.97.0/22,212.119.27.0/25,2.16.219.0/13,66.171.231.0/24,31.13.24.0/21,31.13.64.0/18,212.245.45.0/24,213.254.17.0/24,46.33.70.0/24,instagram.com,instagramstatic-a.akamaihd.ne,googleadservices.com,doubleclick.net,adroll.com,perfectaudience.com,gstatic.com,wistia.com,vimeo.com,clickfunnels.com,fonts.gstatic.com,accounts.google.com,api.instagram.com,172.31.47.120'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_uamdomain='.mywifi.io .facebook.com .facebook.net .akamaihd.net .fbcdn.net .m.facebook.com .twitter.com .abs.twimg.com .api.twitter.com .linkedin.com .licdn.com .api.linkedin.com .fbstatic-a.akamaihd.net .googleapis.com .connect.facebook.net .instagram.com .googleadservices.com .doubleclick.net .adroll.com .perfectaudience.com .gstatic.com .wistia.com .vimeo.com .clickfunnels.com'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_radius='radius1.mywifi.io'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_pass='mywifi'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_uamsecret='mywifi'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_url='https://www.mywifi.io/index.php?m=landingpage&a=viewlandingpage'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_interface='br0'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set rc_firewall='/usr/sbin/iptables -t mangle -A POSTROUTING -p tcp --tcp-flags SYN,RST SYN -m tcpmss --mss 1421:65535 -j TCPMSS --clamp-mss-to-pmtu; /usr/sbin/iptables -I INPUT -p udp -m udp --dport 3779 -j ACCEPT ; /usr/sbin/iptables -I POSTROUTING -t nat -o vlan2 -j MASQUERADE; /usr/sbin/iptables  -t filter -I FORWARD 1 -p tcp --dport 53 -j ACCEPT; /usr/sbin/iptables  -t filter -I FORWARD 1 -p udp --dport 53 -j ACCEPT; /usr/sbin/iptables -I INPUT -p tcp --dport 53 -j ACCEPT; /usr/sbin/iptables -I INPUT -p udp --dport 53 -j ACCEPT;/usr/sbin/iptables -t nat -A PREROUTING -p udp --dport 53 -j DNAT --to 8.8.8.8;/usr/sbin/iptables -t nat -A PREROUTING -p tcp --dport 53 -j DNAT --to 8.8.8.8;/usr/sbin/iptables  -t nat -I PREROUTING -i tun0 -d 192.168.0.0/16 -j DROP;/usr/sbin/iptables -t nat -I PREROUTING -i tun0 -d 169.254.0.0/16 -j DROP;/usr/sbin/iptables -t nat -I PREROUTING -i tun0 -d 172.16.0.0/12 -j DROP;/usr/sbin/iptables -t nat -I PREROUTING -i tun0 -d 10.0.0.0/8 -j DROP;/usr/sbin/iptables -t nat -I PREROUTING -i tun0 -d `nvram get lan_ipaddr`/`nvram get lan_netmask` -j DROP;/usr/sbin/iptables -t nat -I PREROUTING -i tun0 -d `nvram get wan_gateway`/32 -j DROP;/usr/sbin/iptables -t nat -I PREROUTING -i tun0 -d `nvram get chilli_net` -j ACCEPT'" . PHP_EOL;
			$result .= "\n";
			$result .= <<<EOD
nvram set rc_startup="/bin/sh -c 'echo \"/usr/bin/wget \"\"https://www.mywifi.io/index.php?m=api&a=deviceconfig&mac1=`ifconfig eth0| awk '/HWaddr/ {print $5}'`\&mac2=`ifconfig eth1| awk '/HWaddr/ {print $5}'`\&mac3=`ifconfig br0| awk '/HWaddr/ {print $5}'`\&mac=\`nvram get ath0_hwaddr|sed s/:/-/g\`\&mac4=\`nvram get ath1_hwaddr|sed s/:/-/g\`\&nasid=\`nvram get chilli_radiusnasid\`\&os_date=\`nvram get os_date|sed s/\" \"/-/g\`\&wan=\\\\\`\`\`nvram get wan_ipaddr\\\\\`\`\`\&lan=\\\\\`\`\`nvram get chilli_net\\\\\`\`\`\&ssid=\\\\\`\`\`nvram get wl_ssid|sed -e \\\\\"s/ /%20/g\\\\\"\\\\\`\`\`\&uptime=\\\\\`\`\`uptime|sed s/\\\\\" \\\\\"/\"\\\%20\"/g|sed s/:/\"\\\%3A\"/g|sed s/,/\"\\\%2C\"/g\\\\\`\`\`\"\" -O /tmp/remote.sh\" ' > /tmp/up ;chmod 755 /tmp/up; /bin/sh -c 'echo \"*/30 * * * * root /tmp/up \" '  > /tmp/cron.d/up;stopservice cron && startservice cron; /usr/bin/killall chilli;/bin/sleep 2;/usr/sbin/chilli -c /tmp/chilli.conf "
nvram set chilli_radius='radius1.mywifi.io'
EOD;
			
			$result .= "\n";
			$result .= PHP_EOL . 'nvram commit ' . PHP_EOL;
			
			if ($ssid != '') {
				$result .= 'nvram set wl0_ssid=\'' . $ssid . '\' ' . PHP_EOL;
				$result .= "\n";
				$result .= 'nvram set wl_ssid=\'' . $ssid . '\' ' . PHP_EOL;
				$result .= "\n";
				if ($_GET ['c7'] == 'yes') {
					$result .= 'nvram set ath0_ssid=\'' . $ssid . '\' ' . PHP_EOL;
				}
				$result .= "\n";
				$result .= 'nvram commit ' . PHP_EOL;
				// $result .= 'stopservice lan ' .PHP_EOL;
				// $result .= 'startservice lan '.PHP_EOL;
			}
			
			if ($nasid != '') {
				$result .= "\n";
				$result .= 'nvram set chilli_radiusnasid=\'' . $nasid . '\' ' . PHP_EOL;
				$result .= "\n";
				$result .= 'nvram commit ' . PHP_EOL;
				// $result .= 'reboot';
			}
			
			if (($ssid != '') || ($nasid != '')) {
				$result .= 'reboot';
			}
			
			return $result;
		} else {
			if (Request::input('pc') == 'yes') {
				return;
			}
			$result = '#!/bin/sh' . PHP_EOL;
			$result .= 'PATH=/bin:/sbin:/usr/bin:/usr/sbin; ' . PHP_EOL;
			$result .= "\n";
			$result .= 'export PATH; ' . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_uamallowed='mywifi.io,www.facebook.com,fbstatic-a.akamaihd.net,connect.facebook.net,fbcdn-profile-a.akamaihd.net,fbexternal-a.akamaihd.net,licdn.com,www.linkedin.com,linkedin.com,googleapis.com,google.com,static.linkedin.com,gstatic.com,api.linkedin.com,static.licdn.com,licdn.com,8.8.8.8,69.16.208.210,www.gstatic.com,facebook.com,ajax.googleapis.com,fonts.googleapis.com,graph.facebook.com,connect.facebook.net,m.facebook.com,mobileupsell.net,twitter.com,www.twitter.com,199.16.156.0/22,199.59.148.0/22,199.96.56.0/21,192.133.76.0/22,199.16.156.0/22,199.59.148.0/22,199.96.56.0/21,192.133.76.0/22,216.239.32.0/19,64.233.160.0/19,66.249.64.0/19,72.14.192.0/18,209.85.128.0/17,66.102.0.0/20,74.125.0.0/16,64.18.0.0/20,207.126.144.0/20,173.194.0.0/16,5.178.40.0/20,195.27.154.0/24,80.150.192.0/24,77.67.97.0/22,212.119.27.0/25,2.16.219.0/13,66.171.231.0/24,31.13.24.0/21,31.13.64.0/18,212.245.45.0/24,213.254.17.0/24,46.33.70.0/24,instagram.com,instagramstatic-a.akamaihd.ne,googleadservices.com,doubleclick.net,adroll.com,perfectaudience.com,gstatic.com,wistia.com,vimeo.com,clickfunnels.com,fonts.gstatic.com,accounts.google.com,api.instagram.com,172.31.47.120'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_uamdomain='.mywifi.io .facebook.com .facebook.net .akamaihd.net .fbcdn.net .m.facebook.com .twitter.com .abs.twimg.com .api.twitter.com .linkedin.com .licdn.com .api.linkedin.com .fbstatic-a.akamaihd.net .googleapis.com .connect.facebook.net .instagram.com .googleadservices.com .doubleclick.net .adroll.com .perfectaudience.com .gstatic.com .wistia.com .vimeo.com .clickfunnels.com'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_radius='radius1.mywifi.io'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_pass='mywifi'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_uamsecret='mywifi'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_url='https://www.mywifi.io/index.php?m=landingpage&a=viewlandingpage'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set chilli_interface='br0'" . PHP_EOL;
			$result .= "\n";
			$result .= "nvram set rc_firewall='/usr/sbin/iptables -t mangle -A POSTROUTING -p tcp --tcp-flags SYN,RST SYN -m tcpmss --mss 1421:65535 -j TCPMSS --clamp-mss-to-pmtu; /usr/sbin/iptables -I INPUT -p udp -m udp --dport 3779 -j ACCEPT ; /usr/sbin/iptables -I POSTROUTING -t nat -o vlan2 -j MASQUERADE; /usr/sbin/iptables  -t filter -I FORWARD 1 -p tcp --dport 53 -j ACCEPT; /usr/sbin/iptables  -t filter -I FORWARD 1 -p udp --dport 53 -j ACCEPT; /usr/sbin/iptables -I INPUT -p tcp --dport 53 -j ACCEPT; /usr/sbin/iptables -I INPUT -p udp --dport 53 -j ACCEPT;/usr/sbin/iptables -t nat -A PREROUTING -p udp --dport 53 -j DNAT --to 8.8.8.8;/usr/sbin/iptables -t nat -A PREROUTING -p tcp --dport 53 -j DNAT --to 8.8.8.8;/usr/sbin/iptables  -t nat -I PREROUTING -i tun0 -d 192.168.0.0/16 -j DROP;/usr/sbin/iptables -t nat -I PREROUTING -i tun0 -d 169.254.0.0/16 -j DROP;/usr/sbin/iptables -t nat -I PREROUTING -i tun0 -d 172.16.0.0/12 -j DROP;/usr/sbin/iptables -t nat -I PREROUTING -i tun0 -d 10.0.0.0/8 -j DROP;/usr/sbin/iptables -t nat -I PREROUTING -i tun0 -d `nvram get lan_ipaddr`/`nvram get lan_netmask` -j DROP;/usr/sbin/iptables -t nat -I PREROUTING -i tun0 -d `nvram get wan_gateway`/32 -j DROP;/usr/sbin/iptables -t nat -I PREROUTING -i tun0 -d `nvram get chilli_net` -j ACCEPT'" . PHP_EOL;
			$result .= "\n";
			$result .= <<<EOD
nvram set rc_startup="/bin/sh -c 'echo \"/usr/bin/wget \"\"https://www.mywifi.io/index.php?m=api&a=deviceconfig&mac1=`ifconfig eth0| awk '/HWaddr/ {print $5}'`\&mac2=`ifconfig eth1| awk '/HWaddr/ {print $5}'`\&mac3=`ifconfig br0| awk '/HWaddr/ {print $5}'`\&mac=\`nvram get ath0_hwaddr|sed s/:/-/g\`\&mac4=\`nvram get ath1_hwaddr|sed s/:/-/g\`\&nasid=\`nvram get chilli_radiusnasid\`\&os_date=\`nvram get os_date|sed s/\" \"/-/g\`\&wan=\\\\\`\`\`nvram get wan_ipaddr\\\\\`\`\`\&lan=\\\\\`\`\`nvram get chilli_net\\\\\`\`\`\&ssid=\\\\\`\`\`nvram get wl_ssid|sed -e \\\\\"s/ /%20/g\\\\\"\\\\\`\`\`\&uptime=\\\\\`\`\`uptime|sed s/\\\\\" \\\\\"/\"\\\%20\"/g|sed s/:/\"\\\%3A\"/g|sed s/,/\"\\\%2C\"/g\\\\\`\`\`\"\" -O /tmp/remote.sh\" ' > /tmp/up ;chmod 755 /tmp/up; /bin/sh -c 'echo \"*/30 * * * * root /tmp/up \" '  > /tmp/cron.d/up;stopservice cron && startservice cron; /usr/bin/killall chilli;/bin/sleep 2;/usr/sbin/chilli -c /tmp/chilli.conf "
EOD;
			$result .= "\n";
			$result .= PHP_EOL . 'nvram commit ' . PHP_EOL;
			
			return $result;
		}
	}	
}




























