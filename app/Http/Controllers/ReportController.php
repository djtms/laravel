<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Device;
use App\Location;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;


class ReportController extends Controller
{
	public function __construct(){
		$this->middleware('auth');
		$this->middleware('boot');
		view()->share('controller','report');
	}
    public function analytic(Request $request){
    	view()->share('action','analytic');
    	$reset_option = "";
    	$data  = array();
		$data['location_all'] = Location::RetrieveLocationDropdown();
		$data['device_all'] = Device::RetrieveDeviceDropdownAll();
		$modules = explode ( ',', Session::get('MODULE_IDS'));
		$sort_by = array ();
		$sort_by [''] = "Sort by";
		if (in_array ( 'device', $modules )) {
			$sort_by ['device'] = 'Device';
		}
		if (in_array ( 'location', $modules )) {
			$sort_by ['location'] = 'Location';
		}
		$data['sort_by'] = $sort_by;		
		$location_id = "";
		$device_mac = "";
		
		$sql_returning_user = "SELECT `social_user_id`, `social_network`, `social_network_id`, `email`, SUBSTRING_INDEX(full_name, ' ', 1) AS full_name, `return`, `picture_url` from social_user WHERE `return` > 0 ";
		
		$sql_male_female = "SELECT DATE_FORMAT( added_datetime, '%Y-%m-%d' ) AS date, SUM( gender = 'male' ) AS male, SUM( gender = 'female' ) AS female FROM social_user WHERE social_network = 'FBuser'";
		
		$sql_statistics = "SELECT DATE_FORMAT( added_datetime, '%Y-%m-%d' ) AS `date`, SUM( social_network = 'FBuser' ) AS fb, SUM( social_network = 'LIuser' ) AS li, SUM( social_network = 'TWuser' ) AS tw, SUM( social_network = 'GPuser' ) AS gp, SUM( social_network = 'IGuser' ) AS ig, SUM( social_network = 'Cuser' ) AS cu FROM social_user";
		
		$sql_user = "SELECT SUM(`return` = 0) AS new_user, SUM(`return` > 0) AS returning_user, COUNT(*) AS total_user FROM social_user";
		
		$sql_online_user = "SELECT `user`, (TIME_TO_SEC(TIMEDIFF(NOW(),lastupdate))/60) AS connection_time FROM `radpostauth` ";
		
		$sql_campaign_statistics = "SELECT sudi.campaign_id, c.name, count(sudi.campaign_id) AS total_page_view, count(DISTINCT sudi.client_mac) AS unique_visitors, (count(sudi.campaign_id) - count(DISTINCT sudi.client_mac)) AS returning_users, SUM(sudi.suid = 0) AS bounce, CONCAT(cast((SUM(sudi.suid = 0)/count(sudi.campaign_id))*100 as decimal(10,2)),'%') AS bounce_rate FROM `social_user_device_info` AS sudi INNER JOIN `campaign` AS c ON sudi.campaign_id = c.id WHERE c.remove = 0 ";
		
		if ($request->isMethod('post')) {			
			
			$start_date = $request->input('hdn_from_date');
			$end_date = $request->input('hdn_to_date');
			$location_id = $request->has('location_id') ? $request->input('location_id') : 0;
			$device_mac = $request->has('device_mac') ? $request->input('device_mac'): "";
			
			Session::put('sort-by',$request->input('sort-by'));
			Session::put('location_id', $location_id);
			Session::put('device_mac', $device_mac);
			Session::put('start_date',$start_date);
			Session::put('end_date', $end_date);
			
			Session::put('statistics',array ());
			Session::put('fb_male_female',array ());
			Session::put('user', array ());
			
			if ($request->input('sort-by') == "location") {
				
				if($location_id > 0){
					$reset_option .= "<br><div class='row'><div class='col-md-12'>";
					$reset_option .= "<button type='button' onclick='javascript:statisticsDeleteConfirmation(&#39;location&#39;, &#39;".$location_id."&#39;); 'class='btn btn-block btn-danger'>Reset Location Statistics</button>";
					$reset_option .= "</div></div>";
				}
				
				$nasid = Location::getNasId($location_id,'');
				
				$sql_returning_user .= " AND location_id = '$location_id' AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' ORDER BY `added_datetime` DESC LIMIT 12";
				
				$sql_male_female .= " AND location_id = '$location_id' AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY date( added_datetime ) ORDER BY DATE(added_datetime) DESC";
				
				$sql_statistics .= " WHERE location_id = '$location_id' AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY DATE( added_datetime ) ORDER BY DATE(added_datetime) ASC";
				
				$sql_user .= " WHERE location_id = '$location_id' AND date(added_datetime) BETWEEN '$start_date' AND '$end_date'";
				
				$sql_online_user .= "WHERE `Nas_Id` = '$nasid' ORDER BY `id` DESC";
				
				$sql_campaign_statistics .= " AND sudi.location_id = '$location_id' AND date(sudi.created_at) BETWEEN '$start_date' AND '$end_date' GROUP BY sudi.campaign_id ORDER BY DATE(sudi.created_at) DESC LIMIT 15";
				
			} elseif ($request->input('sort-by') == "device") {
				
				if($device_mac != ""){
					$reset_option .= "<br><div class='row'><div class='col-md-12'>";
					$reset_option .= "<button type='button' onclick='javascript:statisticsDeleteConfirmation(&#39;device&#39;, &#39;".$device_mac."&#39;); 'class='btn btn-block btn-danger'>Reset Device Statistics</button>";
					$reset_option .= "</div></div>";
				}
				
				// $mac = trimDeviceMac ( $device_mac );
				
				$nasid = Location::getNasId( "", $device_mac );
				
				$sql_returning_user .= " AND device_mac = '$device_mac' AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' ORDER BY `added_datetime` DESC LIMIT 12";
				
				$sql_male_female .= " AND device_mac = '$device_mac' AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY date( added_datetime ) ORDER BY DATE(added_datetime) DESC";
				
				$sql_statistics .= " WHERE device_mac = '$device_mac' AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY DATE( added_datetime ) ORDER BY DATE(added_datetime) ASC";
				
				$sql_user .= " WHERE device_mac = '$device_mac' AND date(added_datetime) BETWEEN '$start_date' AND '$end_date'";
				
				$sql_online_user .= "WHERE `Nas_Id` = '$nasid' ORDER BY `id` DESC";
				
				$sql_campaign_statistics .= " AND sudi.device_mac = '$device_mac' AND date(sudi.created_at) BETWEEN '$start_date' AND '$end_date' GROUP BY sudi.campaign_id ORDER BY DATE(sudi.created_at) DESC LIMIT 15";
			} else {
				if (Session::get('USER_TYPE') == '1') {
					$sql_returning_user .= " AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' ORDER BY `added_datetime` DESC LIMIT 12";
					$sql_male_female .= " AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY date( added_datetime ) ORDER BY DATE(added_datetime) DESC";
					$sql_statistics .= " WHERE date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY DATE( added_datetime ) ORDER BY DATE(added_datetime) ASC";
					$sql_user .= " WHERE date(added_datetime) BETWEEN '$start_date' AND '$end_date'";
					$sql_online_user .= " WHERE 1=1 ORDER BY `id` DESC";
					$sql_campaign_statistics .= " AND date(sudi.created_at) BETWEEN '$start_date' AND '$end_date' GROUP BY sudi.campaign_id ORDER BY DATE(sudi.created_at) DESC LIMIT 15";
				} elseif (Session::get('USER_TYPE') == '2') {
					$sql = "SELECT GROUP_CONCAT( id ) AS location_ids, GROUP_CONCAT( identifier ) AS nasids FROM location WHERE `remove` = 0 AND owner = ".Session::get('USER_ID');
					$result = DB::select(DB::raw($sql));
					$location_ids = $nasids = "'0'";
					if (count($result) > 0) {						
						$location_ids = ($result[0]->location_ids  && $result[0]->location_ids != "") ? $result[0]->location_ids : 0;
						$nasids = ($result[0]->nasids  && $result[0]->nasids != "") ? $this->addQuotes ( $result[0]->nasids ) : "'0'";
					}
					$sql_returning_user .= " AND location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' ORDER BY `added_datetime` DESC LIMIT 12";
					$sql_male_female .= " AND location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY date( added_datetime ) ORDER BY DATE(added_datetime) DESC";
					$sql_statistics .= " WHERE location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY DATE( added_datetime ) ORDER BY DATE(added_datetime) ASC";
					$sql_user .= " WHERE location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date'";
					$sql_online_user .= " WHERE `Nas_Id` IN( $nasids ) ORDER BY `id` DESC";
					$sql_campaign_statistics .= " AND sudi.location_id IN($location_ids) AND date(sudi.created_at) BETWEEN '$start_date' AND '$end_date' GROUP BY sudi.campaign_id ORDER BY DATE(sudi.created_at) DESC LIMIT 15";
				} elseif (Session::get('USER_TYPE') == '3') {
					$location_ids = Session::get ('LOCATION_IDS') == null ? 0 : Session::get ('LOCATION_IDS');
					$sql = "SELECT GROUP_CONCAT( identifier ) AS nasids FROM location WHERE `remove` = 0 AND id IN ($location_ids)";
					$query = DB::select(DB::raw($sql));
					$nasids = "'0'";
					if (count($query)> 0) {
						$result = $query[0];
						$nasids = ($result->nasids  && $result->nasids != "") ? $this->addQuotes ( $result->nasids ) : "'0'";
					}
					$sql_returning_user .= " AND location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' ORDER BY `added_datetime` DESC LIMIT 12";
					$sql_male_female .= " AND location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY date( added_datetime ) ORDER BY DATE(added_datetime) DESC";
					$sql_statistics .= " WHERE location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY DATE( added_datetime ) ORDER BY DATE(added_datetime) ASC";
					$sql_user .= " WHERE location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date'";
					$sql_online_user .= " WHERE `Nas_Id` IN( $nasids ) ORDER BY `id` DESC";
					$sql_campaign_statistics .= " AND sudi.location_id IN($location_ids) AND date(sudi.created_at) BETWEEN '$start_date' AND '$end_date' GROUP BY sudi.campaign_id ORDER BY DATE(sudi.created_at) DESC LIMIT 15";					
				}
			}
		} else {
			$start_date = date ( 'Y-m-d', strtotime ( "-7 days", time () ) );
			$end_date = date ( 'Y-m-d' );
			
			if (Session::get('USER_TYPE') == '1') {
				$sql_returning_user .= " AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' ORDER BY `added_datetime` DESC LIMIT 12";
				$sql_male_female .= " AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY date( added_datetime ) ORDER BY DATE(added_datetime) DESC";
				$sql_statistics .= " WHERE date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY DATE( added_datetime ) ORDER BY DATE(added_datetime) ASC";
				$sql_user .= " WHERE date(added_datetime) BETWEEN '$start_date' AND '$end_date'";
				$sql_online_user .= " WHERE 1=1 ORDER BY `id` DESC";
				$sql_campaign_statistics .= " AND date(sudi.created_at) BETWEEN '$start_date' AND '$end_date' GROUP BY sudi.campaign_id ORDER BY DATE(sudi.created_at) DESC LIMIT 15";
			} else if (Session::get('USER_TYPE') == '2') {
				$sql = "SELECT GROUP_CONCAT( id ) AS location_ids, GROUP_CONCAT( identifier ) AS nasids FROM location WHERE 'remove' = 0 AND owner = ".Session::get('USER_ID');
				
				$query = DB::select(DB::raw($sql));
				
				$location_ids = 0; $nasids = "'0'";
				if (count($query)>0) {
					$result = $query[0];					
					$location_ids = ( $result->location_ids  && $result->location_ids != "") ? $result->location_ids : 0;
					$nasids = ( $result->nasids  && $result->nasids != "") ? $this->addQuotes ( $result->nasids ) : "'0'";
				}
				$sql_returning_user .= " AND location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' ORDER BY `added_datetime` DESC LIMIT 12";
				$sql_male_female .= " AND location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY date( added_datetime ) ORDER BY DATE(added_datetime) DESC";
				$sql_statistics .= " WHERE location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY DATE( added_datetime ) ORDER BY DATE(added_datetime) ASC";
				$sql_user .= " WHERE location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date'";
				$sql_online_user .= " WHERE `Nas_Id` IN( $nasids ) ORDER BY `id` DESC";
				$sql_campaign_statistics .= " AND sudi.location_id IN($location_ids) AND date(sudi.created_at) BETWEEN '$start_date' AND '$end_date' GROUP BY sudi.campaign_id ORDER BY DATE(sudi.created_at) DESC LIMIT 15";
			} else if (Session::get('USER_TYPE') == '3') {
				$location_ids = Session::get ('LOCATION_IDS') == null ? 0 : Session::get ('LOCATION_IDS');
				$sql = "SELECT GROUP_CONCAT( identifier ) AS nasids FROM location WHERE `remove` = 0 AND id IN ($location_ids)";
				$query = DB::select(DB::raw($sql));
				$nasids = "'0'";
				if (count($query) > 0) {
					$result = $query[0];
					$nasids = ($result->nasids  && $result->nasids != "") ? $this->addQuotes ( $result->nasids ) : "'0'";
				}
				$sql_returning_user .= " AND location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' ORDER BY `added_datetime` DESC LIMIT 12";
				$sql_male_female .= " AND location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY date( added_datetime ) ORDER BY DATE(added_datetime) DESC";
				$sql_statistics .= " WHERE location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date' GROUP BY DATE( added_datetime ) ORDER BY DATE(added_datetime) ASC";
				$sql_user .= " WHERE location_id IN($location_ids) AND date(added_datetime) BETWEEN '$start_date' AND '$end_date'";
				$sql_online_user .= " WHERE `Nas_Id` IN( $nasids ) ORDER BY `id` DESC";
				$sql_campaign_statistics .= " AND sudi.location_id IN($location_ids) AND date(sudi.created_at) BETWEEN '$start_date' AND '$end_date' GROUP BY sudi.campaign_id ORDER BY DATE(sudi.created_at) DESC LIMIT 15";
			}
		}		
		$returning_user = DB::select(DB::raw($sql_returning_user));
		$output  = '';
		if(count($returning_user) > 0){
			foreach ($returning_user as $row){
				$output .= '<div class="col-md-3 text-center">
								<a href="javascript:GetSocialUserDetail(' . $row->social_user_id . ');">
									<p><img style="width: 50px;" src="' . $row->picture_url . '" alt="profile-picture" class="img-circle thumbnail img-responsive img-center"><!--<i class="media-icon" style=""></i>--></p>
									<p>' . $row->return . ' Times <br>' . $row->full_name . '</p>
								</a>
							</div>';
			}
		}else{
			$output .= '<div class="col-md-10 col-md-offset-1"> <div class="alert alert-warning"><strong><i class="fa fa-frown-o"></i> No users found.</strong></div></div>';
		}		
		$data['user'] = array();
		$data['user']['returning_user'] = $output;
		
		$male_female = DB::select(DB::raw($sql_male_female));
		$graph_data = "{x: 'No Data Found(s)', a: 0, b: 0},";
		$male = $female = 0;
		$output = array();
		if(count($male_female) > 0){
			foreach ($male_female as $row){
				$output[] = $row;
				$male  = $male + $row->male;
				$female = $female + $row->female;
			}
		}
		if(count($output) > 0){
			$graph_data = '';
			$date_diff = date_diff(date_create($start_date), date_create($end_date))->format("%r%a");
			for($i = 0;$i <= $date_diff;$i++){
				$m = $f = 0;
				$my_date =date('Y-m-d',strtotime($start_date.' + '.$i.' days'));
				foreach ($output as $value){
					$executed_data = $value->date && $value->date != "" ? $value->date :'';
				    if (strtotime ( $executed_data ) == strtotime ( $my_date )) {
						$m = $value->male != "" ? $value->male : "0";
						$f = $value->female != "" ? $value->female : "0";
					}
				}
				$graph_data .= "{x: " . "'" . date('d M', strtotime($my_date)) . "'" . ", a: " . $m . ", b: " . $f . "},";
			}
		}
		$data ['fb_male_female'] = array();
		$data ['fb_male_female'] ['total_fb_male'] = $male;
		$data ['fb_male_female'] ['total_fb_female'] = $female;
		$data ['fb_male_female'] ['total_male_female_graph_data'] = $graph_data;
		
		
		$statistics = DB::select(DB::raw($sql_statistics));
		$graph_data = "{y: 'No Data Found(s)', a: 0, b: 0, c: 0, d: 0, e: 0, f: 0},";
		$fbuser = $twuser = $liuser = $gpuser = $iguser = $customuser = 0;
		$output = array ();
		if(count($statistics) > 0){
			foreach ($statistics as $row){
				$output [] = $row;
				$fbuser = $fbuser + $row->fb;
				$twuser = $twuser + $row->tw;
				$liuser = $liuser + $row->li;
				$gpuser = $gpuser + $row->gp;
				$iguser = $iguser + $row->ig;
				$customuser = $customuser + $row->cu;
			}
		}
		
		if (count ( $output ) > 0) {
			$graph_data = "";
			$date_diff = date_diff ( date_create ( $start_date ), date_create ( $end_date ) )->format ( "%r%a" );
			for($i = 0; $i <= $date_diff; $i ++) {
				$fb = $tw = $li = $gp = $ig = $cu = 0;
				$my_date = date ( 'Y-m-d', strtotime ( $start_date . ' + ' . $i . ' days' ) );
				foreach ( $output as $value ) {
					$executed_data = $value->date && $value->date != "" ? $value->date : "";
					if (strtotime ( $executed_data ) == strtotime ( $my_date )) {
						$fb = $value->fb != "" ? $value->fb : "0";
						$tw = $value->tw != "" ? $value->tw : "0";
						$li = $value->li != "" ? $value->li : "0";
						$gp = $value->gp != "" ? $value->gp : "0";
						$ig = $value->ig != "" ? $value->ig : "0";
						$cu = $value->cu != "" ? $value->cu : "0";
					}
				}
				$graph_data .= "{y: " . "'" . $my_date . "'" . ", a: " . $fb . ", b: " . $tw . ", c: " . $li . ", d: " . $gp . ", e: " . $ig . ", f: " . $cu . "},";
			}
		}
		$data ['statistics'] = array();
		$data ['statistics'] ['graph_data'] = $graph_data;
		
		$data ['statistics'] ['fbuser'] = $fbuser;
		$data ['statistics'] ['twuser'] = $twuser;
		$data ['statistics'] ['liuser'] = $liuser;
		$data ['statistics'] ['gpuser'] = $gpuser;
		$data ['statistics'] ['iguser'] = $iguser;
		$data ['statistics'] ['cuser'] = $customuser;		
		
		
		$users = DB::select(DB::raw( $sql_user));
		$total_user = $new_user = $returning_user = 0;		
		if(count($users) > 0){
			$data1 = $users[0];
			$total_user = $data1->total_user;
			$new_user = $data1->new_user;
			$returning_user = $data1->returning_user;
		}	
			
		$data ['user'] ['total_user'] = $total_user;
		$data ['user'] ['new_user'] = $new_user;
		$data ['user'] ['total_returning_user'] = $returning_user;
		
		
		/**
		 * Create Connection to radius server
		 */
		$con = DB::connection('radius');
		if($con){
			$online_user = $con->select(DB::raw($sql_online_user));
			
			$usage_time = 0;
			$avg = 0;
			$online_users = "";
			$total_user = count($online_user);
			if(count($online_user)){
				foreach($online_user as $row){
					$sql = "SELECT `social_user_id`, `social_network`, SUBSTRING_INDEX(`full_name`, ' ', 1) AS full_name, `picture_url` FROM `social_user` WHERE `user_id` = '" . $row->user . "' LIMIT 1";
					$user_data = DB::select(DB::raw( $sql ));
					if (count($user_data)>0) {
						$data1 = $user_data[0];
						
						$online_users .= '<div class="col-md-3 text-center">
					               		<a href="javascript:GetSocialUserDetail(' . $data1->social_user_id . ');">
						               		<p><img style="width: 50px;" src="' . $data1->picture_url . '" alt="profile-picture" class="img-circle thumbnail img-responsive img-center"></p>
						               		<p>' . $data1->full_name . '</p>
					               		</a>
				               		</div>';
					}
					$usage_time += $row->connection_time;
				}
				
				if ($usage_time > 0 && $total_user > 0) {
					$var = $usage_time / $total_user;
					$avg = ( float ) number_format ( $var, 2, '.', '' );
				}
			}else{
				$online_users = '<div class="col-md-10 col-md-offset-1"> <div class="alert alert-warning"><strong><i class="fa fa-frown-o"></i> No users are online now.</strong></div></div>';
			}
			
			$data ['user'] ['online_uesrs'] = $online_users;
			$data ['user'] ['total_online_uesrs'] = $total_user;
			$data ['user'] ['avg'] = $avg;
		}else{
			echo "no connection";
			$data ['user'] ['online_uesrs'] = '<div class="col-md-10 col-md-offset-1"><div class="alert alert-warning"><strong><i class="entypo-attention"></i> Could not connect to Radius server.</strong></div></div>';
			$data ['user'] ['total_online_uesrs'] = 0;
			$data ['user'] ['avg'] = 0;
		}
		
		
		$campaign_statistics = "{x: 'No Data Found(s)', y: 0, z: 0, a: 0},";
		
		$query = DB::select(DB::raw($sql_campaign_statistics));
		if(count($query) > 0){
			$campaign_statistics = "";
			foreach ($query as $row){
				$campaign_statistics .= "{x: '".str_replace(array('\'', '"'), '', $row->name)."', y: '".$row->total_page_view."', z: '".$row->unique_visitors."', a: '".$row->returning_users."'},";
			}
		}
		$data ['campaign_statistics'] = utf8_encode($campaign_statistics);
		$data['reset_option'] = $reset_option;
		
		return view('report.analytic',$data);
    }
    
    function addQuotes($string) {
		return '"' . implode ( '","', explode ( ',', $string ) ) . '"';
	}	
    
	public function userlist(){
		view()->share('action','list');
		$data = array();
		
		return view('report.list',$data);
	}
}                                                                                                                                                                                                                                                                  
