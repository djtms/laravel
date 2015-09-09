<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppCampaignDetails extends Model
{
    protected $table ='app_campaign_details';
    
    protected $fillable = array('id','campaign_id', 'app_info_id', 'app_type');
    public $timestamps = false;
    public static function getByAppType($campaign_id,$app_type){
    	$record = AppCampaignDetails::where('campaign_id',$campaign_id)->where('app_type',$app_type)->first();
    	if($record){
    		return $record;
    	}else{
    		return null;
    	}
    }
}
