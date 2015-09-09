<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Session;
use Illuminate\Filesystem\Filesystem;
use App\Option;

use App\SubDomain;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App;
use Exception;

require_once app_path().'/helper/helper.php';

class OptionsController extends Controller
{
   public  function __construct(){
   	 view()->share('controller','options');
   	 $this->middleware('auth');
   	 $this->middleware('boot');
   }
   
   public function view(){
   	 view()->share('actions','view');
   	 if(Session::get('USER_TYPE') == '3'){
   	 	return redirect('user/authorizationrequired');
   	 }
   	 
   	 $data = array();
   	 
   	 if(Session::get('USER_TYPE') != '1'){
   	 	if(Session::get('sub_domain') || Session::get('custom_domain')){
   	 		$data['custom_domain'] = Session::get('custom_domain');
   	 		$data['sub_domain'] = Session::get('sub_domain');
   	 		Session::forget('custom_domain');
   	 		Session::forget('sub_domain');
   	 	}else{
   	 		$result = SubDomain::find(Session::get('SITE_ID'));
   	 		if($result){
   	 			$data['custom_domain'] = $result->custom_domain;
   	 			$data['sub_domain'] = $result->title;
   	 		}
   	 	}
   	 }
   	 $data['subdomain_title'] = Session::get('SUBDOMAIN_TITLE') && Session::get('SUBDOMAIN_TITLE') != "" ? Session::get('SUBDOMAIN_TITLE') : "";   	 
   	 $data['options'] = array('logo'=>Option::getOption('logo'),
   	                          'favicon'=>Option::getOption('favicon'),
   	                          'site_title'=>Option::getOption('site_title'),
   	                          'app_secret_key'=>Option::getOption('app_secret_key'),
   	                          'logoff_time'=>Option::getOption('logoff_time'),
   	                          'menu_background_color'=>Option::getOption('menu_background_color'),
   	 						  'menu_background_hover_color'=>Option::getOption('menu_background_hover_color'),
   	                          'menu_text_color'=>Option::getOption('menu_text_color'),
   	                          'menu_text_hover_color'=>Option::getOption('menu_text_hover_color'),
   	 						  'max_bandwidth'=>Option::getOption('max_bandwidth'),
   	 						  'session_time_limit'=>Option::getOption('session_time_limit'),
   	                          'privacy_policy'=>Option::getOption('privacy_policy'),
   	                          'terms_condition'=>Option::getOption('terms_condition'),
   	                          'standard_terms_privacy'=>Option::getOption('standard_terms_privacy'),
   	                          'no_campaign'=>Option::getOption('no_campaign'),
   	                          'device_not_active'=>Option::getOption('device_not_active'),
   	                          'recurly_thankyou_message'=>Option::getOption('recurly_thankyou_message'),
   	 						  'conversion_tracking_code'=>Option::getOption('conversion_tracking_code'),
   	                          'members_area_content'=>Option::getOption('members_area_content')
   	 						  );
   	 return view('options.view',$data);   	
   }
   
   public function savebranding(Request $request){
   	  $validation_error = '';
   	  $flag  = true;
   	  
   	  if(Session::get('USER_TYPE') != '1'){
   	  	 Session::put('sub_domain',$request->input('subdomain'));
   	  	 Session::put('custom_domain',$request->input('custom_domain'));
   	  	 
   	  	 if($request->input('subdomain') ==""){
   	  	 	$validation_error .="Subdomain is required.<br>";
   	  	 	$flag = false;
   	  	 }else{
   	  	 	$subdomain = $request->input('subdomain');
   	  	 	$subdomains = SubDomain::where('title',$subdomain)->where('id','!=',Session::get('SITE_ID'))->first();
   	  	 	if(count($subdomains) > 0){
   	  	 		$validation_error .= "Given subdomain '<b>".$subdomain."</b>' is already exists.<br>";
	    		$flug = false;
   	  	 	}
   	  	 }
   	  	 
   	  	 if($request->input('custom_domain') != ""){
   	  	 	$custom_domain = $request->input('custom_domain');
   	  	 	$subdomains = SubDomain::where('custom_domain',$custom_domain)->where('id','!=',Session::get('SITE_ID'))->first();
   	  	 	if(count($subdomains) > 0){
   	  	 		$validation_error .= "Given custom domain '<b>".$custom_domain."</b>' is already exists.<br>";
	    		$flug = false;
   	  	 	}
   	  	 }
   	  }
   	  
   	  if($request->file('site_logo')){	   	  
   	  	if($request->file('site_logo')->getError() > 0){
   	  		$validation_error .="Return Code: ".$request->file('site_logo')->getError().'<br>';
   	  		$flag = false;
   	  	}else{
   	  		$validextensions = array('image/jpeg','image/jpg','image/png');
   	  		$file_extension = $request->file('site_logo')->getExtension();
   	  		
   	  		if(!in_array($request->file('site_logo')->getClientMimeType(), $validextensions) || $request->file('site_logo')->getClientSize() > 100000){
   	  			$validation_error .= "Invalid type.<br>";
    			$flug = false;
   	  		}
   	  	}	   	
   	  }
   	  if($request->file('favicon')){	   	  
   	  	if($request->file('favicon')->getError() > 0){
   	  		$validation_error .="ReturnCode : ".$request->file('favicon')->getError()."<br>";
   	  		$flag = false;
   	  	}else{
   	  		$fav_extension = $request->file('favicon')->getClientOriginalExtension();
   	  		if(!in_array($fav_extension, array('ico','ICO','png','PNG'))){
   	  			$validation_error .= "File type should be .ico or .png<br>";
    			$flug = false;
   	  		}else{
   	  			if($request->file('favicon')->getClientSize() > 100000){
   	  				$validation_error .= "File size too large.<br>";
    				$flug = false;
   	  			}
   	  		}
   	  	}	   	 
   	  }
   	  if($flag ==  true){
	   	  	if($request->file('site_logo')){
	   	  	  if($request->file('site_logo')->getClientMimeType() && $request->file('site_logo')->getClientMimeType() != ""){
	   	  	  	$new_file_name = "logo_".Session::get('SITE_ID').".".$request->file('site_logo')->getClientOriginalExtension();
	   	  	  	$targetPath = Config::get('aws.UPLOAD').$new_file_name;
	   	  	  	$s3 = Storage::disk('s3');
	   	  	  	try{	   	  	  		  	  	  	
		   	  	  	//if($s3->exists($targetPath) == 1){
		   	  	  		//$s3->delete($targetPath);
		   	  	  	//}
		   	  	  	$s3->put($targetPath,file_get_contents($request->file('site_logo')),'public');
		   	  	  	Option::addOption("logo", Config::get('aws.AWS_CDN').$targetPath);
					Session::put('SITE_LOGO', Config::get('aws.AWS_CDN').$targetPath); 
	   	  	  	}catch(Exception $e){
	   	  	  		$data['logo_error'] = "Logo has not been uploaded.";				
	   	  	  	}
	   	  	  }else{
	   	  	  	 if($request->input('hdn_logo_name') == "" && $request->input('hdn_site_logo') !=""){
	   	  	  	 	$targetPath = Config::get('aws.UPLOAD').$request->input('hdn_site_logo');
                    $s3 = Storage::disk('s3');
                    try{
                    	$s3->delete($targetPath);
                    	Option::addOption("logo", "");
						Session::put('SITE_LOGO',Config::get('constants.NO_LOGO'));
                    }catch(Exception $e){
                    	
                    }					
	   	  	  	 }
	   	  	  }
	   	  	}
	   	  	if($request->file('favicon')){
		   	  	  if($request->file('favicon')->getClientMimeType() && $request->file('favicon')->getClientMimeType() != ""){
		   	  	  	$new_favicon_name = "favicon_".Session::get('SITE_ID').".".$request->file('favicon')->getClientOriginalExtension();
		   	  	  	$targetPath = Config::get('aws.UPLOAD').$new_favicon_name;
		   	  	  	$s3 = Storage::disk('s3');
		   	  	  	//echo $targetPath;
		   	  	  	try{
		   	  	  		//if($s3->exists($targetPath) == 1){
		   	  	  			//$s3->delete($targetPath);
		   	  	  			//echo "exist";
		   	  	  		//}
		   	  	  		$s3->put($targetPath,file_get_contents($request->file('favicon')),'public');
		   	  	  		Option::addOption("favicon", Config::get('aws.AWS_CDN').$targetPath);
					 	Session::put('FAVICON', Config::get('aws.AWS_CDN').$targetPath);
		   	  	  	}catch(Exception $e){
		   	  	  		//echo "error";
		   	  	  		$data['logo_error'] = "Favicon has not been uploaded.";		
		   	  	  		   	  	  		
		   	  	  	}		   	  	  	
		   	  	  }else{
		   	  	  	if($request->input('hdn_favicon_name') == "" && $request->input('hdn_site_favicon') != ""){
		   	  	  		$targetPath = Config::get('aws.UPLOAD')+$request->input('hdn_site_favicon');
						$s3 = Storage::disk('s3');
						try{
	                    	$s3->delete($targetPath);	                    	
	                    }catch(Exception $e){
	                    	
	                    }	
						Option::addOption("favicon", "");
						Session::put('FAVICON',Option::getOption( "favicon", "", "default_favicon.ico" ));
		   	  	  	}
		   	  	  }
	   	  	}
   	  	  if($request->input('USER_TYPE') !='1'){
   	  	  	$subdomain_id = $request->input('SITE_ID');
   	  	  	$subdomain = trim(strtolower($request->input('subdomain')));
   	  	  	$custom_domain = $request->input('custom_domain');
   	  	  	SubDomain::where('id',$subdomain_id)->update(array('title'=>$subdomain,'custom_domain'=>$custom_domain));
   	  	  }
   	  	  
   	  	  $this->save_options($request);
   	  	  $message = GenerateConfirmationMessage("success", "<i class='fa fa-check-circle'></i> Option has been successfully updated.", true);;
   	  	  
   	  }else{
   	  	$message = GenerateConfirmationMessage("warning", $validation_error, true);
   	  }
   	  Session::put('SESSION_MESSAGE',$message);
   	  return redirect(url('options/view'));
   }
   
   public function save_options($request){
   	  Option::addOption('site_title', $request->input('site_title'));
   	  Option::addOption('menu_background_color',$request->input('menu_background_color'));
   	  Option::addOption('menu_background_hover_color',$request->input('menu_background_hover_color'));
   	  Option::addOption('menu_text_color', $request->input('menu_text_color'));
   	  Option::addOption('menu_text_hover_color',$request->input('menu_text_hover_color'));
   	  Option::addOption('max_bandwidth',$request->input('max_bandwidth'));
   	  Option::addOption('app_secret_key', $request->input('app_secret_key'));
   	  Option::addOption('session_time_limit', $request->input('session_time_limit')*3600);
   	  Option::addOption('privacy_policy',$request->input('privacy_policy'));
   	  Option::addOption('terms_condition', $request->input('terms_condition'));
   	  Option::addOption('footer',$request->input('footer'));
   	  if(Session::get('USER_TYPE') == '1'){
   	  	Option::addOption('no_campaign', $request->input('no_campaign'));
   	  	Option::addOption('device_not_active', $request->input('device_not_active'));
   	  	Option::addOption('standard_terms_privacy',$request->input('standard_terms_privacy'));
   	  	$logoff_time = ($request->input('logoff_time') == "" || $request->input('logoff_time') == 0)?10:$request->input('logoff_time');
   	  	Option::addOption('logoff_time', $logoff_time * 60);
   	  	Option::addOption('recently_thankyou_message', $request->input('recently_thankyou_message'));
   	  	Option::addOption('members_are_content', $request->input('members_area_content'));
   	  	Option::addOption('conversion_tracking_code', $request->input('conversion_tracking_code'));
   	  }
   }
}



















