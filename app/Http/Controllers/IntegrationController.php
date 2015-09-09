<?php

namespace App\Http\Controllers;

use App\Option;
use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
require_once app_path().'/helper/helper.php';

class IntegrationController extends Controller
{
    public function __construct(){
    	view()->share('controller','integration');
    	$this->middleware('auth');
    	$this->middleware('boot');
    }
    
    public function view(){
    	view()->share('actions','view');
    	$data  = array();
    	$getresponse = Option::getOption('getresponse');
		$icontact = Option::getOption('icontact');
		$mailchimp = Option::getOption('mailchimp');
		$sendreach = Option::getOption('sendreach');
		$activecampaign = Option::getOption('activecampaign');
		$data  = array('getresponse' =>array(
										'api_key'=>''
										),
					   'icontact'=>array(
										'api_key'=>'',
										'username'=>'',
										'password'=>''
										),
					   'mailchimp'=>array(
										'api_key'=>''
										),
					   'sendreach'=>array(
										'api_key'=>'',
										'secret'=>'',
										'userid'=>''
										),
					   'activecampaign'=>array(
										'api_key'=>'',
										'url'=>''
										)
				);
		
		if($getresponse != ''){
			$gp_data = json_decode($getresponse);
			$data['getresponse']['api_key'] = $gp_data->getresponse->api_key;
		}
		if($icontact != ''){
			$it_data = json_decode($icontact);
			$data['icontact']['api_key'] = $it_data->icontact->api_key;
			$data['icontact']['username'] = $it_data->icontact->username;
			$data['icontact']['password'] = $it_data->icontact->password;
		}
		if($mailchimp !=''){
			$mc_data = json_decode($mailchimp);
			$data['mailchimp']['api_key']  = $mc_data->mailchimp->api_key;
		}
		if($sendreach !=''){
			$sr_data = json_decode($sendreach);
			$data['sendreach']['api_key'] = $sr_data->sendreach->api_key;
			$data['sendreach']['secret'] = $sr_data->sendreach->secret;
			$data['sendreach']['userid'] = $sr_data->sendreach->userid;
		}
		if($activecampaign !=''){
			$ac_data = json_decode($activecampaign);
			$data['activecampaign']['api_key'] = $ac_data->activecampaign->api_key;
			$data['activecampaign']['url'] = $ac_data->activecampaign->url;
		}				
		return view('integration.view',$data);
    }
    
    public function savegetresponseapiinformation(Request $request){
    	$getresponse = array(
            'getresponse' => array(
                'api_key' => trim( strip_tags( $request->input('gp_api_key')) )
            )
        );
        Option::addOption('getresponse', json_encode($getresponse));
        $message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> API settings has been successfully saved.', true);
        Session::put('SESSION_MESSAGE', $message);  
        return redirect(url('integration/view'));
    }
    
    public function saveicontactapiinformation(Request $request){
    	$icontact = array(
            'icontact' => array(
                'api_key' => trim( strip_tags( $request->input('ic_api_key'))),
                'username' => trim( strip_tags( $request->input('ic_username'))),
                'password' => trim( strip_tags( $request->input('ic_password')))
            )
        );
        Option::addOption('icontact', json_encode($icontact));
        $message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> API settings has been successfully saved.', true);
        Session::put('SESSION_MESSAGE', $message);  
        return redirect(url('integration/view'));
    }
    
    function savemailchimpapiinformation(Request $request){
        $mailchimp = array(
            'mailchimp' => array(
                'api_key' => trim( strip_tags( $request->input('mc_api_key')) )
            )
        );
        Option::addOption('mailchimp', json_encode($mailchimp));
        $message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> API settings has been successfully saved.', true);
        Session::put('SESSION_MESSAGE', $message);  
        return redirect(url('integration/view'));
    }
    
     function savesendreachapiinformation(Request $request){
        $sendreach = array(
            'sendreach' => array(
                'api_key' => trim( strip_tags( $request->input('sr_api_key'))),
                'secret' => trim( strip_tags( $request->input('sr_secret'))),
                'userid' => trim( strip_tags( $request->input('sr_userid')))
            )
        );
        Option::addOption('sendreach', json_encode($sendreach));
        $message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> API settings has been successfully saved.', true);
        Session::put('SESSION_MESSAGE', $message);  
        return redirect(url('integration/view'));
    }
    
    function saveactivecampaignapiinformation(Request $request){
    	$activecampaign = array(
    			'activecampaign' => array(
    					'api_key' => trim( strip_tags( $request->input('ac_api_key'))),
    					'url' => trim( strip_tags( $request->input('ac_url')))
    			)
    	);
    	Option::addOption('activecampaign', json_encode($activecampaign));
    	$message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> API settings has been successfully saved.', true);
        Session::put('SESSION_MESSAGE', $message);  
        return redirect(url('integration/view'));
    }    
     function deleteintegration(Request $request){
    	$integration = $request->input('hdn_integration');
    	if(Option::deleteOption($integration)){
    		$message = GenerateConfirmationMessage('success', '<i class="fa fa-info-circle"></i> API settings has been successfully deleted.', true);
    	}else{
    		$message = GenerateConfirmationMessage('danger', '<i class="fa fa-times-circle"></i> API settings has not been successfully deleted.', true);
    	}
    	Session::put('SESSION_MESSAGE', $message);
    	return redirect(url('integration/view'));
    }
}
