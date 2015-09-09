<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocationMeta extends Model
{
    protected $table = 'location_meta';
    
    protected $fillable = array('id','location_id','meta_key','meta_value');
    public $timestamps = false;
    
    public static function addLocationMeta($location_id,$meta_key,$meta_value){
    	$row = LocationMeta::where('location_id',$location_id)->where('meta_key',$meta_key)->first();
    	if($row){
    		$row->meta_value = $meta_value;
    		$row->save();
    	}else{
    		$row = new LocationMeta;
    		$row->location_id = $location_id;
    		$row->meta_key = $meta_key;
    		$row->meta_value = $meta_value;
    		$row->save();
    	}
    }
    
    public static function getLocationMeta($location_id,$meta_key,$default=''){
    	$output = $default;
    	$row = LocationMeta::where('location_id',$location_id)->where('meta_key',$meta_key)->first();
    	if($row){
    		$output = $row->meta_value;
    	}
    	
    	return $output;
    }
}
