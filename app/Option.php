<?php

namespace App;

use Illuminate\Support\Facades\Session;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'options';
    protected $fillable = array('option_id','site_id','option_name','option_value');
    public $timestamps = false;
    
    public static function getOption($name,$site_id='',$default=''){
    	$output = $default;
    	if($site_id == ''){
    		$site_id = Session::get('SITE_ID')?Session::get('SITE_ID'):0;    		
    	}
    	
    	$record = Option::where('site_id',$site_id)->where('option_name',$name)->first();
    	if($record){
    		$output = $record->option_value;
    	}
    	return $output;
    }
    
    public static function addOption($name,$value,$site_id=''){
    	if($site_id == ''){
    		$site_id = Session::get('SITE_ID')?Session::get('SITE_ID'):0;
    	}
    	$nums = Option::where('site_id',$site_id)->where('option_name',$name)->update(array('option_value'=>$value));
    	if($nums <= 0){    	
	    	$record = new Option();
	    	$record->option_name = $name;
	    	$record->option_value = $value;
	    	$record->site_id = $site_id;
	    	$record->save();   
    	} 	
    }
    
    public static function deleteOption($name){
    	$site_id = 0;
    	if(Session::get('SITE_ID') && Session::get('SITE_ID') != ''){
    		$site_id = Session::get('SITE_ID');
    	}
    	Option::where('site_id',$site_id)->where('option_name',$name)->delete();    	
    }
}
