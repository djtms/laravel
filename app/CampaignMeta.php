<?php

namespace App;

use App\Http\Controllers\CampaignController;

use Illuminate\Database\Eloquent\Model;

class CampaignMeta extends Model
{
    protected $table = 'campaign_meta';
    protected $fillable = array('id','campaign_id','meta_key','meta_value');
    public $timestamps = false;
    public static function addCampaignMeta($campaign_id,$meta_key,$meta_value){
    	$record = CampaignMeta::where('campaign_id',$campaign_id)->where('meta_key',$meta_key)->first();
    	if($record){
    		$record->meta_value = $meta_value;
    		$record->save();
    	}else{
    		$record = new CampaignMeta;
    		$record->campaign_id = $campaign_id;
    		$record->meta_key  = $meta_key;
    		$record->meta_value = $meta_value;
    		$record->save();
    	}
    }
    
    public static function getCampaignMeta($campaign_id,$meta_key =''){
    	$output = NULL;
    	$records = CampaignMeta::where('campaign_id',$campaign_id);
    	if($meta_key != ''){
    		$records = $records->where('meta_key',$meta_key);
    	}    	
    	$records = $records->get();
    	if(count($records) > 0){
    		if(count($records) == 1){
    			return $records->first()->meta_value;
    		}else{
    			$campaign_meta =  array();
    			foreach($records as $record){
    				$campaign_meta[$record->meta_key] = $record->meta_value;
    			}
    			return $campaign_meta;
    		}    		
    	}else{
    		return $output;
    	}
    }
    
}
