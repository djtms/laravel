<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Config;

use Closure;
use App\Option;
use Illuminate\Support\Facades\Session;
use App\Location;
use App\Campaign;

class BootMiddle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	view()->share('title',Option::getOption('site_title','','MyWiFi Demo'));
		view()->share('footer',Option::getOption('footer'));  
		  
        $location_list = Location::select('id','name')->where('owner',Session::get('USER_ID'))->where('status',1)->where('remove',0)->get();
		if(!$location_list){			
			$location_list = array();
		}		
		$campaigns = Campaign::select('id','name')->where('owner',Session::get('USER_ID'))->where('status',1)->where('remove',1)->get();
		if(!$campaigns){		
			$campaigns = array();
		}			
	    view()->share('menudata',array(
			'menu_background_color'=>Option::getOption('menu_background_color','','#303641'),
			'menu_text_color'=>Option::getOption('menu_text_color','','#aaabae'),
			'menu_background_hover_color'=>Option::getOption('menu_background_hover_color','','#303641'),
			'menu_text_hover_color'=>Option::getOption('menu_text_hover_color','','#aaabae'),
			'location_list'=>$location_list,
	    
			'campaigns'=>$campaigns,
			'modules'=>explode(',', Session::get('MODULES_IDS')),
	        'module_array'=>array (									
								'device' => 'Devices',
								'location' => 'Locations',
								'campaign' => 'Campaigns',									
								'report' => 'Analytics & Reports',
								'social_app' => 'Connect Social Accounts',
								'subuser' => 'Sub Users')			
	     ));

	     $logo = Option::getOption('logo');
	     
		 view()->share('logo',$logo == ''? Config::get('aws.AWS_CDN').Config::get('aws.ASSETS').'/images/logo-light.png':$logo);  
		 $asset_dir =  rtrim(Config::get('aws.AWS_CDN').Config::get('aws.ASSETS'),'/');
		 view()->share('assets_dir',$asset_dir);
        return $next($request);
    }
}
