<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Auth;

use App\SubScriptionDetail;

use App\AppInfo;

use Illuminate\Support\Facades\Config;
use App\UserMeta;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Option;
use App\User;
use App\SubDomain;
use App\UserPermission;

require app_path().'/helper/helper.php';

class UserController extends Controller
{
   public $email;
   public $password;
   public function __construct(){    	   
   	   view()->share('controller','user');    	   
   	   $this->middleware('boot');  	   
   }
   public function login(Request $request){    	    
   	    view()->share('action','login');    	     
   	    $this->getLogoFavicon();	        	    	
   		return view('user.login');
   }
   
   public function loginPost(Request $request){
   	   $email = $request->has('email')?$request->input('email'):'';
   	   $password = $request->has('password')?$request->input('password'):'';
   	   if(! empty($email) && ! empty($password)){
   	   	   $this->email = $email;
   	   	   $this->password = $password;
   	   	   $url = $this->loginProcess();
   	   	   if($url != '/'){
   	   	   	  return redirect($url);
   	   	   }else{
   	   	   	  return redirect()->back()->withInput();
   	   	   }
   	   	}else{
   	   	  $message = GenerateConfirmationMessage('warning', '<i class="fa fa-info-circle"></i> Email and Password are required.' );
   	   	  Session::put('SESSION_MESSAGE',$message);
   	   	  return redirect()->back()->withInput();
   	   }
   }
   
   public function loginProcess($redirect_url =''){
   	   $user = User::where('email_address',$this->email)->first();   	   	   
   	   if(empty($this->email)){   	   	
   	   		$message = GenerateConfirmationMessage('danger', "<i class='fa fa-info-circle'></i> What's your password?" ); 
   	   		Session::put('SESSION_MESSAGE',$message);	   	   	    
   	   		return '/';	   	   			   		
   	   } 
       if(empty($this->password)){
       	    $message = GenerateConfirmationMessage ( 'danger', "<i class='fa fa-info-circle'></i> What's your password?" );
			Session::put( 'SESSION_MESSAGE', $message );
			return '/'; 			
       }
       if(!$user){
       	    $message = GenerateConfirmationMessage ( 'danger', '<i class="fa fa-info-circle"></i> Incorrect email address.' );
			Session::put( 'SESSION_MESSAGE', $message );
			return '/';			
       }
   		if (($user->password  != md5 ( $this->password )) && ($user->password != $this->password)) {
			$message = GenerateConfirmationMessage ( 'danger', '<i class="fa fa-info-circle"></i> Incorrect password.' );
			Session::put( 'SESSION_MESSAGE', $message );
			return '/';
		}
		
		if($user->is_active){
			Session::put('ACTIVE',1);
		}else{
			$message = GenerateConfirmationMessage ( 'danger', '<i class="fa fa-info-circle"></i> Incorrect password.' );
			Session::put( 'SESSION_MESSAGE', $message );
			return '/';
		}
		Auth::login($user);
		Session::put('EMAIL_ADDRESS',$user->email_address);
		Session::put('FULL_NAME',$user->full_name);
		Session::put('USER_CREATED_AT',$user->created_at);
		Session::put('USER_ID',$user->id);
		Session::put('PASSWORD',$user->password);
		Session::put('IS_ADMIN',$user->is_admin);
		Session::put('USER_TYPE',$user->user_type_id);
		Session::put('USER_CREATED_BY',$user->created_by);
		Session::put('FIRST_NAME',$user->first_name);
		Session::put('USER_TIME_ZONE',$user->time_zone);
		Session::put('END_TOUR',UserMeta::getUserMeta('end_tour'));
		Session::put('SITE_ID',$user->site_id);
		$owner = Session::get('USER_TYPE') == '3'?Session::get('USER_CREATED_BY'):Session::get('USER_ID');
		$profile_photo = Option::getOption("profile_photo_".$owner);
		if($profile_photo == ''){		
			$profile_photo = Config::get('constants.NO_PHOTO');
		}
		Session::put('PROFILE_PHOTO',$profile_photo);	
		$this->getLogoFavicon();	
		Session::put('SUBDOMAIN_TITLE','');	
		if($user->site_id > 0){
			$record = SubDomain::where('id',$user->site_id)->select('title')->first();
			Session::put('SUBDOMAIN_TITLE',$record->title);
		}
		
		Session::put('AFFILIATE_DASHBOARD_URL','');
		$remote_url = "https://api.leaddyno.com/v1/affiliates/".$user->email_address."?key=".Config::get('constants.LD_PRIVATE_KEY');
		$content = file_get_contents($remote_url);
		$data = json_decode($content,true);
		if(is_array($data)){
			Session::push('AFFILIATE_DASHBOARD_URL', isset($data['affiliate_dashboard_url']) ? $data['affiliate_dashboard_url'] : "");				
		}
		
		$record = UserPermission::where('user_id',$user->id)->select('module_ids','location_ids','campaign_ids')->first();			
		if($record){
			if($record->module_ids){					
				Session::put('MODULE_IDS',$record->module_ids);													
				if(Session::get('USER_TYPE') != 3){
					Session::put('MODULES_IDS',$record->module_ids.',timeline');																							
				}
			}
			if($record->location_ids){
				Session::put('LOCATION_IDS',$record->location_ids);
			}
			if($record->campaign_ids){
				Session::put('CAMPAIGN_IDS',$record->campaign_ids);
			}
		}		
		if($redirect_url != ''){
			return $redirect_url;
		}else{
			return '/dashboard/view';
		}   	
   }
   
   protected function getLogoFavicon(){   	   
   	    $logo = Option::getOption("logo");   	   
   	    if($logo != ''){
   	    	//if (stristr ( $logo, Config::get('aws.AWS_CDN') ) === FALSE) {
				//$logo = Config::get('aws.AWS_CDN').Config::get('aws.UPLOAD'). $logo;
			//}
   	    }else{
   	    	$logo  = Config::get('constants.NO_LOGO');
   	    }
   	    
   	    $favicon = Option::getOption('favicon');
   		if ($favicon != '') {
			//$favicon = Config::get('aws.AWS_CDN').Config::get('aws.targetdir'). $favicon;
		}else{			
			$favicon = Config::get('aws.AWS_CDN').Config::get('aws.UPLOAD').'favicon/default_favicon.ico';
		}
		Session::put('SITE_LOGO',$logo);
		Session::put('FAVICON',$favicon);
   }
   
   public function connectsocialaccount(){
   	  view()->share('action','connectsocialaccount');
   	  $data = array();
   	  $data['app_info'] = AppInfo::RetrieveAll();
   	  return view('user.connectsocialaccount',$data);
   }
   
   public function affiliatedashboard(){
   	  $data = array();
   	  view()->share('action','affiliatedashboard');
   	  $affiliate_dashboard_url_data = Session::get('AFFILIATE_DASHBOARD_URL');
   	  $data['affiliate_dashboard_url'] = $affiliate_dashboard_url_data[0];
   	  return view('user.affiliatedashboard',$data);
   }
   
   public function editprofile(Request $request){
   	  $data = array();
   	  view()->share('action','editprofile');
   	  $data['user'] = User::RetriveByEmailAddress(Session::get('EMAIL_ADDRESS'));
   	  $data['owner'] = Session::get('USER_TYPE') == '3'?Session::get('USER_CREATED_BY'):Session::get('USER_ID');
   	  $d = getRecurlyData($data['owner']);
   	  
   	  $data['recurly_data'] = array (
				'subscription_id' => isset ( $d ['subscription_detail'] ['subscription_id']) ? $d ['subscription_detail'] ['subscription_id'] : "",
				'plan_code' => isset ( $d ['subscription_detail'] ['plan_code'] ) ? $d ['subscription_detail'] ['plan_code'] : "",
				'plan_name' => isset ( $d ['subscription_detail'] ['plan_name'] ) ? $d ['subscription_detail'] ['plan_name'] : "",
				'allowed_quantity' => isset ( $d ['subscription_detail'] ['allowed_quantity'] ) ? $d ['subscription_detail'] ['allowed_quantity'] : "1",
				'plan_price' => isset ( $d ['subscription_detail'] ['plan_price'] ) ? $d ['subscription_detail'] ['plan_price'] : "0.00",
				'plan_interval' => isset ( $d ['subscription_detail'] ['plan_interval'] ) ? $d ['subscription_detail'] ['plan_interval'] : "",
				'first_name' => isset ( $d ['billing_info'] ['first_name'] ) ? $d ['billing_info'] ['first_name'] : "",
				'last_name' => isset ( $d ['billing_info'] ['last_name'] ) ? $d ['billing_info'] ['last_name'] : "",
				'address' => isset ( $d ['billing_info'] ['address'] ) ? $d ['billing_info'] ['address'] : "",
				'city' => isset ( $d ['billing_info'] ['city'] ) ? $d ['billing_info'] ['city'] : "",
				'state' => isset ( $d ['billing_info'] ['state'] ) ? $d ['billing_info'] ['state'] : "",
				'zip' => isset ( $d ['billing_info'] ['zip'] ) ? $d ['billing_info'] ['zip'] : "",
				'country' => isset ( $d ['billing_info'] ['country'] ) ? $d ['billing_info'] ['country'] : "",
				'card_type' => isset ( $d ['billing_info'] ['card_type'] ) ? $d ['billing_info'] ['card_type'] : "",
				'last_four' => isset ( $d ['billing_info'] ['last_four'] ) ? $d ['billing_info'] ['last_four'] : "",
				'plan_list' => is_array ( $d ['plan_list'] ) ? $d ['plan_list'] : array () 
		);
	   $data['timezones'] = get_timezones();
	   $data['profile_photo'] = Option::getOption('profile_photo_'.$data['owner']);	   
	   return view('user.editprofile',$data);
   }
   
   
   public function logout(){
   	   Session::flush();
   	   Auth::logout();
   	   process_unsetcookie();
   	   return redirect('/');
   }
   
   public function lockscreen(Request $request){
   	  view()->share('action','lockscreen');
   	  if(!Session::get('EMAIL') && (Session::get('EMAIL_ADDRESS') && Session::get('EMAIL_ADDRESS') != '')){
   	  	$email = Session::get('EMAIL_ADDRESS');
   	  	$full_name = Session::get('FULL_NAME');
   	  	$profile_photo = Session::get('PROFILE_PHOTO');   	  	
   	  	$site_id = Session::get('SITE_ID');
   	  	$redirect_url = Session::get('REDIRECT_URL');   	  	
   	  	//Auth::logout(); 
   	  	Session::flush();  	  	
   	  	process_unsetcookie();
   	  	Session::put('EMAIL',$email);
   	  	Session::put('NAME',$full_name);
   	  	Session::put('PHOTO',$profile_photo);
   	  	Session::put('SITE_ID',$site_id);
   	  	Session::put('REDIRECT_URL',$redirect_url);
   	  	Session::put('LOGOUT_BY','system');
   	  }
   	  
   	  $data = array(
   	  	'name'=>Session::get('NAME')?Session::get('NAME'):'',
   	    'email'=>Session::get('EMAIL')?Session::get('EMAIL'):'',
   	    'photo'=>Session::get('PHOTO')?Session::get('PHOTO'):'',
   	    'redirect_by'=>Session::get('REDIRECT_URL')?Session::get('REDIRECT_URL'):url('user/logout'),
   	    'logout_by'=>Session::get('LOGOUT_BY')?Session::get('LOGOUT_BY'):'',
   	    '_token'=>$request->input('_token')	  
   	  );
   	  if(Session::get('LOGOUT_BY') == ''){
   	  	return redirect('user/logout');
   	  }
   	  if($request->isMethod('post')){
        $email = $request->has('myhidden')?base64_decode(trim($request->input('myhidden'))):'';
   	  	$password = $request->has('password')?$request->input('password'):"";
   	  	if(!empty($email) && !empty($password)){
   	  		$this->email = ($email);
   	  		$this->password = ($password);
   	  	    $url = $this->loginProcess($request->input('rurl'));
   	   	   if($url != '/'){
   	   	   	  return redirect($url);
   	   	   }else{
   	   	   	  return redirect()->back()->withInput();
   	   	   } 	  		
   	  	}else{
   	  		Session::put('SESSION_MESSAGE','Incorrect Password');
   	  		return redirect('/')->withInput();
   	  	}
   	  }
   	  return view('user.lockscreen',$data);
   }
   
   public function createsocialapp(Request $request){
   	 $id = $request->input('facebookid') =='' ? 0:$request->input('facebookid');
   	 if($id > 0){
   	 	$appinfo = AppInfo::where('id',$id)->first();
   	 	$appinfo->app_name = $request->input('facebookappname');
   	 	$appinfo->app_id = $request->input('facebookappid');
   	 	$appinfo->app_secrect = $request->input('facebookappsecrect');   	 	
   	 }else{
   	 	$appinfo  = new AppInfo;
   	 	$appinfo->app_name = $request->input('facebookappname');
   	 	$appinfo->app_id = $request->input('facebookappid');
   	 	$appinfo->app_secrect = $request->input('facebookappsecrect');
   	 	$appinfo->type = $request->input('apptype');
   	 	$appinfo->date_added  = $request->input('Y-m-d H:m:s');
   	 	$appinfo->owner = Session::get('USER_ID');
   	 }
   	 
   	 $appinfo->save();
   	 if($appinfo->id > 0){
   	 	Session::put('message',GenerateConfirmationMessage ( 'success', 'SocialApp has been successfully added.' ));
   	 }else{
   	 	Session::put('message',GenerateConfirmationMessage ( 'danger', 'SocialApp has not been successfully added.' ));
   	 }
   	 
   	 return redirect(url('user/connectsocialaccount'));
   }
   
   public function deleteapp(Request $request){
   	  $id = $request->input('app_id');
   	  $nums = AppInfo::where('id',$id)->update(array('remove'=>1));
   	  if($nums > 0){
   	  	Session::put('class','alert alert-success');
   	  	Session::put('message','<i class="entypo-info-circled"></i> App has successfully removed.');
   	  }else{
   	  	Session::put('class','alert alert-danger');
   	  	Session::put('message','<i class="entypo-cancel-circled"></i> App has not successfully removed.');
   	  }
   	  
   	  return redirect(url('user/connectsocialaccount'));
   }
   
   public function forgotpassword(Request $request){
   	  view()->share('action','forgotpassword');
   	  if($request->isMethod('post')){
   	  	if(!$request->has('email') || $request->input('email') == ''){
   	  		Session::put('status_message','<div class="alert alert-danger fade in" role="alert"><i class="fa fa-exclamation-circle"></i> Email address is required.</div>');
   	  	}else{
   	  		$email = $request->input('email');
   	  		$user = User::where('email_address',$email)->select('id','first_name','last_name')->first();
   	  		if($user){
   	  			$first_name = $user->first_name != ""?$user->first_name:'';
   	  			$last_name = $user->last_name !="" ? $user->last_name:'';
   	  			$full_name = $first_name ." ".$last_name;
   	  			$encoded_data  = urlencode(base64_encode($user->id .'&'.date('Y-m-d H:m:s').'&resetmypassword'));
   	  			$resetlink = url('user/resetpassword?token='.$encoded_data);
   	  			$site_title = Option::getOption('site_title');
   	  			$subject = $site_title." Password Reset Request";
   	  			$data = array('first_name'=>$first_name,'site_name'=>$site_title,'reset_link'=>$resetlink);
   	  			Mail::send('emails.resetpassword',$data,function($message) use($email,$subject,$full_name){
   	  				$message->to($email,$full_name)->subject($subject);
   	  			});
   	  			
   	  			Session::put('status_message','<div class="alert alert-success fade in" role="alert"><i class="fa fa-exclamation-circle"></i> Please check your email to reset your password.</div>'); 	  				
   	  			
   	  		}else{
   	  			Session::put('status_message','<div class="alert alert-danger fade in" role="alert"><i class="fa fa-exclamation-circle"></i> There were no matching user found.</div>');
   	  		}
   	  	}
   	  }
   	  return view('user.forgotpassword');
   }
   
   public function resetpassword(Request $request){
   	  view()->share('action','resetpassword');
   	  $token = $request->input('token') ? $request->input('token') :'';
   	  if($token != ""){
   	  	$secret_data = base64_decode(urldecode($token));
   	  	$data = explode("&",$secret_data);
   	  	$user_id = $data[0];
   	  	$link_create_date = $data[1];
   	  	$secret_data =  $data[2];
   	  	if($secret_data == "resetmypassword"){
   	  		$created_date  = strtotime($link_create_date);
   	  		$exp_date = strtotime(date('Y-m-d H:i:s'));
   	  		$current_date = strtotime(date('Y-m-d H:i:s'));
   	  		if($exp_date < $current_date){
   	  			Session::put('status_message','<div class="alert alert-danger fade in" role="alert"><i class="fa fa-exclamation-circle"></i> Requested link has been expired.</div>');
   	  			return redirect(url('user/error'));
   	  		}else{
   	  			if($request->has('act') && $request->input('act') =='resetpassword'){
   	  				if($request->has('password') && $request->input('password') != "" && $request->has('confirm_password') && $request->input('confirm_password') != ""){
   	  					if($request->input('password') == $request->input('confirm_password')){
   	  						$password = md5($request->input('password'));
   	  						$nums = User::where('id',$user_id)->update(array('password'=>$password));
   	  						if($nums > 0){
   	  							$login_link = url('/');
   	  							Session::put('status_message','<div class="alert alert-success fade in" role="alert"><i class="fa fa-exclamation-circle"></i> Your password has been successfully updated. <a href=' . $login_link . '>Login</a> with your new password.</div>');   	  							  							
   	  						}else{
   	  							Session::put('status_message','<div class="alert alert-danger fade in" role="alert"><i class="fa fa-exclamation-circle"></i> Your password has not been successfully updated.</div>');
   	  						}
   	  					}else{
   	  						Session::put('status_message','<div class="alert alert-danger fade in" role="alert"><i class="fa fa-exclamation-circle"></i> Password did not match.</div>');
   	  					}
   	  				}else{
   	  					Session::put('status_message','<div class="alert alert-danger fade in" role="alert"><i class="fa fa-exclamation-circle"></i> Both fields are required.</div>');
   	  				}
   	  			}
   	  		}
   	  	}else{
   	  		Session::put('status_message','<div class="alert alert-danger fade in" role="alert"><i class="fa fa-exclamation-circle"></i> Illegal request.</div>');
   	  		return redirect('uset/error');
   	  	}
   	  }else{
   	  	    Sessiom::put('status_message','<div class="alert alert-danger fade in" role="alert"><i class="fa fa-exclamation-circle"></i> Invalid request.</div>');
   	  	    return redirect('user/error');
   	  }
   	  
   	  return view('user.resetpassword');
   }
}























