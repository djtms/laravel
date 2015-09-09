<?php

namespace App\Http\Controllers;

use App\SocialUser;

use Illuminate\Support\Facades\Storage;

use App\UserMeta;

use App\SocialUserDeviceInfo;

use App\UserPermission;

use App\User;

use App\SubScriptionDetail;

use App\Option;

use App\LocationSchedule;
use Aws\load_compiled_json;
use App\AppInfo;
use App\AppCampaignDetails;
use Illuminate\Support\Facades\Session;
use App\Campaign;
use DB;
use App\CampaignMeta;
use App\DeviceStatus;
use App\Device;
use App\Location;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use stdClass;
use Illuminate\Support\Facades\Config;
use Exception;

require_once app_path().'/helper/getresponse/jsonRPCClient.php';
require_once app_path().'/helper/icontact/iContactApi.php';
require_once app_path().'/helper/mailchimp/MailChimp.php';
require_once app_path().'/helper/deviceinfo.php';
require_once app_path().'/helper/helper.php';

class AjaxController extends Controller
{   
    public function uploadcampaignbackgroundimage(Request $request){
    	$this->checkAuthentication();
    	$output = array();
    	$file = $request->file('uploadfile');
    	if($file->isValid()){
    		if($file->getClientSize() <= 1048576){
    			$validextensions = array('jpeg','jpg','png');    			
    			if(in_array($file->getClientOriginalExtension(), $validextensions)){
    				$filename = uniqid(time(),false).'.'.$file->getClientOriginalExtension();
    				$targetPath = Config::get('aws.UPLOAD').$filename;
    				try{
    					$s3 = Storage::disk('s3');
    					$s3->put($targetPath,file_get_contents($file),'public');
    					$output['status'] = 'succeed';
    					$output['filename'] = Config::get('aws.AWS_CDN').$targetPath;
    				}catch(Exception $e){
    				    $output ['status'] = 'failed';
						$output ['message'] = 'Operation failed';
    				}
    			}else{
    				$output['status'] = 'failed';
    		        $output['message'] = '.jpeg, .jpg, .png format are allowed';
    			}
    		}else{
    			$output['status'] = 'failed';
    		    $output['message'] = 'File size should not be greater than 1MB';
    		}
    	}else{
    		$output['status'] = 'failed';
    		$output['message'] = 'Operation has been terminated, try again.';
    	}
    	return json_encode($output);
    }
    
    
    public function checkAuthentication(){
    	if(Session::get('USER_ID','') == ''){
    		exit('You are not authorized');
    	}
    }
    
    public function GetAllCampaigns(Request $request){
    	$this->checkAuthentication();
    	$output = '<div class="row">';
    	$item_per_page = 10;
    	if($request->has('page')){
    		$page_number = filter_var($request->input('page'),FILTER_SANITIZE_NUMBER_INT,FILTER_FLAG_STRIP_HIGH);
    		if(!is_numeric($page_number)){
    			die('Invalid page number');
    		}
    	}else{
    		$page_number = 1;
    	}
    	
    	$page_position = ($page_number -1)*$item_per_page;
    	
    	$where = " where c.remove = 0";    	
    	if(Session::get('USER_TYPE') == '2'){
    		$where .=" and c.owner = ".Session::get('USER_ID');
    	}else if(Session::get('USER_ID') == '3'){
    		$where .=" and c.id IN (".Session::get('CAMPAIGN_IDS').")";
    	}
    	
    	if($request->has('search_value') && $request->input('search_value') != ''){
    		$value = ($request->input('search_value'));    		
			$where .=" and (c.name LIKE '$value%' or u.email_address like '%$value%' or u.full_name like '$value%')";
    	}
    	$sql = "SELECT SQL_CALC_FOUND_ROWS c.id AS campaign_id, c.name AS campaign_title, c.ssid, c.text_color, c.background_color,
				c.background_image, c.header_html, c.footer_html, c.date_create, c.last_modifie, fields_email, 
				IFNULL((SELECT GROUP_CONCAT( app_type SEPARATOR '|' ) FROM app_campaign_details WHERE campaign_id = c.id AND app_info_id > 0 ), 0) AS apps, 
				IFNULL((SELECT GROUP_CONCAT( location_id SEPARATOR ',' ) FROM location_schedule WHERE campaign_id = c.id), 0) AS location_ids 
				FROM `campaign` AS c LEFT JOIN user AS u ON c.owner = u.id
				$where ORDER BY c.id DESC LIMIT $page_position, $item_per_page";
    	$campaigns = DB::select(DB::raw($sql));
    	
    	$total_found_rows = count($campaigns);
    	$total_pages = ceil($total_found_rows / $item_per_page);
    	
    	if($campaigns){
		    $filename = $request->server('DOCUMENT_ROOT').'/language/lang.txt';
    		$contents = file_get_contents($filename);
    		$language_list = json_decode($contents,true);
    		foreach($campaigns as $campaign){
    			$assigned_location = "<span class='badge badge-red'><b>No location assigned</b></span>";
    			$assigned_device = "<label class='badge badge-danger'>No device assigned</label>";
    			$layer_style = '';
    			if($campaign->location_ids > 0){
    				$locations = Location::whereIn('id',explode(',',$campaign->location_ids))->get();
    				if($locations){
    					$assigned_location = '';
    					$assigned_location.='<ul style="list-style:none;padding-left:0px; margin:0px;">';
    					foreach($locations as $location){
    						$location_link = url('location/overview?loca='.$location->id);
    						$assigned_location .='<li style="margin-bottom:2px;"><a style="word-wrap:break-word;white-space:normal;-webkit-hyphens:auto;hyphens:auto;" class="badge badge-blue" href="'.$location_link.'" title="'.$location->name.'">'.$location->name.'</a></li>';
    						
    					}
    					$assigned_location.='</ul>';    					
    				}
    				
    				$devices = Device::whereIn('location_id',explode(',',$campaign->location_ids))->get();
    				if($devices){
    					$assigned_device = '';
    					$assigned_device .= '<ul style="list-style:none;padding-left:0px;margin:0px">';
    					foreach ($devices as $device){
    						$device_status = DeviceStatus::getDeviceStatus($device->mac_address); 
    						$device_link = url('campaign/devicemodal&data='.base64_encode($device->id));
    						$assigned_device.="<li style='margin-bottom&data='".$device_link."' class='badge badge-default' style='white-space:normal'".$device->name." ".$device_status['status_mode']."</a></li>";						
    					}   					
    					$assigned_device .= "</ul>";
    				}
    			}
    			
    			$temp_lang_code = CampaignMeta::getCampaignMeta($campaign->campaign_id,'language');
    			$lang_code = !$temp_lang_code ?"en":$temp_lang_code;
    			$language = $language_list[$lang_code];
    			
    			$agree_btn_text = CampaignMeta::getCampaignMeta($campaign->campaign_id,'language');

    			$campaign_header ="";
    			$campaign_footer = "";
    			$social_media_button = "";
    			$social_media_icon = "";
    			$background_image = "";
    			$background_color = "";
    			$lang_op_markup = "";
    			if($campaign->header_html){
    				$campaign_header = "<div class='row'><div class='col-md-12'><div class='header-mob'>" . $campaign->header_html . "</div></div></div>";
    			}
    			
    			if($campaign->footer_html){
    				$campaign_footer = "<div class='row'><div class='col-md-12'><div class='footer-mob'>" . $campaign->footer_html . "</div></div></div>";
    			}
    			
    			if($campaign->apps > 0){
    				$apps = explode('|',$campaign->apps);
    				foreach ($apps as $app){
    					switch($app){
    						case '1' :
								$social_media_button .= "<div class='conect-social-icon'><a class='btn btn-block btn-social btn-facebook'><i class='fa fa-facebook'></i> " . $language ['facebook'] . "</a></div>";
								$social_media_icon .= "<i class='fa fa-facebook-square' style='padding-right:5px;'></i>";
								break;
							case '2' :
								$social_media_button .= "<div class='conect-social-icon'><a class='btn btn-block btn-social btn-twitter'><i class='fa fa-twitter'></i> " . $language ['twitter'] . "</a></div>";
								$social_media_icon .= "<i class='fa fa-twitter-square' style='padding-right:5px;'></i>";
								break;
							case '3' :
								$social_media_button .= "<div class='conect-social-icon'><a class='btn btn-block btn-social btn-google-plus'><i class='fa fa-google-plus'></i> " . $language ['google'] . "</a></div>";
								$social_media_icon .= "<i class='fa fa-google-plus-square' style='padding-right:5px;'></i>";
								break;
							case '4' :
								$social_media_button .= "<div class='conect-social-icon'><a class='btn btn-block btn-social btn-linkedin'><i class='fa fa-linkedin'></i> " . $language ['linkedin'] . "</a></div>";
								$social_media_icon .= "<i class='fa fa-linkedin-square' style='padding-right:5px;'></i>";
								break;
							case '6' :
								$social_media_button .= "<div class='conect-social-icon'><a class='btn btn-block btn-social btn-instagram'><i class='fa fa-instagram' style='color:#FFF;'></i> " . $language ['instagram'] . "</a></div>";
								$social_media_icon .= "<i class='fa fa-instagram' style='padding-right:5px;'></i>";
								break;
							case '7' :
								$social_media_button .= "<div class='conect-social-icon'><a class='btn btn-block btn-social btn-vk'><i class='fa fa-vk' style='color:#FFF;'></i> Connect With Instagram</a></div>";
								$social_media_icon .= "<i class='fa fa-vk' style='padding-right:5px;'></i>";
								break;
    					}
    				}
    			}
    			if($campaign->fields_email){
    				$social_media_button .= "<div class='conect-social-icon'><a class='btn btn-block btn-social btn-openid'><i class='fa fa-envelope-o'></i> " . $language ['email'] . "</a></div>";
					$social_media_icon .= "<i class='fa fa-envelope-square' style='padding-right:5px;'></i>";
    			}
    			
    			if($campaign->background_image){
    				$path = $campaign->background_image;
					$background_image = "background-image: url(" . $path . "); ";
    			}
    			
    			if($campaign->background_color){
    				$background_color = "background-color:" . $campaign->background_color . "; ";
    			}
    			
    			if(CampaignMeta::getCampaignMeta($campaign->campaign_id,'second_layer') == 'true'){
    				$lang_op_markup .= '<div class="row">
											<div class="col-md-12">
												<select class="form-control conect-social">
													<option>-- Select Language --</option>
												</select>
											</div>
										</div>';
    			}
    			
    			if(CampaignMeta::getCampaignMeta($campaign->campaign_id,'second_layer') == 'true'){
    				$layer_color = CampaignMeta::getCampaignMeta($campaign->campaign_id,'layer_background_color');
    				$layer_style = "style='background-color: rgba($layer_color);border-radius: 15px;'";
    			}
    			
    			$output .= "<div class='col-sm-6'>
								<div class='panel panel-primary campaign'>
									<div class='panel-heading'>
		                                <div class='panel-title'><h4><strong>" . $campaign->campaign_title . "</strong></h4></div>
		                            </div>
                                	<div class='panel-body'>
                                		<div class='row'>
		                                	<div class='col-sm-7 col-md-6 col-lg-5'>
		                                		<div class='backgroundiphone text-center'>
		                                		
		                                			<div class='backgroundiphone-wrapper'>
		                                				<div class='backgroundiphone-content scrollable' style='" . $background_image . $background_color . "'>
		                                						<div id='second_layer' $layer_style>
		                                					" . $lang_op_markup . "
	                                						" . $campaign_header . "
                                							<div class='row'>
		                                                        <div class='backgroundiphone-header'>
		                                                            <div class='col-md-12 switch-box text-center'>
		                                                                <input type='checkbox' class='boots-switch' data-size='mini' data-on-color='success' checked='checked' data-on-text='" . $agree_btn_text [0] . "' data-off-text='" . $agree_btn_text [1] . "'/>
		                                                            </div>																
		                                                            <div class='col-md-12' style='text-align: center;'><p>" . $language ['agree'] . "</p></div>
		                                                        </div>
		                                                    </div>
                                                            <div class='row'>
                                                        		<div class='col-md-12 conect-social'>
                                                            		" . $social_media_button . "
		                                                        </div>
                                                        	</div>
                                                            " . $campaign_footer . "
                                                            		</div>
                                						</div>
		                                			</div>
                                                            		
		                                		</div>	
	                                		</div>
                                			<div class='col-sm-5 col-md-6 col-lg-7' style='margin-top:20px;'>
                                                <div class='row'>
                                                	<div class='col-md-12'>
                                                    	<p style='font-size: 18px; line-height: 32px;'>
															<i class='entypo-signal' style='font-size: 30px; color:#000;'></i>
															" . $campaign->ssid . "
														</p>
														<div class='text-left'>
															<p><b>Active Social Connection:</b></p>
		                                                    <p>" . $social_media_icon . "</p>
														</div>
		                                                <div class='text-left'>
		                                                    <p><b>Location Assigned To:</b></p>
		                                                    " . $assigned_location . "
		                                                </div>
		                                                <div class='text-left'>
		                                                    <p style='margin-top: 10px;'><b>Devices Assigned:</b></p>                                                   
				                                            " . $assigned_device . "
		                                                </div>
                                                    </div>
                                            	</div>
				                                <div class='row'>
                                                    <div class='col-md-12 text-right' style='margin-top: 15px;'>
                                                        <p>
				                                            <button type='button' data-toggle='tooltip' data-placement='top' data-original-title='Edit campaign.' class='btn btn-blue btn-sm' onclick='javascript:editcampaign(" . $campaign->campaign_id . ");'><i class='fa fa-pencil'></i></button>
		                                            		<button data-toggle='tooltip' data-placement='top' data-original-title='Make a clone of this campaign.' class='btn btn-success btn-sm' onclick='javascript:cloneCampaign(" . $campaign->campaign_id . ");'><i class='fa fa-copy'></i></button>
                                            				<button id='" . $campaign->campaign_id . "' type='button' data-toggle='tooltip' data-placement='top' data-original-title='Delete campaign.' onclick='javascript:deleteCampaign(" . $campaign->campaign_id . ");' class='btn btn-red btn-sm'><i class='fa fa-trash-o'></i></button>                                            				
		                                        		</p>
				                                        <p>
															<b>Date Created:</b> " . date ( 'd M, Y', strtotime ( $campaign->date_create ) ) . "<br/>
				                                            <b>Last Modified:</b> " . date ( 'd M, Y', strtotime ( $campaign->last_modifie ) ) . "
														</p>		
                                                    </div>				                                            		
                                                </div>
											</div>		
                                		</div>
                                	</div>	
								</div>
							</div>";
    		}
    	}else{
    		$output .= "<div class='col-md-12'>
						<div class='alert alert-danger'>No Camaign has been found!</div>
					</div>";
    	}
    	
    	$output.="<div>";
    	$output .= $this->paginate_function ( $page_position, $item_per_page, $page_number, $total_found_rows, $total_pages );
    	return $output;
    }
    
    public function paginate_function($page_position, $item_per_page, $current_page, $total_records, $total_pages){
    	$pagination = '';
		if ($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages) { // verify total pages and current page number
			$pagination .= '<ul class="pagination">';
			
			$right_links = $current_page + 3;
			// $previous = $current_page - 3; //previous link
			$next = $current_page + 1; // next link
			$first_link = true; // boolean var to decide our first link
			
			if ($current_page > 1) {
				// $previous_link = ($previous==0)?1:$previous;
				$previous_link = ($current_page > 0) ? $current_page - 1 : 1;
				$pagination .= '<li class="first"><a href="#" data-page="1" title="First">&laquo;</a></li>'; // first link
				$pagination .= '<li><a href="#" data-page="' . $previous_link . '" title="Previous"><i class="entypo-left-open"></i></a></li>'; // previous link
				for($i = ($current_page - 2); $i < $current_page; $i ++) { // Create left-hand side links
					if ($i > 0) {
						$pagination .= '<li><a href="#" data-page="' . $i . '" title="Page' . $i . '">' . $i . '</a></li>';
					}
				}
				$first_link = false; // set first link to false
			}
			
			if ($first_link) { // if current active page is first link
				$pagination .= '<li class="first active"><a>' . $current_page . '</a></li>';
			} elseif ($current_page == $total_pages) { // if it's the last active link
				$pagination .= '<li class="last active"><a>' . $current_page . '</a></li>';
			} else { // regular current link
				$pagination .= '<li class="active"><a>' . $current_page . '</a></li>';
			}
			
			for($i = $current_page + 1; $i < $right_links; $i ++) { // create right-hand side links
				if ($i <= $total_pages) {
					$pagination .= '<li><a href="#" data-page="' . $i . '" title="Page ' . $i . '">' . $i . '</a></li>';
				}
			}
			if ($current_page < $total_pages) {
				// $next_link = ($i > $total_pages)? $total_pages : $i;
				$next_link = ($current_page == $total_pages) ? $total_pages : $current_page + 1;
				$pagination .= '<li><a href="#" data-page="' . $next_link . '" title="Next"><i class="entypo-right-open"></i></a></li>'; // next link
				$pagination .= '<li class="last"><a href="#" data-page="' . $total_pages . '" title="Last">&raquo;</a></li>'; // last link
			}
			
			$pagination .= '</ul>';
			$data_start = $page_position + 1;
			$data_end = (($current_page * $item_per_page) > $total_records ? $total_records : ($current_page * $item_per_page));
			$pagination .= '<p>Showing ' . $data_start . ' to ' . $data_end . ' of ' . $total_records . ' entries</p>';
		}
		return $pagination; // return pagination links
    }
    
    public function GetLanguageDetails(Request $request){
    	$lang_code = $request->input('lang_code');
    	$filename = 'language/lang.txt';
    	$contents = file_get_contents($request->server('DOCUMENT_ROOT').'/'.$filename);
    	$languages = json_decode($contents,true);
    	$language = $languages[$lang_code];
    	return json_encode($language); 
    }
    
    public function RetrieveCampaignById(Request $request){
    	$this->checkAuthentication();
    	$id = $request->input('id');
    	$campaign = Campaign::find($id);
    	$campaign_meta  =CampaignMeta::getCampaignMeta($id);
    	$appfacebook = AppCampaignDetails::getByAppType($id,1);
    	$apptwitter = AppCampaignDetails::getByAppType($id,2);
    	$appgoogle = AppCampaignDetails::getByAppType($id,3);
    	$applinkedin = AppCampaignDetails::getByAppType($id,4);
    	$appfacebooklike = AppCampaignDetails::getByAppType($id,5);
    	$appinstagram = AppCampaignDetails::getByAppType($id,6);
    	$appvkontakte = AppCampaignDetails::getByAppType($id,7);
    	$fid = '';
		$fname = '';
		$tid = '';
		$tname = '';
		$gid = '';
		$gname = '';
		$lid = '';
		$lname = '';
		$igid = '';
		$igname = '';
		$vkid = '';
		$vkname = '';
		$flid = '';
		$flname = '';
		if($applinkedin != null){
			$linkedin = AppInfo::find($applinkedin->app_info_id);
			if($linkedin != null){
				$lid = $linkedin->id;
				$lname = $linkedin->app_name;
			}
		}
		
		if($appgoogle != null){
			$google = AppInfo::find($appgoogle->app_info_id);
			if($google != null){
				$gid = $google->id;
				$gname = $google->app_name;
			}
		}
		
		if($appfacebook != null){
			$facebook  = AppInfo::find($appfacebook->app_info_id);
			if($facebook != null){
				$fid =  $facebook->id;
				$fname = $facebook->app_name;
			}
		}
		
		if($appfacebooklike != null){
			$facebooklike = AppInfo::find($appfacebooklike->app_info_id);
			if($facebooklike != null){
				$flid  = $facebooklike->id;
				$flname = $facebooklike->app_name;
			}		
		}
		
		if($apptwitter != null){
			$twitter = AppInfo::find($apptwitter->app_info_id);
			if($twitter!= null){
				$tid = $twitter->id;
				$tname = $twitter->name;
			}
		}
		
		if($appinstagram != null){
			$instagram = AppInfo::find($appinstagram->app_info_id);
			if($instagram != null){
				$igid = $instagram->id;
				$igname = $instagram->app_name;
			}
		}
		
		if($appvkontakte != null){
			$vkontakte = AppInfo::find($appvkontakte->app_info_id);
			if($vkontakte != null){
				$vkid = $vkontakte->id;
				$vkname = $vkontakte->app_name;
			}
		}
		
		$layer_rgb  = "255,255,255";
		$opacity = "0";
		if(isset($campaign_meta['layer_background_color']) && $campaign_meta['layer_background_color'] != ""){
			$temp_data = explode(',', $campaign_meta ['layer_background_color']);
			$layer_rgb = $temp_data[0].', '.$temp_data[1].', '.$temp_data[2];
			$opacity = $temp_data[3];
		}
		
		$cp = array (
				'id' => $campaign->id,
				'name' => $campaign->name,
				'ssid' => $campaign->ssid,
				'locationid' => $campaign->location_id,
				'lang_code' =>  $campaign->language ? $campaign->language : "en",
				'textcolor' => $campaign->text_color,
				'backgroundcolor' => $campaign->background_color,
				'headerhtml' => $campaign->header_html,
				'footer' => $campaign->footer_html,
				'analytics_header_script' => isset ( $campaign_meta ['analytics_header_script'] ) ? base64_decode ( $campaign_meta ['analytics_header_script'] ) : "",
				'analytics_footer_script' => isset ( $campaign_meta ['analytics_footer_script'] ) ? base64_decode ( $campaign_meta ['analytics_footer_script'] ) : "",
				'customterm' => $campaign->custom_term,
				'successloginurl' => $campaign->success_login_url,
				'thankyoupage' => $campaign->thankyou_page,
				'conversion_tracking_code' => isset ( $campaign_meta ['conversion_tracking_code'] ) ? base64_decode ( $campaign_meta ['conversion_tracking_code'] ) : "",
				'redirection_time' => isset ( $campaign_meta ['redirection_time_of_thankyou_page'] ) ? $campaign_meta ['redirection_time_of_thankyou_page'] : "5000",
				'autoemail' => isset ( $campaign_meta ['auto_email'] ) ? $campaign_meta ['auto_email'] : "",
				'sender_name' => isset ( $campaign_meta ['sender_name'] ) ? $campaign_meta ['sender_name'] : "",
				'sender_email' => isset ( $campaign_meta ['sender_email'] ) ? $campaign_meta ['sender_email'] : "",
				'subject' => isset ( $campaign_meta ['subject'] ) ? $campaign_meta ['subject'] : "",
				'message' => isset ( $campaign_meta ['message'] ) ? $campaign_meta ['message'] : "",
				'autoresponder' => isset ( $campaign_meta ['autoresponder'] ) ? $campaign_meta ['autoresponder'] : "",
				'autoresponder_api' => isset ( $campaign_meta ['autoresponder_api'] ) ? $campaign_meta ['autoresponder_api'] : "",
				'autoresponder_list' => isset ( $campaign_meta ['autoresponder_list'] ) ? $campaign_meta ['autoresponder_list'] : "",
				'emailreporting' => isset ( $campaign_meta ['emailreporting'] ) ? $campaign_meta ['emailreporting'] : "",
				'emailreporting_email' => isset ( $campaign_meta ['emailreporting_email'] ) ? $campaign_meta ['emailreporting_email'] : "",
				
				'tripadvisor' => isset ( $campaign_meta ['tripadvisor'] ) ? $campaign_meta ['tripadvisor'] : "false",
				'tripadvisor_markup' => isset ( $campaign_meta ['tripadvisor_markup'] ) ? base64_decode ( $campaign_meta ['tripadvisor_markup'] ) : "",
				
				'second_layer' => isset ( $campaign_meta ['second_layer'] ) ? $campaign_meta ['second_layer'] : "false",
				'layer_rgb' => $layer_rgb,
				'opacity' => $opacity,
				
				'show_lang_option' => isset ( $campaign_meta ['show_lang_option'] ) ? $campaign_meta ['show_lang_option'] : "",
				'frequency_of_report' => isset ( $campaign_meta ['frequency_of_report'] ) ? $campaign_meta ['frequency_of_report'] : "",
				'pictureurl' => $campaign->picture_url,
				'datecreate' => $campaign->date_create,
				'lastmodifie' => $campaign->last_modifie,
				'autopost' => isset ( $campaign_meta ['auto_post'] ) ? $campaign_meta ['auto_post'] : "",
				'post_name' => isset ( $campaign_meta ['post_name'] ) ? $campaign_meta ['post_name'] : "",
				'post_link' => isset ( $campaign_meta ['post_link'] ) ? $campaign_meta ['post_link'] : "",
				'post_caption' => isset ( $campaign_meta ['post_caption'] ) ? $campaign_meta ['post_caption'] : "",
				'post_description' => isset ( $campaign_meta ['post_description'] ) ? $campaign_meta ['post_description'] : "",
				'facebook_like' => isset ( $campaign_meta ['facebook_like'] ) ? $campaign_meta ['facebook_like'] : "",
				'facebook_page' => isset ( $campaign_meta ['facebook_page'] ) ? $campaign_meta ['facebook_page'] : "",
				'backgroundimage' => $campaign->background_image,
				'fieldsemail' => $campaign->fields_email,
				'checkcustomterm' => $campaign->check_custom_term,
				'checkthankyoupage' => $campaign->check_thank_you_page,
				'appfbid' => $fid,
				'appfname' => $fname,
				'apptid' => $tid,
				'apptname' => $tname,
				'appgid' => $gid,
				'appgname' => $gname,
				'applid' => $lid,
				'applname' => $lname,
				'appigid' => $igid,
				'appigname' => $igname,
				'appvkid' => $vkid,
				'appvkname' => $vkname,
				'appflid' => $flid,
				'appflname' => $flname 
		);
		
		return json_encode ( $cp );
    }
    
    public function CheckFacebookPage(Request $request){
    	$output= '';
    	$page = $request->input('page');
    	$content = @file_get_contents( 'https://graph.facebook.com/' . $page );
    	if($content){
    		$data = json_decode($content);
    		$output = $data->id;
    	}
    	return $output;
    }
    
    public function GetAutoresponderList(Request $request){
    	$this->checkAuthentication();
    	$autoresponder = $request->input('value');
    	$selected_list_id = $request->input('selected_list_id');
    	$output = '';
    	$options = '';
    	if($autoresponder){
    		switch($autoresponder){
    			case 'getresponse':
    				$getresponse_data = Option::get('getresponse');
    				if($getresponse_data){
    					$gc_data = json_decode($getresponse_data,true);
    					$api_key = $gc_data['getresponse']['api_key'];
    					$api_url = 'http://api2.getresponse.com';
    					try{
    						$client = new jsonRPCClient($api_url);
    						$campaigns =  $client->get_campaigns($api_key);
    					}catch(Exception $ex){
    						$campaigns = array(
    						    ''=>array('name'=>'Invalid API key')
    						);
    					}
    					
    					if($campaigns){
    					    foreach ( $campaigns as $key => $value ) {
								$id = $key;
								$name = $value ['name'];
								if ($id == $selected_list_id) {
									$options .= "<option selected='selected' value='$id'>$name</option>";
								} else {
									$options .= "<option value='$id'>$name</option>";
								}
							}
    					}
    				}
    				break;
    			case 'icontact':
    		        $icontact_data = Option::get('icontact');
					if ($icontact_data) {
						$ic_data = json_decode ( $icontact_data, true );						
						$config = array (
								'appId' => $ic_data ['icontact'] ['api_key'],
								'apiPassword' => $ic_data ['icontact'] ['password'],
								'apiUsername' => $ic_data ['icontact'] ['username'] 
						);
						iContactApi::getInstance ()->setConfig ( $config );
						$oiContact = iContactApi::getInstance ();
						$lists = $oiContact->getLists ();
						if ($lists) {
							foreach ( $lists as $list ) {
								$id = $list->listId;
								$name = $list->name;
								if ($id == $selected_list_id) {
									$options .= "<option selected='selected' value='$id'>$name</option>";
								} else {
									$options .= "<option value='$id'>$name</option>";
								}
							}
						}
					}
    				break;
    			case 'mailchimp':
    		        $mailchimp_data = Option::get( 'mailchimp' );
					if ($mailchimp_data) {
						$mc_data = json_decode ( $mailchimp_data, true );
						$MailChimp = new MailChimp ( $mc_data ['mailchimp'] ['api_key'] );
						$results = $MailChimp->call ( "lists/list" );
						if ($results) {
							if (isset ( $results ['data'] ) && count ( $results ['data'] ) > 0) {
								foreach ( $results ['data'] as $my_data ) {
									$id = $my_data ['id'];
									$name = $my_data ['name'];
									if ($id == $selected_list_id) {
										$options .= "<option selected='selected' value='$id'>$name</option>";
									} else {
										$options .= "<option value='$id'>$name</option>";
									}
								}
							}
						}
					}
    				break;
    				
    			case 'sendreach':
    		       $sendreach_data = Option::get( 'sendreach' );
					if ($sendreach_data) {
						$sr_data = json_decode ( $sendreach_data );
						$api_vars = array (
								'key' => $sr_data->sendreach->api_key,
								'secret' => $sr_data->sendreach->secret,
								'userid' => $sr_data->sendreach->userid 
						);
						// this query is geting list from sendreach.
						$query = 'http://api.sendreach.com/index.php?key=' . $api_vars ['key'] . '&secret=' . $api_vars ['secret'] . '&action=lists_view';
						$lists_view = file_get_contents ( $query ); // the data is returned in json format
						$lists_view = json_decode ( $lists_view ); // here we convert the json data into a PHP array
						if (! isset ( $lists_view->code ) && $lists_view->code != '400') {
							foreach ( $lists_view as $my_data ) {
								$id = $my_data->id;
								$name = $my_data->list_name;
								if ($id == $selected_list_id) {
									$options .= "<option selected='selected' value='$id'>$name</option>";
								} else {
									$options .= "<option value='$id'>$name</option>";
								}
							}
						}
					}
    				break;
    			case 'activecampaign':
    		        $activecampaign_data = Option::get( 'activecampaign' );
					if ($activecampaign_data) {
						$ac_data = json_decode ( $activecampaign_data );
						$api_key = $ac_data->activecampaign->api_key;
						$url = $ac_data->activecampaign->url;
						
						$params = array (
								
								// the API Key can be found on the "Your Settings" page under the "API" tab.
								// replace this with your API Key
								'api_key' => $api_key,
								
								// this is the action that fetches a list info based on the ID you provide
								'api_action' => 'list_list',
								
								// define the type of output you wish to get back
								// possible values:
								// - 'xml' : you have to write your own XML parser
								// - 'json' : data is returned in JSON format and can be decoded with
								// json_decode() function (included in PHP since 5.2.0)
								// - 'serialize' : data is returned in a serialized format and can be decoded with
								// a native unserialize() function
								'api_output' => 'serialize',
								
								// a comma-separated list of IDs of lists you wish to fetch
								'ids' => 'all',
								
								// filters: supply filters that will narrow down the results
								// 'filters[name]' => 'General', // perform a pattern match (LIKE) for List Name
								
								// include global custom fields? by default, it does not
								// 'global_fields' => true,
								
								// whether or not to return ALL data, or an abbreviated portion (set to 0 for abbreviated)
								'full' => 1 
						);
						
						// This section takes the input fields and converts them to the proper format
						$query = "";
						foreach ( $params as $key => $value )
							$query .= $key . '=' . urlencode ( $value ) . '&';
						$query = rtrim ( $query, '& ' );
						
						// clean up the url
						$url = rtrim ( $url, '/ ' );
						
						// This sample code uses the CURL library for php to establish a connection,
						// submit your request, and show (print out) the response.
						if (! function_exists ( 'curl_init' ))
							// die ( 'CURL not supported. (introduced in PHP 4.0.2)' );
							
							// If JSON is used, check if json_decode is present (PHP 5.2.0+)
							if ($params ['api_output'] == 'json' && ! function_exists ( 'json_decode' )) {
								// die ( 'JSON not supported. (introduced in PHP 5.2.0)' );
							}
							
							// define a final API request - GET
						$api = $url . '/admin/api.php?' . $query;
						
						$request = curl_init ( $api ); // initiate curl object
						curl_setopt ( $request, CURLOPT_HEADER, 0 ); // set to 0 to eliminate header info from response
						curl_setopt ( $request, CURLOPT_RETURNTRANSFER, 1 ); // Returns response data instead of TRUE(1)
						                                                     // curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment if you get no gateway response and are using HTTPS
						curl_setopt ( $request, CURLOPT_FOLLOWLOCATION, true );
						
						$response = ( string ) curl_exec ( $request ); // execute curl fetch and store results in $response
						                                               
						// additional options may be required depending upon your server configuration
						                                               // you can find documentation on curl options at http://www.php.net/curl_setopt
						curl_close ( $request ); // close curl object
						
						if (! $response) {
							// die ( 'Nothing was returned. Do you have a connection to Email Marketing server?' );
						}
						
						// This line takes the response and breaks it into an array using:
						// JSON decoder
						// $result = json_decode($response);
						// unserializer
						$result = unserialize ( $response );
						
						/* removing unnecessery element from array */
						if (isset ( $result ['result_code'] )) {
							unset ( $result ['result_code'] );
						}
						if (isset ( $result ['result_message'] )) {
							unset ( $result ['result_message'] );
						}
						if (isset ( $result ['result_output'] )) {
							unset ( $result ['result_output'] );
						}
						/* End */
						if (count ( $result ) > 0) {
							foreach ( $result as $row ) {
								$id = $row ['id'];
								$name = $row ['name'];
								if ($id == $selected_list_id) {
									$options .= "<option selected='selected' value='$id'>$name</option>";
								} else {
									$options .= "<option value='$id'>$name</option>";
								}
							}
						}
					}    				
    				break;
    		}   		
    		
    	}
    	$output = $options != "" ? $options : "<option value=''>No List/Campaign Found</>";
    	return $output;
    }   
    
    public function editorsimageupload(Request $request){
    	if(!Session::get('SITE_ID') && !Session::get('EAMIL_ADDRESS') != ""){
    		exit('You aer not authorized');
    	}
    	
    	if(Session::get("SITE_ID") && Session::get("SITE_ID") != ""){
    		$uploaddir = getcwd().'/uploads/site'.Session::get("SITE_ID").'/';
    		$short_uploaddir = '/uploads/site'.Session::get("SITE_ID").'/';
    	}else{
    		$uploaddir = getcwd().'/uploads/';
    		$short_uploaddir = '/uploads/';
    	}
    	
    	if(!file_exists($uploaddir)){
    		mkdir($uploaddir,0755);
    	}
    	
    	$allowedExts = array (
				"gif",
				"jpeg",
				"jpg",
				"png" 
		);
		
		$mime = $request->file('file')->getMimeType();
		$extension = $request->file('file')->getExtension();
		if ((($mime == "image/gif") || ($mime == "image/jpeg") || ($mime == "image/pjpeg") || ($mime == "image/x-png") || ($mime == "image/png")) && in_array ( $extension, $allowedExts )) {
			$name = sha1 ( microtime () ) . "." . $extension;
			$request->file('file')->move($uploaddir,$name);
			$response = new stdClass();
			$response->link = $short_uploaddir.$name;
			return stripslashes(json_decode($response));
		}
    }
    
    public function editorsmedia(Request $request){
    	if(!Session::get('SITE_ID') && !Session::get("EMAIL_ADDRESS") != ""){
    		exit("You are not authorized");
    	}
    	
        if (Session::get('SITE_ID') && Session::get('SITE_ID') != "") {
			$uploaddir = getcwd () . '/uploads/site' . Session::get('SITE_ID') . '/';
			$short_uploaddir = '/uploads/site' . Session::get('SITE_ID') . '/';
		} else {
			$uploaddir = getcwd () . '/uploads/';
			$short_uploaddir = '/uploads/';
		}
		
		$response = array ();
		
		// Image types.
		$image_types = array (
				"image/gif",
				"image/jpeg",
				"image/pjpeg",
				"image/jpeg",
				"image/pjpeg",
				"image/png",
				"image/x-png" 
		);
		
		// Filenames in the uploads folder.
		$fnames = scandir ( $uploaddir );
		if ($fnames) {
			// Go through all the filenames in the folder.
			foreach ( $fnames as $name ) {
				// Filename must not be a folder.
				if (! is_dir ( $name )) {
					// Check if file is an image.
					if (in_array ( mime_content_type ( $uploaddir . $name ), $image_types )) {
						// Add to the array of links.
						array_push ( $response, $short_uploaddir . $name );
					}
				}
			}
		} 		

		// Folder does not exist, respond with a JSON to throw error.
		else {
			$response = new StdClass ();
			$response->error = "Images folder does not exist!";
		}
		
		$response = json_encode ( $response );
		
		// Send response.
		return stripslashes ( $response );
    }
    
    public function Editorsimgdel(Request $request){
        if (!Session::get("SITE_ID") && ! Session::get("SITE_ID") != "")
			exit ( "You are not authorized!" );
			
			// Get src.
		 $src = $request->input('src');
		
		// Check if file exists.
		if (file_exists ( getcwd () . $src )) {
			// Delete file.
			unlink ( getcwd () . $src );
		}
    }
    
    public function GetAllLocations(Request $request){
    	$this->checkAuthentication();
    	$active_locations = $inactive_locations = 0;
    	$markup = "";
    	$item_per_page = 10;
    	if($request->has('page')){
    		$page_number = filter_var($request->input('page'),FILTER_SANITIZE_NUMBER_INT,FILTER_FLAG_STRIP_HIGH);
    		if(!is_numeric($page_number)){
    			die('Invalid page number');
    		}
    	}else{
    		$page_number =1;
    	}    	
    	
    	$page_position = (($page_number - 1)* $item_per_page);
    	$where = 'where l.remove = 0';
    	if(Session::get('USER_TYPE') == '2'){
    		$where .=' and l.owner = '.Session::get('USER_ID');
    	}elseif(Session::get('USER_TYPE') == '3'){
    		$where .=' and l.id IN ('.Session::get('LOCATION_IDS').')';
    	}
    	
    	if($request->has('status') && $request->input('status') != ''){
    		$status = $request->input('status');
    		$where .=' and l.status = '.$status;
    	}
    	
    	if($request->has('search_value') && $request->input('search_value') != ''){
    		$value = ($request->input('search_value'));
    		$where .= " AND (l.name LIKE '$value%' OR l.address LIKE '$value%' OR l.country LIKE '$value%' OR u.email_address LIKE '%$value%' OR u.full_name LIKE '$value%' )";
    	}
    	
    	$sql = "SELECT SQL_CALC_FOUND_ROWS l.id AS location_id, l.identifier, l.name AS location_title, l.location AS latlng, l.address, l.country, l.state, l.status, 
    			IFNULL((SELECT GROUP_CONCAT( CONCAT(id,'*',name) SEPARATOR '|' ) FROM device WHERE location_id = l.id), '') AS devices 
		    	FROM location AS l
    			LEFT JOIN user AS u ON l.owner = u.id 
				$where ORDER BY l.id DESC LIMIT $page_position, $item_per_page";
		$location_query = DB::select(DB::raw($sql));
		
		$total_found_rows = count($location_query);
		$total_pages = ceil($total_found_rows / $item_per_page);
		
		if($location_query){
			$markup .='<div class="row">';
			foreach ($location_query as $data){
				$checkbox_status = '';
				if($data->status == 1){
					$active_locations +=1;
					$checkbox_status = 'checked ="checked"';
				}else{
					$inactive_locations +=1;
				}
				
				$device_tags  = "<label class='badge badge-danger'>No device assigned</label>";
				
				if($data->devices){
					$device_tags = '';
					$devices = explode('|',$data->devices);
					foreach ($devices as $device){
						$device_info  = explode('*',$device);
						$device_tags .="<label class='badge badge-info assigned-bg'>" . $device_info [1] . "</label>";
					}
				}
				
				$location_edit_link = url('location/overview?loca='.$data->location_id);
				$markup .= "<div class='col-sm-6 col-md-6' id='loca_on_" . $data->location_id . "'>
    										<div class='panel panel-primary'>
    											<div class='panel-body no-padding'>
    												<div class='row'>
    													<div class='col-md-5 location-des'>
															<address>
																<h4>" . $data->location_title . "</h4><br>
																" . $data->address . "<br>
																Country: " . $data->country . "<br>
																State: " . $data->state . "<br>
															</address>    														
															<p class='caps-style' style='padding: 0 5px;'>Assigned Campaign:<br>" . LocationSchedule::getActiveCampaignId($data->identifier, 'campaign_name' ) . "</p>
															<p class='caps-style' style='padding: 0 5px;'>Assigned Devices:<br>" . $device_tags . "</p>
															<div class='row' style='position: absolute; bottom: 10px; padding-left: 5px;'>
																<div class='col-xs-6' style='margin-top: 9px'>
																	<div class='make-switch' onclick=\"javascript:changeStatus($data->location_id)\">
																		<input class='boots-switch' data-size='mini' data-on-color='success' type='checkbox' " . $checkbox_status . " disabled id='add_time_4' />
																	</div>	
																</div>
																<div class='col-xs-6'>
																	<div class='row'>
																		<div class='col-xs-6'>
																			<a data-toggle='tooltip' data-placement='top' data-original-title='Edit this location' href='" . $location_edit_link . "' class='btn btn-blue btn-sm'><i class='fa fa-pencil'></i></a>
																		</div>
																		<div class='col-xs-6' style='padding-left: 1px;'>
																			<a id='" . $data->location_id . "' href='javascript:removeLocation(" . $data->location_id . ");' data-toggle='tooltip' data-placement='top' data-original-title='Remove this location' class='btn btn-red btn-sm'><i class='fa fa-trash-o'></i></a>
																		</div>
																	</div>
																</div>
															</div>
    													</div>
    													<div class='col-md-7 figre text-right'>
															<img src='https://maps.googleapis.com/maps/api/staticmap?center=" . $data->latlng . "&zoom=10&size=300x250&scale=2&markers=size:mid%7Ccolor:red%7C" . $data->latlng . "' class='img-responsive'>
														</div>
    												</div>
    											</div>
    										</div>
    									</div>";
			}
			$markup .= "</div>";
			}else{
				$markup = "<div class='row'><div class='col-md-12'>
						<div class='alert alert-danger'>No Location has been found!</div>
					</div></div>";
		}
		
		$where = " where remove = 0";
		if(Session::get('USER_TYPE') == '3'){
			$where .= " and id IN( ".Session::get('LOCATION_IDS').")";			
		}elseif(Session::get('USER_TYPE') == '2'){
			$where .=" and owner = ".Session::get('USER_ID');
		}
		
		$sql = "select status from location $where";
		$results = DB::select(DB::raw($sql));
		$active = $inactive = 0;
		if($results){
			foreach ($results as $row){
				if($row->status == 1){
					$active +=1;
				}else{
					$inactive +=1;
				}
			}
		}
		
		$markup .= "<div class='row'><div class='col-md-12'>" . $this->paginate_function ( $page_position, $item_per_page, $page_number, $total_found_rows, $total_pages ) . "</div></div>";
		
		$location_count = array(
			'all_locations' => $active + $inactive,
		    'active_locations'=>$active,
		    'inactive_locations'=>$inactive
		);
		
		$output =array(
		    'markup' => $markup,
		    'location_count'=>$location_count
		);
		
		return json_encode($output);
    }
    
    public function GetAllDevices(Request $request){
    	$this->checkAuthentication();
    	
    	$device_list = get_device_info();
    	$markup = '';
    	$item_per_page = 12;
    	
    	if($request->has('page')){
    		$page_number = filter_var($request->input('page'),FILTER_SANITIZE_NUMBER_INT,FILTER_FLAG_STRIP_HIGH);
    		if(!is_numeric($page_number)){
    			die('Invalid page number');
    		}
    	}else{
    		$page_number =1;
    	}
    	
    	$page_position = (($page_number - 1)*$item_per_page);
    	$where = ' ';
    	if(Session::get('USER_TYPE') == '1'){
    		$where = ' where 1= 1';
    	}elseif(Session::get('USER_TYPE') == '2'){
    		$where = ' where d.owner = '.Session::get('USER_ID');
    	}elseif(Session::get('USER_TYPE') == '3'){
    		$where  = ' where d.owner = '.Session::get('USER_CREATED_BY');
    	}
    	
    	if($request->has('status') && $request->input('status') != ''){
    		$status = base64_decode($request->input('status'));    		
    		$where .=' and d.id IN('.$status.')';
    	}
    	
    	if($request->has('search_value') && $request->input('search_value') != ''){
    		$value = mysql_real_escape_string($request->input('search_value'));
    		$where .=" and (d.name LIKE '$value%' OR d.mac_address LIKE '$value%' OR d.model LIKE '$value%' OR u.email_address LIKE '%$value%' OR u.full_name LIKE '$value%') ";
    	}
    	
    	$sql = "SELECT SQL_CALC_FOUND_ROWS d.id AS device_id, d.location_id, d.name AS device_title, l.name AS location_title,
    			l.identifier, d.mac_address, d.model, d.status FROM device AS d
		    	LEFT JOIN location AS l ON d.location_id = l.id
    			LEFT JOIN user AS u ON d.owner = u.id 
				$where ORDER BY d.id DESC LIMIT $page_position, $item_per_page";
		$device_query = DB::select(DB::raw($sql));
		$total_found_rows = count($device_query);
		$total_pages = ceil($total_found_rows/ $item_per_page);
		
		if($device_query){
			$markup .='<div class="row">';
			foreach($device_query as $data){
				$class = "";
				$inactive_text = "";
				$checkbox_status = "";
				$campaign_name = "<label class='badge badge-danger'>No campaign assigned</label>";
				$location_name = "<label class='badge badge-danger'>No location assigned</label>";
				$device_status = DeviceStatus::getDeviceStatus($data->mac_address);
				$ssid = $device_status['ssid'];
				
				if($data->identifier != '' && $data->location_title != ''){
					$location_name = "<a href='" . url ( 'location/overview?loca=' ) . $data->location_id . "' data-toggle='tooltip' data-placement='top' data-original-title='$data->location_title' class='badge badge-info'>" . $data->location_title . "</a>";
					$campaign_name = LocationSchedule::getActiveCampaignId($data->identifier, 'campaign_name');
					$camp_loca_info = "<p>Campaign: ".$campaign_name."</p><p>Location: ".$location_name."</p>";					
				}else{
					$camp_loca_info = "<p>Campaign: " . $campaign_name . "</p><p>Location: " . $location_name . "</p>";
				}
				
				if($data->status == 1){
					$class = $device_status['status_class'];
					$checkbox_status = "checked ='checked'";
					$onclick = "javascript:changeDeviceStatus(" . $data->device_id . ", 'off')";
				}else{
					$class = "danger";
					$inactive_status = "checked='checked'";
					$onclick =  "javascript:changeDeviceStatus(" . $data->device_id . ", 'on')";
				}
				
				$markup .= "<div class='col-sm-6 col-lg-4 device-content " . $device_status ['status_text'] . "' id='device" . $data->device_id . "'>
    							<div class='panel panel-" . $class . "'>
    								<div class='panel-heading'>
			                            <div class='panel-title'>
			                                <h4 id='device_title_" . $data->device_id . "'><strong data-toggle='tooltip' data-placement='top' data-original-title='$data->device_title'>" . $data->device_title . "</strong>" . $device_status ['status_mode'] . "</h4>
			                            </div>
                                		" . $inactive_text . "
		                            </div>
		                            <div class='panel-body'>
		                            	<div class='row'>
		                            		<div class='col-md-12'>
		                            			<p style='font-size:20px; line-height: 22px;'><i style='font-size:30px; color:#000;' class='entypo-signal'></i><span data-toggle='tooltip' data-placement='top' data-original-title='$ssid'>" . trimSentence ( $ssid, 19 ) . "</p>
		                            		</div>
		                            	</div>
			                        	<div class='row'>
			                            	<div class='col-xs-8'>
                                            	<p>Model: " . $device_list [$data->model] ['title'] . "</p>
                                            	" . $camp_loca_info . "
                                        	</div>
                                        	<div class='col-xs-4'>
                                            	<img style='width: 100%;' src='" . $device_list [$data->model] ['image'] . "' />
				                                            	</div>
				                                            	</div>
				                                            	<div class='row'>
		                                            			<div class='col-xs-8' style='margin-top: 10px;'>
																	<div class='make-switch' onclick=\"" . $onclick . "\">
																		<input class='boots-switch' data-size='mini' data-on-color='success' type='checkbox' " . $checkbox_status . " disabled id='add_time_4' />
																	</div>	
																</div>
				
				                                            	<div class='col-xs-4'>
				                                            	<div class='text-right'>
				                                            	<a data-toggle='tooltip' data-placement='top' data-original-title='Edit this device' href='javascript:editDevice(" . $data->device_id . ");' class='btn btn-blue btn-sm'><i class='fa fa-pencil'></i></a>
                                                				<!--<a id='" . $data->device_id . "' title='Delete this device' class='btn btn-red btn-sm delete_device'><i class='entypo-trash'></i></a>-->
                                            </div>
                                        </div>
                                    </div>
                                    </div>
    							</div>
				    						</div>";
			}
			$markup .="</div>";
		}else{
			$markup = "<div class='row'><div class='col-md-12'>
						<div class='alert alert-danger'>No Device has been found!</div>
					</div></div>";
		}
		
		$where =" ";
		if(Session::get('USER_TYPE') == '3'){
			$where .="where owner = ".Session::Get('USER_CREATED_BY');
		}else if(Session::get('USER_TYPE') == '2'){
			$where .="where owner = ".Session::get('USER_ID');
		}
		
		$results = DB::select(DB::raw("SELECT id, mac_address, status FROM device $where"));
		$active = $inactive = $online = $offline = $never_connected = 0;
		$active_ids = $inactive_ids = $online_ids = $offline_ids = $never_connected_ids = 0;
		if($results){
			foreach ($results as $row){
				$device_status = DeviceStatus::getDeviceStatus($row->mac_address);
				
				if($device_status['status_text'] == 'online'){
					$online +=1;
					$online_ids .=$row->id.',';
				}else if($device_status['status_text'] == 'offline'){
					$offline +=1;
					$offline_ids .=$row->id.',';
				}else{
					$never_connected  +=1;
					$never_connected_ids .=$row->id.',';
				}
				
				if($row->status == 1){
					$active +=1;
					$active_ids .=$row->id.',';
				}else{
					$inactive +=1;
					$inactive_ids .=$row->id.',';
				}
			}
		}
		
		$markup .= "<div class='row'><div class='col-md-12'>" . $this->paginate_function ( $page_position, $item_per_page, $page_number, $total_found_rows, $total_pages ) . '</div></div>';
		
		$device_count = array(
			'all' => $active + $inactive,
		    'active'=>$active,
		    'inactive'=>$inactive
		);
		
		$device_status_count = array(
			'online'=>$online,
		    'offline'=>$offline,
		    'never_connected'=>$never_connected
		);
		
		$device_ids = array(
			'active_ids'=>base64_encode(rtrim($active_ids,',')),
		    'inactive_ids'=>base64_encode(rtrim($inactive_ids,',')),
			'online_ids'=>base64_encode(rtrim($online_ids,',')),
			'offline_ids'=>base64_encode(rtrim($offline_ids,',')),
			'never_connected_ids'=>base64_encode(rtrim($never_connected_ids,','))
		);
		
		$output = array(
			'markup'=>$markup,
		    'device_count'=>$device_count,
			'device_status_count'=>$device_status_count,
			'device_ids'=>$device_ids
		
		);
		
		return json_encode($output);
    }
    
    public function GenerateOverview(Request $request){
    	$this->checkAuthentication();
    	
		$data = array ();
		$recordsTotal = 0;
		$columns = array (
				array (
						'db' => 'full_name',
						'dt' => 0 
				),
				array (
						'db' => 'email_address',
						'dt' => 1 
				),
				array (
						'db' => 'sub_users',
						'dt' => 2 
				),
				array (
						'db' => 'allocated_devices',
						'dt' => 3 
				),
				array (
						'db' => 'active_devices',
						'dt' => 4 
				),
				array (
						'db' => 'status',
						'dt' => 5 
				),
				array (
						'db' => 'created_at',
						'dt' => 6 
				),
				array (
						'db' => 'actions',
						'dt' => 7 
				) 
		);
		
		$bindings = array ();
		
		$limit = '';
		if($request->has('start') && $request->input('length') != -1){
			$limit = "limit ".intval($request->input('start')).", ".intval($request->input('length'));
		}
		
		$where = "where u.user_type_id = 2 and u.remove = 0 ";
		if($request->has('search')){
			$str = $request->input('search');
			$str = $str['value'];
			if($str != ''){
				$where .=" and u.full_name LIKE '%".$str."%' or u.email_address like '%".$str."%' or u.created_at like '%".$str."%'";
			}
		}
		
		$order = "order by u.full_name desc";
		if($request->has('order') && count($request->input('order'))){
			$input = $request->input('order');
			$columnIdx = intval($input[0]['column']);
			if($columnIdx == 0){
				$order = "ORDER BY u.full_name " . strtoupper ( $input[0]['dir'] );
			}elseif ($columnIdx == 1) {
				$order = "ORDER BY u.email_address " . strtoupper ( $input[0]['dir'] );
			} elseif ($columnIdx == 6) {
				$order = "ORDER BY u.created_at " . strtoupper ( $input[0]['dir'] );
			}
		}
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS u.id, u.full_name, u.email_address, u.created_at, 
				(SELECT COUNT(id) FROM user WHERE user_type_id = 3 AND created_by = u.id) AS sub_users, 
				IFNULL(sd.allowed_quantity, 0) AS allocated_devices,  COUNT(distinct d.id) AS active_devices, 
				(SELECT COUNT(id) FROM `device` WHERE status = 1 AND owner = u.id) AS active_devices, 
				u.is_active AS status FROM `user` AS u LEFT JOIN device AS d ON d.owner = u.id 
				LEFT JOIN `subscription_detail` AS sd ON sd.user_id = u.id $where GROUP BY u.id $order $limit ";
		
		$result = DB::select(DB::raw($sql));
		if($result){
			$data = $result;
		}
		
		$recordsFiltered = Device::count();
		
		$recordsTotal = User::where('user_type_id',2)->where('remove',0)->count();
		$out = array ();
		$data_len = count ( $data );
		$col_len = count ( $columns );
    for($i = 0; $i < $data_len; $i ++) {
			$row = array ();
			for($j = 0; $j < $col_len; $j ++) {
				$identifier = $columns [$j] ['db'];
				$user_id = $data [$i]->id;
				switch ($identifier) {
					case 'full_name' :
						$row [] = $data [$i]->full_name;
						break;
					case 'email_address' :
						$row [] = $data [$i]->email_address;
						break;
					case 'sub_users' :
						$row [] = '<label class="label label-info"><strong>' . $data [$i]->sub_users . '</strong></label>';
						break;
					case 'allocated_devices' :
						$row [] = '<label class="label label-info"><strong>' . $data [$i]->allocated_devices . '</strong></label>';
						break;
					case 'active_devices' :
						$row [] = '<label class="label label-info"><strong>' . $data [$i]->active_devices. '</strong></label>';
						break;
					case 'status' :
						$button = "";
						if ($data [$i]->status == '1') {
							$button = "<button id='btn_user_status_$user_id' type='button' class='btn btn-success btn-sm' onclick='javascript:UpdateUserStatus(" . $data [$i]->id . "," . $data [$i]->status . ");'><i class='fa fa-check-circle'></i></button>";
						} else {
							$button = "<button id='btn_user_status_$user_id' type='button' class='btn btn-danger btn-sm' onclick='javascript:UpdateUserStatus(" . $data [$i]->id . "," . $data [$i]->status . ");'><i class='fa fa-ban'></i></button>";
						}
						$row [] = $button;
						break;
					case 'created_at' :
						$row [] = date ( 'd M, Y', strtotime ( $data [$i]->created_at) );
						break;
					case 'actions' :
						$actions = '<button type="button" class="btn btn-info btn-sm" onclick="javascript:GetPlatformUserProfile(' . $user_id . ')" title="Edit this user."><i class="fa fa-pencil"></i></button> ' . '<a href="javascript:DeleteAlert(' . $user_id . ');" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a> ' . '<img id="al_' . $user_id . '" src="' . url('themes/neon/assets/images/ajax-loader.gif') . '" class="hide"/>';
						$row [] = $actions;
						break;
				}
			}
			$out [] = $row;
		}
		
		$ouptut = array (
				"draw" => intval ( $request->input('draw')),
				"recordsTotal" => intval ( $recordsTotal ),
				"recordsFiltered" => intval ( $recordsFiltered ),
				"data" => $out 
		);
		
		return json_encode($ouptut);
    }
    
    public function Logoutuseraftersessiontimeout(Request $request){    	
    	$current_server_time = base64_decode($request->input('current_server_time'));
    	$logout_time = $current_server_time + Option::getOption('logoff_time','0');
    	if((time()) >= $logout_time){    		
    		Session::put('REDIRECT_URL',$request->input('rqu'));
    		Session::forget('EMAIL');
    		Session::forget('NAME');
    		Session::forget('PHOTO');
    		Session::forget('LOGOUT_BY');
    		return url('user/lockscreen');
    	}    	
    	return '';
    }
    
    public function SetAsDefaultCampaign(Request $request){
    	$this->checkAuthentication();
    	$location_id = $request->input('location_id');
    	$campaign_id = $request->input('campaign_id');
    	$location_schedule_id = LocationSchedule::IsDefaultSchedule($location_id);
    	
    	$owner = Session::get('USER_ID');
    	if(Session::get('USER_TYPE') == '3'){
    		$owner = Session::get('USER_CREATED_BY');
    	}
    	
    	if($location_schedule_id > 0){
    		$num = LocationSchedule::where('id',$location_schedule_id)->update(array('campaign_id'=>$campaign_id,'update_at'=>date('Y-m-d H:i:s'),'owner'=>$owner));
    	}else{
    		$record = new LocationSchedule;
    		$record->location_id = $location_id;
    		$record->campaign_id = $campaign_id;
    		$record->is_default = 1;
    		$record->owner = $owner;
    		if($record->save()){
    			$num = 1;
    		}else{
    			$num = 0;
    		}
    	}
    	
    	if($num > 0){
    		return 1;
    	}else{
    		return 0;
    	}
    }
    
    public function GetScheduleById(Request $request){
    	$this->checkAuthentication();
    	
    	$loca_schedule = LocationSchedule::find($request->input('id'));
    	$start_datetime= $loca_schedule->start_date;
    	$end_datetime= $loca_schedule->end_date;
    	$start_date = $end_date = $start_time = $end_time = '';
    	if($start_datetime != ''){
    		$temp = explode(' ',$start_datetime);
    		$start_date = $temp[0];
    		$start_time  = date("h:i A",strtotime($temp[1]));
    	}
    	
    	
    	if($end_datetime != ''){
    		$temp = explode(' ',$end_datetime);
            $end_date  = $temp[0];
            $end_time  = date("h:i A",strtotime($temp[1]));
    	}
    	
    	$days_array = array();
    	
    	if($loca_schedule->repeat_type == 'weekly'){
    		$days = explode(',',$loca_schedule->repeat_data);
    		foreach ($days as $val){
    		    if (key_exists ( $val, $week_array )) {
					$temp_array = array (
							'id' => $val,
							'text' => $week_array [$val] 
					);
					$days_array [] = $temp_array;
				}
    		}
    	}
    	
    	$data = array (
				'id' => $loca_schedule->id,
				'campaign_id' => $loca_schedule->campaign_id,
				'location_id' => $loca_schedule->location_id,
				'repeat_type' => $loca_schedule->repeat_type,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'start_time' => $start_time,
				'end_time' => $end_time,
				'repeat_data' => $loca_schedule->repeat_data,
				'repeat_until' => date ( 'Y-m-d', strtotime ( $loca_schedule->repeat_until)),
				'days_array' => json_encode ( $days_array ) 
		);
		
		
		return json_encode($data);
    }
    
    public function DeleteSchedule(Request $request){
    	$this->checkAuthentication();
    	$id = $request->input('id');
    	$status = LocationSchedule::find($id)->delete();
    	if($status){    		
			$message = GenerateConfirmationMessage('success','<i class="entypo-info-circled"></i> Schedule has been successfully removed.');
    	}else{    		
			$message = GenerateConfirmationMessage('danger','<i class="entypo-cancel-circled"></i> Schedule has not been successfully removed.');
    	}
    	Session::put('SESSION_MESSAGE',$message);
    	Session::put('TAB','campaigns');
    	return;
    }
    
    public function AssignDevice(Request $request){
    	$this->checkAuthentication();
    	$output = array();
    	if($request->has('device_list')  && is_array($request->input('device_list'))){
    		$location_id = $request->input('location_id');
    		$i = 0;
    		foreach($request->input('device_list') as $device_id){
    			$num = Device::find($device_id)->update(array('location_id'=>$location_id));
    			if($num > 0){
    				$i++;
    			}
    		}
    		
    		if($i > 0){
    			$output['success'] = $i." device(s) has successfully added. Redirecting..";
    		}else{
    			$output ['error'] = "Device has not successfully added";
    		}
    	}else{
    		$output ['error'] = "No device selected!";
    	}
    	
    	Session::put('TAB','hardware');
    	return json_encode($output);
    }
    
    public function RemovingDevice(Request $request){
    	$this->checkAuthentication();
    	$param = $request->input('param');
    	$value = $request->input('value');
    	if($param == 'location_id'){
    		$nums = Device::where('location_id',$value)->update(array('location_id'=>0,'update_date'=>date('Y-m-d h:m:s')));    		
    	}else{
    		$nums = Device::where('id',$value)->update(array('location_id'=>0,'update_date'=>date('Y-m-d h:m:s')));
    	}
    	
    	Session::put('TAB','hardware');
    	return $nums;
    }
    
    public function EditDevice(Request $request){
    	$this->checkAuthentication();
    	$id = $request->input('id');
    	$sql = "SELECT d.id, d.location_id, d.name, d.mac_address, d.model, d.update_date, d.status, l.identifier, 
		ds.status_created_on, ds.device_status_details, NOW() AS current_datetime, 
		IF( LENGTH( u.time_zone ) >0, u.time_zone,  @@system_time_zone ) AS timezone, 
		@@system_time_zone AS server_timezone FROM device AS d 
		LEFT JOIN `device_status` AS ds ON d.id = ds.device_id 
		LEFT JOIN location AS l ON d.location_id = l.id 
		LEFT JOIN user AS u ON d.owner = u.id 
		WHERE d.id = $id LIMIT 1";
    	
    	$query = DB::select(DB::raw($sql));
    	if($query){
    		$data = $query[0];
    		$last_contact = "Unknown";
			$device_status = json_decode ( $data->device_status_details );
			$ssid = isset ( $device_status->ssid ) && $device_status->ssid != "" ? $device_status->ssid : "None";
			$os_date = isset ( $device_status->os_date ) && $device_status->os_date != "" ? $device_status->os_date : "Unknown";
			$wan = isset ( $device_status->wan ) && $device_status->wan != "" ? $device_status->wan : "Unknown";
			$lan = isset ( $device_status->lan ) && $device_status->lan != "" ? $device_status->lan : "Unknown";
			if ($data->status_created_on != "") {
				$last_contact = convertTimeBasedOnTimezone ( ini_get('date.timezone'), $data->timezone, $data->status_created_on, 'd M, Y @ h:i A (P)' );
			}
			$firmwares = "";
			$router_model = "";
			$router_image = "";
			$vendor = "";
			$device_info = get_device_info ();
			
			$router_model = $device_info [$data->model] ['title'];
			$router_image = $device_info [$data->model] ['image'];
			$vendor = $device_info [$data->model] ['manufacturer'];
			$firmware_array = $device_info [$data->model] ['firmwares'];
			if (is_array ( $firmware_array )) {
				$firmwares .= "<div class='row text-center'>";
				$firmwares .= "<div class='col-md-6'><a class='btn btn-lg btn-red btn-block' href='$firmware_array[0]' title='Download MyWiFi Firmware' target='_blank'><i class='fa fa-cloud-download'></i> Factory to MyWiFi Firmware</a></div>";
				$firmwares .= "<div class='col-md-6'><a class='btn btn-lg btn-red btn-block' href='$firmware_array[1]' title='Download MyWiFi Webflash' target='_blank'><i class='fa fa-cloud-download'></i> DD-WRT to MyWiFi Webflash</a></div>";
				$firmwares .= "</div>";
			}
			
			$device_data = array (
					'id' => $data->id,
					'name' => $data->name,
					'location_id' => $data->location_id,
					'nasid' => $data->identifier,
					'mac_address' => $data->mac_address,
					'internal_ip' => '', // $data->internal_ip,
					'external_ip' => '', // $data->external_ip,
					'update_date' => $data->update_date,
					'model' => $router_model,
					'image' => $router_image,
					'firmwares' => $firmwares,
					'vendor' => $vendor,
					'status' => $data->status,
					'campaign_name' => $data->location_id > 0 ? LocationSchedule::getActiveCampaignId ( $data->identifier, 'campaign_name' ) : "None",
					'last_contact' => $last_contact,
					'ssid' => $ssid,
					'os_date' => $os_date,
					'wan' => $wan,
					'lan' => $lan 
			);
			
			return '{"js_arr":' . json_encode ( $device_data ) . '}';
    	}   	
    	
    }
    
    public function SaveSubuser(Request $request){
    	$this->checkAuthentication();
    	$output = array();
    	$flag =  true;
    	$validation_msg ="";
    	if(!$request->has('first_name') || empty($request->input('first_name'))){
    		$flag = false;
    		$validation_msg .="First Name is required.</br>";
    	}
    	
       if (!$request->has('phone') || empty ($request->input('phone') )) {
			$flag = false;
			$validation_msg .= "Phone Number is required.</br>";
		}
		
		if ($request->has('hdn_action') && $request->input('hdn_action') == "add") {
			if (!$request->has('email') || empty ($request->input('email'))){
				$flag = false;
				$validation_msg .= "Email is required.</br>";
			}else{
				$exist = User::RetriveByEmailAddress($request->input('email'));
				if ($exist != null) {
					$flag = false;
					$validation_msg .= "This email is already in use. Please try a different email address.</br>";
				}
			}
			
			if (!$request->has('password') || empty ( $request->input('password'))) {
				$flag = false;
				$validation_msg .= "Password is required.</br>";
			}
			if (!$request->has('confirm_password') || empty ($request->input('confirm_password'))) {
				$flag = false;
				$validation_msg .= "Confirm Password is required.</br>";
			}
			if (! empty ( $request->input('password')) && ! empty ( $request->input('confirm_password'))) {
				if ($_POST ['password'] != $_POST ['confirm_password']) {
					$flag = false;
					$validation_msg .= "Password did not match.</br>";
				}
			}
		} elseif ($request->has('hdn_action') && $request->input('hdn_action') == "edit") {
			if ($_POST ['password'] != "" || $_POST ['confirm_password'] != "") {
				if ($_POST ['password'] != $_POST ['confirm_password']) {
					$flag = false;
					$validation_msg .= "Password did not match.</br>";
				}
			}
		}
		
		if (!$request->has('user_permission') || ! is_array ( $request->input('user_permission'))) {
			$flag = false;
			$validation_msg .= "You did not select any module.</br>";
		} else {
			if (in_array ( 'location', $request->input('user_permission'))) {
				if (!$request->has('location')) {
					$flag = false;
					$validation_msg .= "You did not select any location.</br>";
				}
			}
			if (in_array ( 'campaign', $request->input('user_permission'))) {
				if (!$request->has('campaign')) {
					$flag = false;
					$validation_msg .= "You did not select any Campaign.</br>";
				}
			}
		}
		
		if($flag == true){
			$user = new User;
			$user->first_name = $request->input('first_name');
			$user->last_name = $request->input('last_name');
			$user->mobile_phone = $request->input('phone');
			$user->full_name = $request->input('first_name')." ".$request->input('last_name');
			$module_ids = implode(',', $request->input('user_permission'));
			$location_ids = '';
			if($request->has('location') && $request->input('location') != ''){
				$temp_lids = implode(',',$request->input('location'));
				$location_ids = empty($temp_lids)?"":$temp_lids;
			}
			
			$campaign_ids = "";
			if($request->has('campaign') && $request->input('campaign') != ''){
				$temp_lids = implode(',',$request->input('campaign'));
				$campaign_ids = empty($temp_lids) ? "":$temp_lids;
			}
			
			if($request->has('hdn_action') && $request->input('hdn_action') == 'add'){
				$token = User::guid();				
				$user->email_address = $request->input('email');
				$user->password = md5($request->input('password'));
				$user->is_active = true;
				$user->token = $token;
				$user->created_by = Session::get('USER_ID');
				$user->user_type_id = 3;
				$user->created_at = date('Y-m-d H:i:s',time());
				$user->site_id = Session::get('SITE_ID');
				$user->save();
				if($user->id <= 0){
					$output['style'] = 'danger';
					$output['message'] = "Can not save";
				}else{
					$module_ids .=',user,dashboard';
					$record = new UserPermission;
					$record->user_id = $user->id;
					$record->module_ids = $module_ids;
					$record->location_ids = $location_ids;
					$record->campaign_ids = $campaign_ids;
					$record->save();
					$output ['style'] = "success";
					$output ['message'] = "User has been successfully created. redirecting...";
				}
			}else{	
				if($request->has('confirm_password') && $request->input('confirm_password') != ""){
					$affectdRows = User::where('id',$request->input('hdn_sub_user_id'))->update(array(
						'first_name'=>$request->input('first_name'),
					    'last_name'=>$request->input('last_name'),
					    'full_name'=>$request->input('first_name')." ".$request->input('last_name'),
					    'mobile_phone'=>$request->input('phone'),
						'password'=>md5($request->input('confirm_password'))
					));
				}else{			
					$affectdRows = User::where('id',$request->input('hdn_sub_user_id'))->update(array(
						'first_name'=>$request->input('first_name'),
					    'last_name'=>$request->input('last_name'),
					    'full_name'=>$request->input('first_name')." ".$request->input('last_name'),
					    'mobile_phone'=>$request->input('phone')
					));
				}
				if($affectdRows > 0){
					$module_ids .=",user,dashboard";
					$record = UserPermission::where('user_id',$request->input('hdn_sub_user_id'))->first();
					$record->module_ids = $module_ids;
					$record->location_ids = $location_ids;
					$record->campaign_ids = $campaign_ids;
					$record->save();
					
					$output['style'] = "success";
					$output['message'] = "User has been successfully updated. redirecting..";
				}else{
					$output['style'] = "danger";
					$output['message'] = "Can not save.";
				}
			}
		}else{
			$output['style']  = "warning";
			$output['message'] = $validation_msg;
		}
		
		return json_encode($output);
    }
    
    public function CheckEmailExist(Request $request){
    	$user_id = $request->has('uid')? base64_decode($request->input('uid')):'';
    	$email = $request->input('email');
    	if($email != ""){
    		$result = User::where('email_address',$email)->select('id');
    		if($user_id != ""){
    			$result = $result->where('id','!=',$user_id);
    		}
    		$result = $result->first();
    		if($result){
    			return "1";
    		}else{
    			return "0";
    		}
    	}else{
    		return "0";
    	}
    }
    
    public function ChangeStatusDevice(Request $request){
    	$this->checkAuthentication();
    	$result = 0;
    	$id = $request->input('id');
    	$device = Device::find($id);
    	if($device){
    		if($device->status == 1){
    			$device->status =  0;
    			$device->location_id= 0;
    		}else{
    			$device->status = 1;
    		}
    		
    		$device->id = $id;
    		if($device->save()){
    		    $result = 1;
    		}
    	}
    	
    	return $result;
    }
    
    public function CloneCampaign(Request $request){
    	$this->checkAuthentication();
    	$output = "false";
    	$old_campaign_id =$request->input('id');
    	$old_record = Campaign::where('id',$old_campaign_id)->first();
    	$new_record = $old_record->replicate();    	
    	$new_record->name = $new_record->name ."(copy)";
    	$new_record->save();
    	if($new_record->id > 0){
    		$output = "true";
    		$results = AppCampaignDetails::select('app_info_id','app_type')->where('campaign_id',$old_campaign_id)->get();
    		if(count($results) > 0){
    			foreach($results as $row){
    				$newrecord = new AppCampaignDetails;
    				$newrecord->campaign_id = $new_record->id;
    				$newrecord->app_info_id = $row->app_info_id;
    				$newrecord->app_type = $row->app_type;
    				$newrecord->save();
    			}
    		}
    		
    		$campaign_meta = CampaignMeta::getCampaignMeta($old_campaign_id);
    		if(count($campaign_meta) > 0){
    			foreach($campaign_meta as $meta_key => $meta_value){
    				CampaignMeta::addCampaignMeta($new_record->id, $meta_key, $meta_value);
    			}
    		}
    	}
    	
    	return $output;
    }
    
    public function GetSocialUsers(Request $request){
    	$this->checkAuthentication();
    	$data = array();
    	$recordsTotal = 0;
    	$columns  = array(
    		    array (
						'db' => 'social_network',
						'dt' => 0 
				),
				array (
						'db' => 'picture_url',
						'dt' => 1 
				),
				array (
						'db' => 'full_name',
						'dt' => 2 
				),
				array (
						'db' => 'email',
						'dt' => 3 
				),
				array (
						'db' => 'gender',
						'dt' => 4 
				),
				array (
						'db' => 'campaign',
						'dt' => 5 
				),
				array (
						'db' => 'location',
						'dt' => 6 
				),
				array (
						'db' => 'return',
						'dt' => 7 
				),
				array (
						'db' => 'os_name',
						'dt' => 8 
				),
				array (
						'db' => 'device',
						'dt' => 9 
				),
				array (
						'db' => 'added_datetime',
						'dt' => 10 
				) 
    	);
    	
    	$bindings = array();
    	$limit = '';
    	if($request->has('start') && $request->input('length') != -1){
    		$limit = "limit ".intval($request->input('start')).", ".intval($request->input('length'));
    	}
    	
    	$location_ids = 0;
    	$WHERE = " where ";
    	$sql_data_set_length = "SELECT COUNT(`social_user_id`) AS recordsTotal FROM `social_user` ";
    	switch(Session::get('USER_TYPE')){
    		case '1' :
				$WHERE .= "1=1 ";
				break;
			case '2' :
				$sql = "SELECT GROUP_CONCAT( id ) AS location_ids FROM location WHERE `remove` = 0 AND owner = " . Session::get('USER_ID');
				
				$query = DB::select(DB::raw($sql));
				if (count ( $query ) > 0) {
					$result = $query[0];
					$location_ids = $result->location_ids;
				}
				$WHERE .= "su.location_id IN($location_ids) ";
				$sql_data_set_length .= "WHERE location_id IN(".$location_ids.") ";
				break;
			case '3' :
				$location_ids = Session::get ('LOCATION_IDS') != null ? Session::get ('LOCATION_IDS') : 0;
				$WHERE .= "su.location_id IN($location_ids) ";
				$sql_data_set_length .= "WHERE location_id IN(".$location_ids.") ";
				break;
    	}
    	
    	if($request->has('search') && $request->input('search')['value'] != ''){
    		$str = $request->input('search')['value'];
    		$WHERE .= " AND su.full_name LIKE '%" . $str . "%' OR su.email LIKE '%" . $str . "%' OR l.name LIKE '%" . $str . "%' ";
    	}
    	
    	$order = "ORDER BY su.added_datetime DESC";
    	if($request->has('order') && count($request->input('order'))){
    		$columnIdx = intval($request->input('order')[0]['column']);
    		switch($columnIdx){
    			case 1 :
					$order = "ORDER BY su.full_name " . strtoupper ( $request->input('order')[0] ['dir'] );
					break;
				case 2 :
					$order = "ORDER BY su.email " . strtoupper ( $request->input('order') [0] ['dir'] );
					break;
				case 4 :
					$order = "ORDER BY c.name " . strtoupper ( $request->input('order') [0] ['dir'] );
					break;
				case 5 :
					$order = "ORDER BY l.name " . strtoupper ( $request->input('order') [0] ['dir'] );
					break;
				case 10 :
					$order = "ORDER BY su.added_datetime " . strtoupper ( $request->input('order') [0] ['dir'] );
					break;
    		}
    	}
    	
    	$sql = "SELECT SQL_CALC_FOUND_ROWS su.social_user_id as id, su.picture_url, IF(LENGTH (su.full_name) > 0, su.full_name, 'No Name') AS full_name, IF(LENGTH (su.email) > 0, su.email, 'n/a') AS email, su.gender, c.name AS campaign, su.campaign_id, l.name AS location, su.location_id, su.social_network, su.`return`, su.added_datetime, u.time_zone AS owner_timezone, l.time_zone AS location_timezone, @@system_time_zone AS server_timezone FROM social_user AS su LEFT JOIN location AS l ON su.location_id = l.id LEFT JOIN campaign AS c ON su.campaign_id = c.id LEFT JOIN user AS u ON u.id = l.owner $WHERE $order $limit ";
    	$result = DB::select(DB::raw($sql));
    	
    	$recordsFiltered = count($result);
    	
    	if(count($result) > 0){
    		foreach($result as $row){
    			$sql = "SELECT `os_name`, IF(LENGTH (`model`) > 0, CONCAT(`device`,' (',`model`,')'), `device`) AS device FROM `social_user_device_info` WHERE `suid` = " . $row->id . " ORDER BY `created_at` DESC LIMIT 1";
    			$sq_query = DB::select(DB::raw($sql));
    			if(count($sq_query) > 0){
    				$device_data = $sq_query[0];
    			}else{
    				$device_data = new stdClass();
    				$device_data->os_name = '';
    				$device_data->device = '';
    			}
    			$row->os_name = $device_data->os_name;
    			$row->device = $device_data->device;
    			$data[] = $row;
    		}
    	}
    	
    	$result2 = DB::select(DB::raw($sql_data_set_length));
    	if(count($result2) > 0){
    		$dataObject = $result2[0];
    		$recordsTotal = $dataObject->recordsTotal;
    	}
    	
    	$out = array ();
		$data_len = count ( $data );
		$col_len = count ( $columns );
		for($i = 0; $i < $data_len; $i ++) {
			$row = array ();
			for($j = 0; $j < $col_len; $j ++) {
				$identifier = $columns [$j] ['db'];
				switch ($identifier) {
					case 'full_name' :
						$img = "<img style='width:44px;' class='img-circle thumbnail-highlight' src='" . url( 'themes/neon/assets/images/no_photo.png') . "'/>   ";
						if ($data [$i]->picture_url != "") {
							$img = "<img style='width:44px;' class='img-circle thumbnail-highlight' src='" . $data [$i]->picture_url . "'/>   ";
						}
						$row [] = "<a href='javascript:GetSocialUserDetail(" . $data [$i]->id . ");'>" . $img . " " . $data [$i]->full_name . "</a>";
						break;
					case 'email' :
						$row [] = $data [$i]->email;
						break;
					case 'gender' :
						$row [] = $data [$i]->gender == "" ? "n/a" : ucfirst ( $data [$i]->gender );
						break;
					case 'campaign' :
						$row [] = $data [$i]->campaign == "" ? "n/a" : "<a style='color:blue;' href='" . url ( 'campaign/view?camp_id=' . $data [$i]->campaign_id ) . "'>" . $data [$i]->campaign . "</a>";
						break;
					case 'location' :
						$row [] = $data [$i]->location == "" ? "n/a" : "<a style='color:blue;' href='" . url ( 'location/overview?loca=' . $data [$i]->location_id ) . "'>" . $data [$i]->location . "</a>";
						break;
					case 'social_network' :
						switch ($data [$i]->social_network) {
							case 'FBuser' :
								$row [] = '<i class="fa fa-facebook-square"></i>';
								break;
							case 'TWuser' :
								$row [] = '<i class="fa fa-twitter-square"></i>';
								break;
							case 'LIuser' :
								$row [] = '<i class="fa fa-linkedin-square"></i>';
								break;
							case 'GPuser' :
								$row [] = '<i class="fa fa-google-plus-square"></i>';
								break;
							case 'IGuser' :
								$row [] = '<i class="fa fa-instagram"></i>';
								break;
							case 'Cuser' :
								$row [] = '<i class="fa fa-envelope-square"></i>';
								break;
						}
						break;
					case 'return' :
						$label = "<label class='badge badge-info'>New</label>";
						if ($data [$i]->return > 0) {
							$label = "<label class='badge badge-info'>" . $data [$i]->return . "</label>";
						}
						$row [] = $label;
						break;
					case 'os_name' :
						$row [] = getOSLogo ( $data [$i]->os_name );
						break;
					case 'device' :
						$row [] = $data [$i]->device == "" ? "Unknown" : $data [$i]->device;
						break;
					case 'added_datetime' :
						$added_datetime = $data [$i]->added_datetime;
						$server_timezone = ini_get('date.timezone');
						$user_timezone = $data [$i]->location_timezone;
						if ($user_timezone == "" || $user_timezone == "0") {
							$user_timezone = $data [$i]->owner_timezone;
						}
						if ($user_timezone == "" || $user_timezone == "0") {
							$user_timezone = $data [$i]->server_timezone;
						}
						$row [] = convertTimeBasedOnTimezone ( $server_timezone, $user_timezone, $added_datetime, 'd M, Y @ h:i A (P)', false );
						break;
				}
			}
			$out [] = $row;
		}
		
		// Output
		$output = array (
				"draw" => intval ( $request->input('draw')),
				"recordsTotal" => intval ( $recordsTotal ),
				"recordsFiltered" => intval ( $recordsFiltered ),
				"data" => $out 
		);

	  return json_encode($output);
    }
    
    public function LoadTimelineData(Request $request){
    	$this->checkAuthentication();
    	$currently_showing = $request->input('currently_showing');
    	$markup = "";
    	$sql = "SELECT SQL_CALC_FOUND_ROWS su.social_user_id AS id, su.campaign_id, su.location_id, d.id AS device_id, 
				su.social_network, su.full_name, su.picture_url, c.name AS campaign, IF( LENGTH( l.name ) <= 0,  'N/A', l.name ) AS location, d.name AS device, 
				su.added_datetime, u.time_zone AS owner_timezone, l.time_zone AS location_timezone, 
				@@system_time_zone AS server_timezone
				FROM `social_user` AS su
				LEFT JOIN location AS l ON su.location_id = l.id
				LEFT JOIN device AS d ON su.device_mac = d.mac_address
				LEFT JOIN campaign AS c ON su.campaign_id = c.id
				LEFT JOIN user AS u ON u.id = l.owner
				WHERE su.full_name != '' ";
    	switch(Session::get('USER_TYPE')){
    		case '2':
    			$l_ids =  "";
    			$locations = Location::where('remove',0)->where('owner',Session::get('USER_ID'))->select('id')->get();
    			if(count($locations)  > 0 ){
    				foreach($locations as $location){
    					$l_ids .=$location->id.",";
    				}
    			}
    			
    			$location_ids = $l_ids != ""?rtrim($l_ids,','):0;
    			$sql .="and su.location_id IN ($location_ids)";
    			break;
    		case '3':
    			$location_ids = Session::get('LOCATION_IDS') == null? 0: Session::get('LOCATION_IDS');
    			$sql .="and su.location_id IN($location_ids)";
    			break;
    	}
    	
    	$sql .=" ORDER BY su.social_user_id DESC LIMIT $currently_showing, 9";
    	$query = DB::select(DB::raw($sql));
    	if(count($query) > 0){
    		$count = $currently_showing + 1;
    		$currently_showing += count($query);
    		foreach($query as $row){
    			$added_datetime  = $row->added_datetime;
    			$server_timezone = ini_get('date.timezone');
    			$user_timezone = $row->location_timezone;
    			if($user_timezone == '' || $user_timezone == "0"){
    				$user_timezone = $row->owner_timezone;
    			}
    			if($user_timezone == "" || $user_timezone == "0"){
    				$user_timezone = $row->server_timezone;
    			}
    			
    			$row->added_datetime = convertTimeBasedOnTimezone($server_timezone, $user_timezone, $added_datetime, 'd M, Y @ h:i A(P)');
    			$device_data = SocialUserDeviceInfo::where('suid',$row->id)->orderBy('created_at','desc')->select('os_name','model')->first();
    			if(count($device_data) <= 0){
    				$device_data = new stdClass();
    				$device_data->os_name = '';
    				$device_data->model = '';
    			}
    			
    			$row->os_name = $device_data->os_name;
    			$row->model = $device_data->model;
    			
    			$class = $count % 2 == 0 ? "left-aligned" : "";
				$markup .= '<article class="timeline-entry ' . $class . '">
						<div class="timeline-entry-inner">
							<time class="timeline-time">
								<span>' . $row->added_datetime. '</span>
								<span>Today</span>
							</time>
							<div class="timeline-icon">
								<img class="img-circle img-responsive pointer" src="' . $row->picture_url . '" onclick="javascript:GetSocialUserDetail(' . $row->id . ');" alt="user-avatar">
							</div>
							<div class="timeline-label">
								<h2>
									<a href="javascript:GetSocialUserDetail(' . $row->id. ');">' . $row->full_name . '</a>
									' . getSocialMediaIcon ( $row->social_network) . '
									' . getOSLogo ( $row->os_name ) . '
								</h2>
								<p>Campaign:  <a href="' . url ( "campaign/view?camp_id=" . $row->campaign_id) . '">' . $row->campaign. '</a></p>
								<p>Location:  <a href="' . url ( "location/overview?loca=" . $row->location_id) . '">' . $row->location . '</a></p>
								<p>Device:  <a href="' . url ( "campaign/devicemodal&data=" ) . base64_encode ( $row->device_id) . '">' . $row->device. '</a></p>
							</div>
						</div>
					</article>';
				$count ++;
    		}
    	}else{
    		$markup = '';
    	}
    	
    	$output = array(
    	      'currently_showing' => $currently_showing,
				'markup' => $markup 
    	);
    	
    	return json_encode($output);
    }
    
    public function RetriveAppInfoById(Request $request){
    	$this->checkAuthentication();
    	$id = $request->input('id');
    	$appinfo = AppInfo::where('id',$id)->first();
    	if($appinfo){
    		$result = array(
    		 'id'=>$appinfo->id,
    		 'name'=>$appinfo->app_name,
    		 'appid'=>$appinfo->app_id,
    		 'appsecrect'=>$appinfo->app_secrect,
    		 'type'=>$appinfo->type
    		);
    	}else{
    		$result = array(
    		 'id'=>'',
    		 'name'=>'',
    		 'appid'=>'',
    		 'appsecrect'=>'',
    		 'type'=>''
    		);
    	}
    	return json_encode($result);
    }
    
    public function CheckFacebookApp(Request $request){
    	$appid  = $request->input('appid');
    	$appsecret = $request->input('appsecret');
    	$url = "https://graph.facebook.com/$appid?fields=id&access_token=$appid|$appsecret";
    	
    	$opts = array(
    		'http'=>array(
    	       'method'=>'get',
    	       'max_redirects'=>'0',
    	       'ignore_errors'=>'1'
    	     )
    	);
    	
    	$context = stream_context_create($opts);
    	$stream = fopen($url , 'r',false,$context);
    	$output = json_decode(stream_get_contents($stream));
    	fclose($stream);
    	if(isset($output->error)){
    		return 'invalid';
    	}else{
    		return 'valid';
    	}    	
    }
    
    public function CheckTwitterApp(Request $request){
    	return 'valid';
    }
    public function CheckLinkedInApp(Request $request){
    	return 'valid';
    }
    
    public function CheckGoogleApp(Request $request){
    	return 'valid';
    }
    
    public function CheckInstagramApp(Request $request){
    	return 'valid';
    }
    
    public function CheckVkontakteApp(Request $request){
    	return 'valid';
    }
    public function setDefault(Request $request){
    	$this->checkAuthentication();
    	$id = $request->input('id');
    	$type_id = $request->input('type_id');
    	
    }
    
    public function GetSubUserById(Request $request){
    	$this->checkAuthentication();
    	$user_id = $request->input('id');
    	$sql = "SELECT u.first_name, u.last_name, u.email_address, u.mobile_phone, up.module_ids, up.location_ids, up.campaign_ids FROM `user` as u LEFT JOIN user_permission as up ON u.id = up.user_id WHERE u.id = " . $user_id;
    	$query = DB::select(DB::raw($sql));
    	$user = $query[0];
    	
    	$output['first_name'] = $user->first_name;
    	$output['last_name'] = $user->last_name;
    	$output['email'] = $user->email_address;
    	$output['phone'] = $user->mobile_phone;
    	$output['module_ids'] = $user->module_ids;
    	$location_array = array();
    	if(!empty($user->location_ids)){
    		$location_array = explode(',',$user->location_ids);
    	}
    	
    	$location_ids = $user->location_ids != ""?$user->location_ids : 0;
    	$my_data =Location::whereIn('id',explode(',',$location_ids))->get();
    	$location_data = null;
    	if(count($my_data) > 0){
    		foreach($my_data as $row){
    			$temp_array = array(
    				'id'=>$row->id,
    			    'text'=>$row->name
    			);
    			$location_data[] = $temp_array;
    		}
    	}
    	
    	if(is_array($location_data)){
    		$output['location_data'] = json_encode($location_data);
    	}
    	$campaign_data = NULL;
    	$campaign_ids = $user->campaign_ids != ""?$user->campaign_ids:0;
    	$my_data = Campaign::whereIn('id',explode(',',$campaign_ids))->get();
    	if(count($my_data) > 0){
    		foreach($my_data as $row){
    			$temp_array = array(
    			  'id'=>$row->id,
    			  'text'=>$row->name
    			);
    			$campaign_data [] = $temp_array;
    		}
    	}
    	
    	if(is_array($campaign_data)){
    		$output['campaign_data'] = json_encode($campaign_data);
    	}
    	
    	$permitted_modules = explode(',',$user->module_ids);
    	$output['module_array'] = json_encode($permitted_modules);
    	
    	return json_encode($output);
    }  

    
    public function updateprofile(Request $request){
    	$this->checkAuthentication();
    	$output = array();
    	$owner = Session::get('USER_TYPE') == '3'?Session::get('USER_CREATED_BY') : Session::get('USER_ID');
    	$validation_error  ="";
    	$img_name = "";
    	$is_upload = false;
    	if(!$request->has('first_name') || $request->input('first_name') == ""){
    		$validation_error .= "First Name is required.<br>";
    	}
    	if(!$request->has('mobile_phone') || $request->input('mobile_phone') == ""){
    		$validation_error .= "Mobile Phone is required.<br>";
    	}
    	if(!$request->has('time_zone') || $request->input('time_zone') == "0"){
    		$validation_error .= "Time Zone is required.<br>";
    	}
    	
    	$password = $request->has('new_password') ?$request->input('new_password'):'';
    	$confirm_password = $request->has('confirm_password')?$request->input('confirm_password'):"";
    	if($password !="" || $confirm_password !=""){
    		if($password != $confirm_password){
    			$validation_error .="Password did not match.";
    		}
    	}
    	
    	if($validation_error == ""){
    		if($request->file('profile_photo') && $request->file('profile_photo')!= ""){
    			$validationextns  = array(
    				'jpeg','png','jpg'
    			);
    		}
    		if($request->file('profile_photo')){
    		$type = $request->file('profile_photo')->getClientMimeType();
    		$ext  =$request->file('profile_photo')->getClientOriginalExtension();
    		$size = $request->file('profile_photo')->getClientSize();       		 		
    		if(($type == "image/jpg" || $type == "image/jpeg"|| $type == "image/png") && ($size < 200000)){
    			if($request->file('profile_photo')->getError() > 0){
    				$validation_error .=$request->file('profile_photo')->getErrorMessage();
    			}else{
    				$new_file_path = "profile_photo_".$owner.'.'.$ext;
    				$targetPath = Config::get('aws.UPLOAD').$new_file_path;
    				//try{
	    				$s3 = Storage::disk('s3');
	    				if($s3->exists($targetPath)){
	    					$s3->delete($targetPath);
	    				}
	    				$s3->put($targetPath,file_get_contents($request->file('profile_photo')),'public');
	    				Option::addOption('profile_photo_'.$owner, Config::get('aws.AWS_CDN').$targetPath);
    					$img_name = Option::getOption( "profile_photo_" . $owner ) == "" ? url (Config::get('aws.NO_PHOTO')) : Option::getOption( "profile_photo_" . $owner );
    					Session::put('PROFILE_PHOTO',$img_name);
    					$is_upload  =true;
    				//}catch(Exception $e){
    					//$validation_error .="Cannot upload photo Image.";
    				//}    				
    			}
    		}else{
    			$validation_error .="Invalid file size or type.";
    		}
    	  }
    	}
       if($validation_error == ""){
       	   $sql = "UPDATE `user` SET 
					`first_name`='" .( $request->input('first_name')) . "', 
					`last_name`='" . ( $request->input('last_name') ). "',
					`full_name`='" . ( $request->input ('first_name') . " " . $request->input ('last_name')). "',
					`mobile_phone`='" . $request->input('mobile_phone') . "',
					`time_zone`='" . $request->input('time_zone') . "'";
			
			if ($request->has('confirm_password') && $request->input('confirm_password') != "") {
				$sql .= ", `password`='" . md5 ( $request->input ('confirm_password')) . "'";
			}
			
			$sql .= " WHERE `id`='" . Session::get('USER_ID') . "'";
			
           $affrctedRows = DB::update(DB::raw($sql));
			
			if ($affrctedRows > 0 || $is_upload) {
				Session::put ( 'FULL_NAME', $request->input('first_name') . " " . $request->input ('last_name') );
				Session::put('USER_TIME_ZONE',$request->input('time_zone'));
				$output ['status'] = 'success';
				$output ['message'] = '<div role="alert" class="alert alert-success fade in">
                        <button data-dismiss="alert" class="close" type="button"><i class="fa fa-times-circle"></i></button>
                        <i class="fa fa-check-circle"></i> User has been successfully updated!
                    </div>';
				$output ['full_name'] = $request->input('first_name') . " " .  $request->input('last_name');
			} else {
				$output ['status'] = 'error';
				$output ['message'] = '<div role="alert" class="alert alert-danger fade in">
                        <button data-dismiss="alert" class="close" type="button"><i class="fa fa-times-circle"></i></button>
                        <i class="fa fa-warning"></i> User has not been successfully updated!
                    </div>';
			}
		} else {
			$output ['status'] = 'validation_error';
			$output ['message'] = '<div role="alert" class="alert alert-warning fade in">
                        <button data-dismiss="alert" class="close" type="button"><i class="fa fa-times-circle"></i></button>
                        ' . $validation_error . '
                    </div>';
		}  
		return json_encode($output);  	
    }   
    
    public function GetPlatformUserProfile(Request $request){
    	$this->checkAuthentication();
    	$output = array();
    	Session::put('platform_user_id',$request->input('user_id'));
    	$result = User::select('first_name','last_name','email_address','mobile_phone','time_zone')->where('id',$request->input('user_id'))->first();
    	if($result){
    		$output['first_name'] = $result->first_name;
    		$output['last_name'] = $result->last_name;
    		$output['email_address'] = $result->email_address;
    		$output['mobile_phone'] = $result->mobile_phone;
    		$output['time_zone'] = $result->time_zone;
    	}
    	return json_encode($output);
    }
    
    public function UpdateUserProfile(Request $request){
    	$this->checkAuthentication();
    	$output = "";
    	$flag = true;
    	$validation_error = "";
    	if(!$request->has('first_name') || $request->input('first_name') == ''){
    		$validation_error .='First Name is required.<br>';
    		$flag = false;
    	}
    	
    	if(!$request->has('mobile_phone') || $request->input('mobile_phone') == ''){
    		$validation_error .="Mobile Phone is required.<br>";
    		$flag = false;
    	}
    	
    	if(!$request->has('time_zone') || $request->input('time_zone') == ""){
    		$validation_error .="Time Zone is requird.<br>";
    		$flag = false;
    	}
    	
    	if($flag == true){
    		$nums = User::where('id',Session::get('platform_user_id'))->update(array('first_name'=>$request->input('first_name'),'last_name'=>$request->input('last_name'),'full_name'=>$request->input('first_name')." ".$request->input('last_name'),'mobile_phone'=>$request->input('mobile_phone'),'time_zone'=>$request->input('time_zone'),'modified'=>date('Y-m-d h:m:s')));
    		if($nums > 0){
    			$output = '<div role="alert" class="alert alert-success alert-dismissible fade in">' . '<button data-dismiss="alert" class="close" type="button">' . '<i class="fa fa-times"></i>' . '<span class="sr-only">Close</span>' . '</button>' . '<i class="fa fa-check-circle"></i> User has been successfully updated.' . '</div>';
    		}else{
    			$output = '<div role="alert" class="alert alert-danger alert-dismissible fade in">' . '<button data-dismiss="alert" class="close" type="button">' . '<i class="fa fa-times"></i>' . '<span class="sr-only">Close</span>' . '</button>' . '<i class="fa fa-times-circle"></i> User has not been successfully updated.' . '</div>';
    		}
    	}else{
    		$output = '<div role="alert" class="alert alert-warning alert-dismissible fade in">' . '<button data-dismiss="alert" class="close" type="button">' . '<i class="fa fa-times"></i>' . '<span class="sr-only">Close</span>' . '</button>' . $validation_error . '</div>';
    	}
    	
    	return $output;
    }
    
    public function ChangeStatusLocation(Request $request){
    	$this->checkAuthentication();
    	$output = "";
    	$id  = $request->input('id');
    	$location = Location::where('id',$id)->first();
    	if($location){
    		if($location->status == '1'){
    			$num = Location::where('id',$id)->update(array('status'=>0));
    		}else{
    			$num = Location::where('id',$id)->update(array('status'=>1));
    		}
    		
    		if($num > 0){
    			$output = 'success';
    		}    		
    	}    	
    	return $output;
    }
    
    public function ProcessPayment(Request $request){
    	$this->checkAuthentication();   	
		return processpayment($request);
    }

    public function GetSocialUserDetail(Request $request){
    	$this->checkAuthentication();
    	$sql = "SELECT social_network_id, username, su.social_user_id as id, su.picture_url, IF( LENGTH( su.full_name ) <= 0,  'No Name', su.full_name ) AS full_name, su.email, su.username, 
				su.device_mac, su.client_mac, CONCAT(UCASE(LEFT(su.gender, 1)), SUBSTRING(su.gender, 2)) AS gender, IF( LENGTH( l.name ) <= 0,  'N/A', l.name ) AS location, c.name AS campaign, su.location_id, 
				su.social_network AS media, su.added_datetime, u.time_zone AS owner_timezone, l.time_zone AS location_timezone, 
				@@system_time_zone AS server_timezone, sudi.os_name, IF(LENGTH (sudi.model) > 0, CONCAT(sudi.device,' (',sudi.model,')'), 
				sudi.device) AS device FROM social_user AS su 
				LEFT JOIN location AS l ON su.location_id = l.id 
				LEFT JOIN campaign AS c ON su.campaign_id = c.id 
				LEFT JOIN user AS u ON u.id = l.owner 
				LEFT JOIN `social_user_device_info` AS sudi ON su.social_user_id = sudi.suid 
				WHERE su.social_user_id = " . $request->input('id');
    	$result = DB::select(DB::raw($sql));
    	$user_details = $result[0];
    	$added_datetime = $user_details->added_datetime;
    	$server_timezone = ini_get('date.timezone');
    	$user_timezone = $user_details->location_timezone;
    	if($user_timezone == '' || $user_timezone == '0'){
    		$user_timezone = $user_details->owner_timezone;
    	}
    	$user_details->added_datetime = convertTimeBasedOnTimezone($server_timezone, $user_timezone,$added_datetime, 'd M, Y @ h:i A (P)' );
    	$user_details->os_name = getOSLogo($user_details->os_name);
    	$user_details->timezone = $user_timezone;
    	return json_encode($user_details);
    }
    
    public function DeletePicture(Request $request){
    	$this->checkAuthentication();
    	$option_name = $request->input('option_name');
    	$photo_path = $request->input('photo_path');
    	$s3 = Storage::disk('s3');    	
    		
    	$targetPath = ltrim($photo_path,Config::get('aws.AWS_CDN'));      		
    	$s3->delete($targetPath);
    	Option::deleteOption($option_name);
    	Session::put('PROFILE_PHOTO',Config::get('constants.NO_PHOTO'));  	
    	
    }
    
    public function DeleteCBGImage(Request $request){
    	$this->checkAuthentication();
    	$output = 'failed';
    	$path = $request->input('file_name');
    	try{
	    	$s3 = Storage::disk('s3');
	    	if($s3->exists($path)){
	    		$s3->delete($path);
	    	}
	    	$output = 'succeed';
    	}catch(Exception $e){
    		
    	}
    	
    	return $output;
    }
    
    public function ResetAllStatistics(Request $request){
    	$this->checkAuthentication();    	
    	if($request->has('type') && $request->input('type') == 'location'){
    		$location_id = trim($request->input('identifier'));
    		$social_user  = DB::table('social_user')->where('location_id',$location_id);
    		$device_info = DB::table('social_user_device_info')->where('location_id',$location_id);
    	}else{
    		$device_mac = trim($request->input('identifier'));
    		$social_user = DB::table('social_user')->where('device_mac',$device_mac);
    		$device_info = DB::table('social_user_device_info')->where('device_mac',$device_mac);
    	}
    	try{
    		$social_user->delete();
    		$device_info->delete();
    		$message = "Statistics has been successfully deleted.";
    	}catch(Exception $e){
    		$message = "Statistics has not been successfully deleted.";
    	}
    	return $message;
    }
    
    public function loadreportdata(Request $request){
    	$this->checkAuthentication();
    	
    	$location_ids = 0;
    	$start_date = $request->input('start_date');
    	$end_date = $request->input('end_date');
    	$sort_by = $request->has('sort-by')?$request->input('sort-by'):'';
    	$device_mac = $request->has('device_mac')?$request->input('device_mac'):'';
    	$location_id = $request->has('location_id')?$request->input('location_id'):'';
    	
    	$where = "WHERE date(su.added_datetime) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
    	
    	if($sort_by != ''){
    		if($sort_by == 'location'){
    			$where .= " AND su.location_id IN($location_id)";
    		}elseif($sort_by == 'device'){
    			$where .= " AND su.device_mac = '$device_mac'";
    		}
    	}else{
    		if(Session::get('USER_TYPE') == '2'){
    			$sql = "SELECT GROUP_CONCAT( id ) AS location_ids FROM location WHERE `remove` = 0 AND owner = " . Session::get('USER_ID');
    			$query = DB::select(DB::raw($sql));
    			if(count($query) > 0){
    				$result = $query[0];
    				$location_ids = $result->location_ids;
    			}
    			$where .= " AND su.location_id IN($location_ids)";
    		}elseif(Session::get('USER_TYPE') == '3'){
    			$location_ids = Session::get ( 'LOCATION_IDS' ) != null ? Session::get ( 'LOCATION_IDS' ) : 0;
    			$where .= " AND su.location_id IN($location_ids)";
    		}
    	}
    	
    	$sql = "SELECT IF(LENGTH(su.full_name) > 0 , su.full_name, 'Unknown') AS full_name, IF(LENGTH(su.email) > 0 , su.email, 'N/A') AS email, IF(LENGTH(su.gender) > 0 , su.gender, 'N/A') AS gender, l.name AS location_name, su.social_network, DATE_FORMAT(su.added_datetime,'%b %d %Y @ %h:%i %p') AS added_datetime
		FROM social_user AS su LEFT JOIN location AS l ON su.location_id = l.id $where";
    	$output = "";
    	$query = DB::select(DB::raw($sql));
    	if(count($query) > 0){
    		foreach ($query as $row){
    			$output .= "<tr>";
				$output .= "<td>" . $row->full_name . "</td>";
				$output .= "<td>" . $row->email . "</td>";
				$output .= "<td class='text-center'>" . ucfirst ( $row->gender ) . "</td>";
				$output .= "<td>" . $row->location_name . "</td>";
				$output .= "<td class='text-center'>" . $row->social_network . "</td>";
				$output .= "<td>" . $row->added_datetime . "</td>";
				$output .= "</tr>";
    		}	
    	}

    	return $output;
    }
}




























