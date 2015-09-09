<?php

namespace App;

use Illuminate\Support\Facades\Session;
use DB;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'campaign';
    protected $fillable = array('id','name','ssid','login_type','location_id','status',
                           'language','text_color','background_color','background_image',
    					   'header_html','footer_html','custom_term','success_login_url',
    					   'thankyou_page','auto_email','auto_post','subject','sender_name',
    					   'sender_email','message','picture_url','date_create','last_modifie',
    					   'post_status','fields_email','check_custom_term','check_thank_you_page',
    					   'owner');

    public $timestamps = false;
    public static function RetrieveCampaignDropdown(){
    	$result = Campaign::select('id','name');
    	if(Session::get('USER_TYPE') != null && Session::get('USER_TYPE') == '1'){
    		$result = $result->where('remove',0)->orderBy('name','desc')->get();
    	}elseif(Session::get('USER_TYPE') != null && Session::get('USER_TYPE') == '2'){
    		$result = $result->where('remove',0)->where('owner',Session::get('USER_ID'))->orderBy('name','desc')->get();
    	}elseif(Session::get('USER_TYPE') && Session::get('USER_TYPE') == '3'){
    		$result = $result->where('remove',0)->where('owner',Session::get('USER_CREATED_BY'))->orderBy('name','desc')->get();
    	}
    	if($result){
    		return $result;
    	}else{
    		return array();
    	}
    }
    
    
    public static function GetActiveCampaign($location_id){
    	$result = DB::table('campaign as c')->join('location_schedule as ls','ls.campaign_id','=','c.id')->select('c.*')->where('ls.location_id',$location_id)->where('is_default','>',0)->first();    	
    	if($result){
    		return $result;
    	}
    	return null;
    }
}
