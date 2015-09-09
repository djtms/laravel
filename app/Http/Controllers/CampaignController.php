<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\AppCampaignDetails;
use App\CampaignMeta;
use App\Http\Requests;
use App\Campaign;
use App\Option;
use App\AppInfo;
use App\User;
use App\Location;
require_once app_path().'/helper/helper.php';

class CampaignController extends Controller
{
	public function __construct(){
		view()->share('controller','campaign');	
		$this->middleware('auth');
		$this->middleware('boot');			
	}
    public function view(Request $request){
    	view()->share('action','view');
    	$owner = Session::get('USER_TYPE') == '3' ? Session::get('USER_CREATED_BY') : Session::get('USER_ID');		
		if ($request->isMethod('post')) {			
			$id = $request->input('id-campaign');
			if ($id != '0') {
				$campaign = Campaign::where('id',$id)->first();
			} else {
				$campaign = new Campaign;
				$campaign->owner = $owner;
				$campaign->status = 1;
			}
			$campaign->name = $request->input('campaign-name');
			$campaign->ssid = $request->input('ssid-name');
			$campaign->location_id = 0;
			$campaign->language = $request->input('language');
			$campaign->text_color =$request->input('textcolor');
			$campaign->background_color = $request->input('backgroundcolor');
			$campaign->header_html = $request->input('editor1');
			$campaign->footer_html =$request->input('editor2');			
			$campaign->success_login_url = $request->input('success_login_url');
			
			if ($request->has('check-editor4') && $request->input('check-editor4') == 'on') {
				$campaign->check_thank_you_page = 1;
				$campaign->thankyou_page = $request->input('editor4');
			} else {
				$campaign->check_thank_you_page = 0;
				$campaign->thankyou_page = '';
			}
			
			if ($request->has('chk_standard_terms_privacy') && $request->input('chk_standard_terms_privacy') == 'on') {
				$campaign->check_custom_term = 1;
				$campaign->custom_term =  $request->input('txarea_standard_terms_privacy');
			} else {
				$campaign->check_custom_term = 0;
				$campaign->custom_term ='';
			}
			$cvb = "";
			if ($request->has('email_login')) {
				if ($request->has('selectmultiple') && count ($request->input('selectmultiple')) > 0) {
					foreach ($request->input('selectmultiple') as $cv ) {
						$cvb .= $cv . ';';
					}
				}
			}
			$campaign->fields_email = $cvb;			

			if ($request->has('check-editor5') && $request->input('check-editor5') == 'on') {
				$campaign->auto_email = 1;
			} else {
				$campaign->auto_email = 0;
			}
			if ($request->has('check-editor6') && $request->input('check-editor6') == 'on') {
				$campaign->auto_post  = 1;
			} else {
				$campaign->auto_post = 0;
			}			
			$campaign->background_image = $request->input('backgroundimage');
			if ($id == '0') {
				$campaign->data_create = date('Y-m-d');
			}
			$campaign->last_modifie = date('Y-m-d');
			$campaign->save ();
			
			/* Manage Campaign Meta */
			if ($campaign->id > 0) {
				$cid = $campaign->id;
								
				if ($request->has('check-editor4') && $request->input('check-editor4') == 'on') {
					CampaignMeta::addCampaignMeta($cid, 'conversion_tracking_code', base64_encode($request->input('txarea_conersion_tracking_code')));
					$redirect_time = $request->has('redirection_time_of_thankyou_page') ? $request->input('redirection_time_of_thankyou_page') : "5";
					CampaignMeta::addCampaignMeta( $cid, 'redirection_time_of_thankyou_page', ($redirect_time*1000) );
				} else {
					CampaignMeta::addCampaignMeta( $cid, 'conversion_tracking_code', '' );
					CampaignMeta::addCampaignMeta( $cid, 'redirection_time_of_thankyou_page', "0" );
				}
				
				if ($request->has('check-editor5') && $request->input('check-editor5') == 'on') {
					CampaignMeta::addCampaignMeta ( $cid, 'auto_email', 'true' );
					CampaignMeta::addCampaignMeta ( $cid, 'sender_name', strip_tags ($request->input('sender_name')));
					CampaignMeta::addCampaignMeta ( $cid, 'sender_email', strip_tags ($request->input('sender_email')));
					CampaignMeta::addCampaignMeta ( $cid, 'subject', strip_tags ( $request->input('subject')));
					CampaignMeta::addCampaignMeta ( $cid, 'message', $request->input('editor5'));
				} else {
					CampaignMeta::addCampaignMeta( $cid, 'auto_email', 'false' );
				}
				
				if ($request->has('check-editor6') && $request->input('check-editor6') == 'on') {
					CampaignMeta::addCampaignMeta ( $cid, 'auto_post', 'true' );
					CampaignMeta::addCampaignMeta ( $cid, 'post_name', strip_tags ( $request->input('post_name')));
					CampaignMeta::addCampaignMeta( $cid, 'post_link', strip_tags ( $request->input('post_link')));
					CampaignMeta::addCampaignMeta( $cid, 'post_caption', strip_tags ($request->input('post_caption')));
					CampaignMeta::addCampaignMeta( $cid, 'post_description', strip_tags ($request->input('post_description')));
				} else {
					CampaignMeta::addCampaignMeta( $cid, 'auto_post', 'false' );
				}
				
				if ($request->has('chk_facebook_like') && $request->input('chk_facebook_like') == 'on'){
					CampaignMeta::addCampaignMeta ( $cid, 'facebook_like', 'true' );
					CampaignMeta::addCampaignMeta ( $cid, 'facebook_page', strip_tags ( $request->input('facebook_page')));
				} else {
					CampaignMeta::addCampaignMeta ( $cid, 'facebook_like', 'false' );
				}
				
				if ($request->has('chk_autoresponder') && $request->input('chk_autoresponder') == 'on') {
					CampaignMeta::addCampaignMeta ( $cid, 'autoresponder', 'true' );
					CampaignMeta::addCampaignMeta ( $cid, 'autoresponder_api', $request->input('autoresponder'));
					CampaignMeta::addCampaignMeta ( $cid, 'autoresponder_list',$request->input('autoresponder_list'));
				} else {
					CampaignMeta::addCampaignMeta ( $cid, 'autoresponder', 'false' );
				}
				
				$lang = $request->has('language') && $request->has('language') != "" ? $request->input('language') : "en";
				CampaignMeta::addCampaignMeta ( $cid, 'language', $lang );
				
				if ($request->has('chk_emailreporting') && $request->input('chk_emailreporting') == 'on' && $request->input('emailreporting_email') != "") {
					CampaignMeta::addCampaignMeta ( $cid, 'emailreporting', 'true' );
					CampaignMeta::addCampaignMeta ( $cid, 'emailreporting_email', $request->input('emailreporting_email'));
					CampaignMeta::addCampaignMeta ( $cid, 'frequency_of_report', $request->input('frequency_of_report'));
				} else {
					CampaignMeta::addCampaignMeta ( $cid, 'emailreporting', 'false' );
					CampaignMeta::addCampaignMeta ( $cid, 'emailreporting_email', "" );
					CampaignMeta::addCampaignMeta ( $cid, 'frequency_of_report', "" );
				}
				
				if ($request->has('chk_language_option') && $request->input('chk_language_option') == "on") {
					CampaignMeta::addCampaignMeta ( $cid, 'show_lang_option', 'true' );
				} else {
					CampaignMeta::addCampaignMeta ( $cid, 'show_lang_option', 'false' );
				}
				
				if ($request->has('chk_tripadvisor') && $request->input('chk_tripadvisor') == "on") {
					CampaignMeta::addCampaignMeta ( $cid, 'tripadvisor', 'true' );
					CampaignMeta::addCampaignMeta ( $cid, 'tripadvisor_markup', base64_encode(str_replace("http://", "https://", $request->input('tripadvisor'))));
				} else {
					CampaignMeta::addCampaignMeta ( $cid, 'tripadvisor', 'false' );
					CampaignMeta::addCampaignMeta ( $cid, 'tripadvisor_markup', "" );
				}
				
				if ($request->has('chk_layer') && $request->input('chk_layer') == "on") {
					$layer_color = str_replace( array('rgb', '(', ')'), '', $request->input('layer_color'));
					$opacity = ($request->input('opacity_slider_value')/10);
					$layer_bg_color = $layer_color.', '.$opacity;
					CampaignMeta::addCampaignMeta ( $cid, 'second_layer', 'true' );
					CampaignMeta::addCampaignMeta ( $cid, 'layer_background_color', $layer_bg_color );
				}else{
					CampaignMeta::addCampaignMeta ( $cid, 'second_layer', 'false' );
					CampaignMeta::addCampaignMeta ( $cid, 'layer_background_color', "" );
				}
				
				
				CampaignMeta::addCampaignMeta ( $cid, 'analytics_header_script', base64_encode ( $request->input('analytics_header_script')));
				CampaignMeta::addCampaignMeta ( $cid, 'analytics_footer_script', base64_encode ( $request->input('analytics_footer_script')));
			}
			/* END */
			
			$id_app_details = $campaign->id;
			if ($request->has('app-facebook-hidden')) {
				$fb_data = AppCampaignDetails::getByAppType($id_app_details, 1);
				$appid = $request->input('app-facebook-hidden') > 0 ? $request->input('app-facebook'):0;				
				if ($fb_data != null) {
					$appcampaign =  AppCampaignDetails::where('id',$fb_data->id)->update(array('app_info_id'=>$appid,'campaign_id'=>$id_app_details,'app_type'=>1));
				} else {
					$appcampaign = new AppCampaignDetails;
					$appcampaign->app_info_id =  $appid;
					$appcampaign->campaign_id =  $id_app_details;
					$appcampaign->app_type = 1;
					$appcampaign->save ();
				}				
			}
			if ($request->has('app-twitter-hidden')) {
				$twit_data = AppCampaignDetails::getByAppType($id_app_details, 2 );
				$appid = $request->input('app-twitter-hidden') > 0 ? $request->input('app-twitter') : 0;				
				if ($twit_data != null) {
					$appcampaign =  AppCampaignDetails::where('id',$twit_data->id)->update(array('app_info_id'=>$appid,'campaign_id'=>$id_app_details,'app_type'=>2));
				} else {
					$appcampaign = new AppCampaignDetails;
					$appcampaign->app_info_id = $appid;
					$appcampaign->campaign_id = $id_app_details;
					$appcampaign->app_type = 2;
					$appcampaign->save ();
				}				
				
			}
			if ($request->has('app-google-hidden')){
				$google_data = AppCampaignDetails::getByAppType($id_app_details, 3 );
				$appid = $request->input('app-google-hidden') > 0 ? $request->input('app-google') : 0;
				if($google_data != null){
					$appcampaign =  AppCampaignDetails::where('id',$google_data->id)->update(array('app_info_id'=>$appid,'campaign_id'=>$id_app_details,'app_type'=>3));
				}else{
					$appcampaign = new AppCampaignDetails;
					$appcampaign->app_info_id = $appid;
					$appcampaign->campaign_id = $id_app_details;
					$appcampaign->app_type = 3;
					$appcampaign->save ();
				}			
				
			}
			if ($request->has('app-linkedin-hidden')) {
				$linkedin_data = AppCampaignDetails::getByAppType($id_app_details, 4 );
				$appid = $request->input('app-linkedin-hidden') > 0 ?$request->input('app-linkedin') : 0;
				
				if ($linkedin_data != null) {
					$appcampaign =  AppCampaignDetails::where('id',$linkedin_data->id)->update(array('app_info_id'=>$appid,'campaign_id'=>$id_app_details,'app_type'=>4));
				} else {
					$appcampaign = new AppCampaignDetails;
					$appcampaign->app_info_id = $appid;
					$appcampaign->campaign_id = $id_app_details;
					$appcampaign->app_type = 4;
					$appcampaign->save ();
				}				
			}
			if ($request->has('app-instagram-hidden')) {
				$instagram_data = AppCampaignDetails::getByAppType( $id_app_details, 6 );
				$appid = $request->input('app-instagram-hidden') > 0 ? $request->input('app-instagram') : 0;
				if ($instagram_data != null) {
					$campaign = AppCampaignDetails::where('id',$instagram_data->id)->update(array('app_info_id'=>$appid,'campaign_id'=>$id_app_details,'app_type'=>6));
				} else {
					$appcampaign->save ();
					$appcampaign->app_info_id = $appid;
					$appcampaign->campaign_id = $id_app_details;
					$appcampaign->app_type = 6;
					$appcampaign->save ();
				}				
			}
			if ($request->has('app-vkontakte-hidden')) {
				$instagram_data = AppCampaignDetails::getByAppType($id_app_details, 7 );
				$appid = $request->input('app-vkontakte-hidden') > 0 ? $request->input('app-vkontakte') : 0;
				
				if ($instagram_data != null) {
					$appcampaign=AppCampaignDetails::where('id',$instagram_data->id)->update(array('app_info_id'=>$appid,'campaign_id'=>$id_app_details,'app_type'=>7));
				} else {
					$appcampaign = new AppCampaignDetails;
					$appcampaign->app_info_id =  $appid;
					$appcampaign->campaign_id = $id_app_details;
					$appcampaign->app_type = 7;
					$appcampaign->save ();
				}
				
			}
			if ($request->has('app-facebook-like-hidden')){
				$fblike_data = AppCampaignDetails::getByAppType($id_app_details, 5 );
				$appid = $request->input('app-facebook-like-hidden') > 0 ? $request->input('app-facebook-like') : 0;
				
				if ($fblike_data != null) {
					$appcampaign = AppCampaignDetails::where('id',$fblike_data->id)->update(array('app_info_id'=>$appid,'campaign_id'=>$id_app_details,'app_type'=>5));
				} else {
					$appcampaign = new AppCampaignDetails;
					$appcampaign->app_info_id = $appid;
					$appcampaign->campaign_id = $id_app_details;
					$appcampaign->app_type =  5;
					$appcampaign->save ();
				}				
			}
		}   
		
		$fb_apps = AppInfo::getByAppType(1);
		$tw_apps = AppInfo::getByAppType(2);
		$gp_apps = AppInfo::getByAppType(3);
		$li_apps = AppInfo::getByAppType(4);
		$ig_apps = AppInfo::getByAppType(6);
		$vk_apps = AppInfo::getByAppType(7);
		$filename = $request->server('DOCUMENT_ROOT').'/language/lang.txt';
		$contents = file_get_contents($filename);
		$language = json_decode($contents);
		
		$data = array (
				'fb_apps' => $fb_apps,
				'tw_apps' => $tw_apps,
				'li_apps' => $li_apps,
				'gp_apps' => $gp_apps,
				'ig_apps' => $ig_apps,
				'vk_apps' => $vk_apps,
				'language_list' => $language 
		);
		$result = User::find($owner);        
        $data['platform_user_email'] = $result->email_address;
        $data['camp_id'] = $request->input('camp_id');	
		$data['standard_terms_privacy'] = Option::getOption('standard_terms_privacy','','0');		
	
		return view('campaign.view',$data);		
    }
    
    public function delete(Request $request){
    	$id = $request->input('campaign_id');
    	$nums = Campaign::where('id',$id)->update(array('last_modifie'=>date('Y-m-d h:i:s'),'remove'=>'1'));
    	if($nums > 0){
    		$message = GenerateConfirmationMessage ( 'success', '<i class="entypo-info-circled"></i> Campaign has been successfully removed.' );
    	}else{
    		$message = GenerateConfirmationMessage ( 'danger', '<i class="entypo-info-circled"></i> Campaign has not been successfully removed.' );
    	}
    	
    	Session::put('SESSION_MESSAGE',$message);
    	return redirect(url('campaign/view'));
    }
}
