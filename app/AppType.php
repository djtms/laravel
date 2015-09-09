<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppType extends Model
{
    protected $table = 'app_type';
    protected $fillable = array('id','app_type');
    public $timestamps = false;
    public static function getAppType($id){
    	$result = AppType::find($id);
    	if($result){
    		return $result->app_type;
    	}else{
    		return null;
    	}
    }
}
