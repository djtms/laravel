<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

use App\Option;
use DB;
use App\SocialUserDeviceInfo;
use App\Device;
use App\SocialUser;

use App\CampaignMeta;

use App\Campaign;

use App\LocationSchedule;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
require_once app_path().'/helper/helper.php';

class LandingpageController extends Controller
{
    public function __construct(){
    	view()->share('controller','landingpage');
    	$this->middleware('auth');
    	$this->middleware('boot');
    }
    
    public function viewlandingpage(Request $request){
    	view()->share('action','landingpage');
    	$data = array();
    	if($request->has('nasid') && $request->input('nasid') != ""){
    		$data['location_id'] =0;
    		
    		$device_status = false;
    		$device_mac = $request->has('called')?trim($request->input('called')):'';
    		
    		if($device_mac){
    			$sql = "SELECT d.id, d.location_id FROM `device` AS d 
				INNER JOIN `device_status` AS ds ON d.id = ds.device_id 
				WHERE (ds.mac = '$device_mac' OR ds.mac1 = '$device_mac' OR ds.mac2 = '$device_mac' OR ds.mac3 = '$device_mac' OR ds.mac4 = '$device_mac') AND d.status = 1 LIMIT 1";
    			$query = DB::select(DB::raw($sql));
    			if($query){
    				$result = $query[0];
    				$data['location_id'] = $result->location_id;
    				$device_status = true;
    			}else{
    				$device_record = Device::where('mac_address',$device_mac)->where('status',1)->select('id','location_id')->first();
    				if($device_record){
    					$data['location_id']  = $device_record->location_id;
    					$device_status =  true;
    				}else{
    					$trim_mac = trimDeviceMac($device_mac);
    					$device_record = Device::where('mac_address','like','$trim_mac%')->where('status',1)->select('id')->first();
    					if($device_record){
    						$data['location_id'] = $device_record->location_id;
    						$device_status = true;
    					}
    				}
    			}
    		}
    		
    	if ($device_status) {
				$nasid = $request->input('nasid');
				$campaign_id = LocationSchedule::getActiveCampaignId( $nasid, 'id' );
				
				$data['campaign'] = NULL;
				$data['appinfo'] = NULL;
				if (is_numeric ( $campaign_id ) && $campaign_id > 0) {
					
					Session::put('campaign.id',$campaign_id);
					
					/* Preparing Landing Page data by campaign id */
					$campaign = Campaign::find($campaign_id);
					
					$sql = "SELECT IF(`site_id` IS NULL, 0, `site_id`) AS site_id FROM `user` WHERE `id` = " . $campaign->owner . " LIMIT 1 ";
					$query = DB::select(DB::raw($sql));
					$site_id = 0;
					if ($query) {
						$result = $query[0];
						$site_id = $result->site_id;
					}
					
					$data['campaign'] = $campaign;
					
					$query = DB::select(DB::raw("SELECT app_type from app_campaign_details WHERE campaign_id = $campaign_id AND app_info_id > 0"));
					$apptype = array ();
					if ($query) {
						foreach($query as $row){
							$apptype [] = $row->app_type;
						}
					}
					$data['apptype'] = $apptype;
					
					$analytics_header_script = CampaignMeta::getCampaignMeta( $campaign_id, 'analytics_header_script' );
					$analytics_footer_script = CampaignMeta::getCampaignMeta( $campaign_id, 'analytics_footer_script' );
					$conversion_tracking_code = CampaignMeta::getCampaignMeta( $campaign_id, 'conversion_tracking_code' );
					
					Session::put('analytics_header_script', $analytics_header_script != "" ? base64_decode ( $analytics_header_script ) : "");
					Session::put('analytics_footer_script', $analytics_footer_script != "" ? base64_decode ( $analytics_footer_script ) : "");
					Session::put('conversion_tracking_code',$conversion_tracking_code != "" ? base64_decode ( $conversion_tracking_code ) : "");
					
					// Getting timezone
					Session::put('time_zone', "");
					$sql = "SELECT u.time_zone AS user_timezone, l.time_zone AS location_time_zone FROM `location` AS l INNER JOIN `user` AS u ON u.id = l.owner WHERE l.identifier = '$nasid' LIMIT 1";
					$query = DB::select(DB::raw($sql));
					if ($query) {
						$data1 = $query[0];
						if (isset ( $data1->location_time_zone ) && $data1->location_time_zone != "") {
							Session::put('time_zone', $data1->location_time_zone);
						} elseif (isset ( $data1->user_timezone ) && $data1->user_timezone != "") {
							Session::put('time_zone',$data1->user_timezone);
						}
					}
					// echo get_campaign_meta($campaign_id, 'language');
					if ($request->has('language')) {
						$lang_code = $request->input('language') != "" ? $request->input('language') : "en";
					} else {
						$lang_code = CampaignMeta::getCampaignMeta($campaign_id, 'language' ) == "" ? "en" : CampaignMeta::getCampaignMeta( $campaign_id, 'language' );
					}
					
					Session::put('lang_code', $lang_code);
					$data['languages'] = getLanguageArray ($request);					
					$data['language'] = $data['languages'][Session::get('lang_code')];
					
					$campaign_meta = CampaignMeta::getCampaignMeta($campaign_id);					
					
					$layer_bg_color = "rgba(255, 255, 255, 0.0)";
					if(isset($campaign_meta['second_layer']) && $campaign_meta['second_layer'] == true){
						$layer_bg_color = "rgba(".$campaign_meta['layer_background_color'].")";
					}
					
					$campaign_meta = array(
						'layer_bg_color' => $layer_bg_color,
						'lang_option' => isset ( $campaign_meta ['show_lang_option'] ) ? $campaign_meta ['show_lang_option'] : "false"
					);
					
					Session::put('campaign.autoemail_status',isset($campaign_meta ['auto_email']) ? $campaign_meta ['auto_email'] : "false");
					if (isset($campaign_meta ['auto_email']) && $campaign_meta ['auto_email'] == 'true') {
						Session::put('campaign.autoemail_sender_name', isset ( $campaign_meta ['sender_name'] ) ? $campaign_meta ['sender_name'] : "");
						Session::put('campaign.autoemail_sender_email',isset ( $campaign_meta ['sender_email'] ) ? $campaign_meta ['sender_email'] : "");
						Session::put('campaign.autoemail_subject', isset ( $campaign_meta ['subject'] ) ? $campaign_meta ['subject'] : "");

						Session::put('campaign.autoemail_message',"");
						if(isset($campaign_meta ['message']) && $campaign_meta ['message'] != ""){
							$host = 'https://'.$request->server('HTTP_HOST').'/uploads/';
							Session::put('campaign.autoemail_message',str_replace("/uploads/", $host, $campaign_meta ['message']));
						}
					}
					
					$facebook_share = isset($campaign_meta ['auto_post']) ? $campaign_meta ['auto_post'] : "false";
					$facebook_like = isset($campaign_meta ['facebook_like']) ? $campaign_meta ['facebook_like'] : "false";
					
					$post_login_page_url = "";
					if ($facebook_share == 'true' || $facebook_like == 'true') {
						$post_login_page_url = url( 'landingpage/postlogin' );
					}
					
					$thankyou_page_url = url ( 'landingpage/thankyoupage&cid=' . $campaign_id );
					if ($campaign->success_login_url != "") {
						$thankyou_page_url = $campaign->success_login_url;
					}
					
					$on_login_success_url = $post_login_page_url == "" ? $thankyou_page_url : $post_login_page_url;
					
					if (! Session::get('user_requested_url') || Session::get('user_requested_url') == "") {
						Session::put('user_requested_url',$request->has('userurl') ? $request->input('userurl') : "");
					}
					
					$info = array (
							'challenge' => $request->has('challenge') ? $request->input('challenge') : "",
							'uamip' => $request->has('uamip') ? $request->input('uamip') : "",
							'uamport' => $request->has('uamport') ? $request->input('uamport') : "",
							'userurl' => $request->has('userurl') ? $request->input('userurl') : "",
							'device_mac' => $request->has('called') ? $request->input('called') : "",
							'client_mac' => $request->has('mac') ? $request->input('mac') : "",
							'location_id' => $data['location_id'],
							'on_login_success_url' => $on_login_success_url,
							'thankyou_page_url' => $thankyou_page_url,
							'site_id' => $site_id 
					);
					Session::put('info',$info);
					
					/**
					 * Inserting data into social_user_device_info table
					 */
					
					if ($request->input('m') == 'landingpage' && $request->input('a') == 'viewlandingpage' && ! Session::get('USER_ID')) {
						$session_id = session_id ();
						$query = DB::select(DB::raw("SELECT `session_id` FROM `social_user_device_info` WHERE `session_id` = '$session_id' LIMIT 1"));
						if (!$query) {
							$device_details = getDeviceDetails ($request);
							$device_details ['suid'] = "";
							$device_details ['campaign_id'] = $campaign_id;
							$device_details ['location_id'] = $this->location_id;
							$device_details ['plan'] = "";
							$device_details ['session_id'] = $session_id;
							$device_details ['client_ip'] = $request->has('ip') ? $request->input('ip') : "";
							$device_details ['client_mac'] = $request->has('mac') ? $request->input('mac') : "";
							$device_details ['device_mac'] = $request->has('called') ?$request->input('called') : "";
							$column = $value = "";
							foreach ( $device_details as $col => $val ) {
								$column .= "`$col`,";
								$value .= "'$val',";
							}
							$sql = "INSERT INTO `social_user_device_info` (" . rtrim ( $column, ',' ) . ") VALUES (" . rtrim ( $value, ',' ) . ")";
							DB::insert(DB::raw($sql));
						}
					}
				
				/**
				 * ****END*****
				 */
					// echo "<pre>";print_r($_SESSION);
				} else {
					die ( Option::getOption( 'no_campaign', "0" ) );
				}
			} else {
				die ( Option::getOption( 'device_not_active', "0" ) );
			}
		} else {
			die ( GenerateConfirmationMessage ( "danger", "<i class='entypo-alert'></i> Invalid request." ) );
		}  
		$data['standard_terms_privacy'] = Option::getOption('standard_terms_privacy','');
		$data['campaign_meta'] =$campaign_meta;		
		$data['layer_bg_color']  =$layer_bg_color;
	    return view('landingpage.viewlandingpage',$data);
    }
}
