<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SocialUser;
use App\Option;
use App\Campaign;
use App\Location;
require app_path().'/helper/helper.php';

class DashboardController extends Controller
{
	public function __construct(){
		view()->share('controller','dashboard');
		$this->middleware('auth');
		$this->middleware('boot');	
	}
    public function view(){ 	
    	view()->share('action','view');      	 	
    	$sql_social_user = "SELECT social_network AS social_media, IFNULL(COUNT(social_network_id),0) AS total_user, (SUM(`return` > 0)) AS returning FROM social_user ";
		$sql_location_map = "SELECT location, country FROM location WHERE `remove` = 0 ";
		$sql_gender = "SELECT SUM(`gender` = 'male') AS male, SUM(`gender` = 'female') AS female FROM social_user ";
		$sql_recent_connected = "SELECT su.social_user_id as id, IF( LENGTH( su.full_name ) <= 0,  'No Name', SUBSTRING_INDEX(su.full_name, ' ', 1) ) AS name, su.location_id, IF( LENGTH( l.name ) <= 0,  'N/A', l.name ) AS location, su.picture_url FROM social_user AS su LEFT JOIN location AS l ON su.location_id = l.id ";
		$sql_location_connections = "SELECT l.name, l.id AS location_id, SUM( su.social_network = 'FBuser' ) AS fb, SUM( su.social_network = 'LIuser' ) AS li, SUM( su.social_network = 'TWuser' ) AS tw, SUM( su.social_network = 'GPuser' ) AS gp, SUM( su.social_network = 'IGuser' ) AS ig, SUM( su.social_network = 'Cuser' ) AS cu, ( SUM( su.social_network = 'FBuser' ) + SUM( su.social_network = 'LIuser' ) + SUM( su.social_network = 'TWuser' ) + SUM( su.social_network = 'GPuser' ) + SUM( su.social_network = 'IGuser' ) + SUM( su.social_network = 'Cuser' ) ) AS total FROM social_user AS su INNER JOIN location AS l ON l.id = su.location_id  ";
    	
    	switch(Session::get('USER_TYPE')){
    		case '1':
    			$sql_social_user .= "GROUP BY social_network";
				$sql_location_map .= "GROUP BY country";
				$sql_gender .= "";
				$sql_recent_connected .= "ORDER BY su.social_user_id DESC LIMIT 12";
				$sql_location_connections .="GROUP BY su.location_id ORDER BY total DESC LIMIT 8";
				break;
    		case '2':
    			$l_ids = '';
    			$locations = DB::table('location')->where('remove',0)->where('owner',Session::get('USER_ID'))->select('id')->get();
    			if(count($locations) > 0){
	    			foreach($locations as $location){
	    				$l_ids.=$location->id.',';
	    			}
    			}
    			$location_ids = $l_ids != "" ? rtrim ( $l_ids, ',' ) : 0;
    			$sql_social_user .= "WHERE location_id IN($location_ids) GROUP BY social_network";
				$sql_location_map .= "AND id IN($location_ids) GROUP BY country";
				$sql_gender .= "WHERE location_id IN($location_ids)";
				$sql_recent_connected .= "WHERE su.location_id IN($location_ids) ORDER BY su.social_user_id DESC LIMIT 12";
				$sql_location_connections.= "WHERE su.location_id IN ( $location_ids ) GROUP BY su.location_id ORDER BY total DESC LIMIT 8";
				break;
    		case '3':
    			$location_ids = Session::get ( 'LOCATION_IDS' ) == null ? 0 : Session::get ( 'LOCATION_IDS' );
				$sql_social_user .= "WHERE location_id IN($location_ids) GROUP BY social_network";
				$sql_location_map .= "AND id IN($location_ids) GROUP BY country";
				$sql_gender .= "WHERE location_id IN($location_ids)";
				$sql_recent_connected .= "WHERE su.location_id IN($location_ids) ORDER BY su.social_user_id DESC LIMIT 12";
				$sql_location_connections.= "WHERE su.location_id IN ( $location_ids ) GROUP BY su.location_id ORDER BY total DESC LIMIT 8";
				break;		
    			
    	}
    	$fb_user = $tw_user = $gp_user = $li_user = $ig_user = $c_user = 0;
    	$fb_returning = $tw_returning = $li_returning = $gp_returning = $ig_returning = $c_returning = 0;
    	$result = DB::select(DB::raw($sql_social_user));
    	if(count($result) > 0){
	    	foreach($result as $row){
	    		switch($row->social_media){
	    			case 'FBuser':
	    				$fb_user = $row->total_user;
	    				$fb_returning = $row->returning;
	    				break;
	    			case 'GPuser':
	    				$gp_user = $row->total_user;
	    				$gp_returning = $row->returning;
	    				break;
	    			case 'LIuser':
	    				$li_user = $row->total_user;
	    				$li_returning = $row->returning;
	    				break;
	    			case 'TWuser':
	    				$tw_user = $row->total_user;
	    				$tw_returning = $row->returning;
	    				break;
	    			case 'IGuser':
	    				$ig_user = $row->total_user;
	    				$ig_returning = $row->returning;
	    				break;
	    			case 'Cuser':
	    				$c_user = $row->total_user;
	    				$c_returning = $row->returning;
	    				break;
	    		}
	    	}
    	}
    	$myData ['total_social_user'] = array (
				'fb' => $fb_user,
				'gp' => $gp_user,
				'li' => $li_user,
				'tw' => $tw_user,
				'ig' => $ig_user,
				'cuser' => $c_user 
		);
		
		$myData ['returning_user'] = array (
				'fb' => $fb_returning,
				'gp' => $gp_returning,
				'li' => $li_returning,
				'tw' => $tw_returning,
				'ig' => $ig_returning,
				'cuser' => $c_returning 
		);
		
		$temp_location = DB::select(DB::raw($sql_location_map));		
		$location_graph_data = '';
		if(count($temp_location) > 0){
			foreach($temp_location as $location){
				$location_graph_data .= "['" . $location->country . "', " . $location->location . "],";
			}		
			$location_graph_data = rtrim($location_graph_data,',');
		}		
		$myData['location_graph_data'] = $location_graph_data;
		
		$male = $female = 0;
		$data = DB::select(DB::raw($sql_gender));
		if(count($data) > 0){
			foreach ($data as $u){
				$male = $u->male != ""?$u->male:0;
				$female = $u->female != ""?$u->female:0;
			}
		}
		$myData['male_female'] = array(
			'male'=>$male,
		    'female'=>$female
		);
		
		$lastest_users = DB::select(DB::raw($sql_recent_connected));
		if(count($lastest_users) > 0){		
		    $myData['lastest_users'] = $lastest_users;
		}else{
			$myData['lastest_users'] = array();
		}
		
		$top_locations =  DB::select(DB::raw($sql_location_connections));
		if(count($top_locations) > 0){
		   $myData['top_locations'] = $top_locations;
		}else{
			$myData['top_locations']  =array();
		}
		return view('dashboard.view',$myData);
    }
    
    public function membersarea(Request $request){
    	view()->share('actions','membersarea');
    	$data = array();
    	$data['members_area_content'] = Option::getOption('members_area_content','0');
    	return view('dashboard.membersarea',$data);
    }
}
