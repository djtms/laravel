<?php

namespace App\Http\Controllers;

use App\SubScriptionDetail;

use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use stdClass;
use DB;
require_once app_path().'/helper/helper.php';

class AdminController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    	$this->middleware('boot');
    	view()->share('controller','admintools');    	
    }
    
    public function activitylog(Request $request){
    	view()->share('action','activitylog');
    	$data = array();
    	$data['search_value'] = null;
    	$data['limit'] = 10;
    	$data['operator'] = null;
    	$data['order'] = null;
    	$temp  =  new stdClass();
    	$temp->status = 'error';
    	$temp->message = 'No data found';
    	$device_info = $temp;
    	$device_status_info = $temp;
    	$radius_device_info = $temp;
    	$social_user_device_info = $temp;
    	
    	if($request->isMethod('post')){    		
    		$validation_error = '';
    		if($request->input('search_value') == ''){
    			$validation_error.="You did not enter any value to search.";
    		}
    		
    		if($validation_error == ''){
    			$device_info = null;
    			$device_status_info = null;
    			$radius_device_info = null;
    			$social_user_device_info  =  null;
    			$data['search_value'] = $request->input('search_value');
    			$data['limit'] =$request->input('limit');
    			$data['operator'] = $request->input('operator');
    			$data['order'] =$request->input('order');
    			
    			$search_value = trim($request->input('search_value'));
    			$order = trim($request->input('order'));
    			$limit = trim($request->input('limit'));
    			
    			$sql_device_info = "SELECT u.full_name, u.email_address, d.name AS device_name, d.mac_address, l.name AS location_name, l.id AS location_id, l.identifier, d.create_date FROM `device` AS d
						LEFT JOIN `user` AS u ON d.owner = u.id
						LEFT JOIN `location` AS l ON d.location_id = l.id
						WHERE ";
				
				$sql_device_status_info = "SELECT ds.mac, ds.mac1, ds.mac2, ds.mac3, ds.mac4, ds.device_status_details, 
						DATE_FORMAT(ds.status_created_on,'%d %b %Y @ %H:%i:%s') AS status_created_on FROM `device_status` AS ds WHERE ";
				
				$exception_mac = str_replace ( "-", ":", $search_value );
				$sql_radius = "SELECT `info_details`, `created_on` FROM `temp` WHERE ";
				
				$sql_social_user_device_info = "SELECT c.name AS campaign, l.name AS location, sudi.plan, sudi.os_name, sudi.model, sudi.client_mac, sudi.created_at 
						FROM `social_user_device_info` AS sudi
						LEFT JOIN `campaign` AS c ON sudi.campaign_id = c.id
						LEFT JOIN `location` AS l ON sudi.location_id = l.id WHERE ";
				switch($request->input('operator')){
					case '=' :
						$sql_device_info .= "d.mac_address = '$search_value' ORDER BY d.id $order LIMIT 0, $limit";
						$sql_device_status_info .= " ds.mac = '$search_value' OR ds.mac1 = '$search_value' OR ds.mac2 = '$search_value' OR ds.mac3 = '$search_value' ORDER BY ds.id $order LIMIT 0, $limit";
						$sql_radius .= "`info_details` = '$search_value' OR `info_details` = '$exception_mac' ORDER BY `created_on` $order LIMIT 0, $limit";
						$sql_social_user_device_info .= "`device_mac` = '$search_value' ORDER BY sudi.id $order LIMIT 0, $limit";
						break;
					case 'LIKE%%' :
						$sql_device_info .= "d.mac_address LIKE '%$search_value%' ORDER BY d.id $order LIMIT 0, $limit";
						$sql_device_status_info .= " ds.mac = '%$search_value%' OR ds.mac1 LIKE '%$search_value%' OR ds.mac2 LIKE '%$search_value%' OR ds.mac3 LIKE '%$search_value%' ORDER BY ds.id $order LIMIT 0, $limit";
						$sql_radius .= "`info_details` LIKE '%$search_value%' OR `info_details` LIKE '%$exception_mac%' ORDER BY `created_on` $order LIMIT 0, $limit";
						$sql_social_user_device_info .= "`device_mac` LIKE '%$search_value%' ORDER BY sudi.id $order LIMIT 0, $limit";
						break;
				}
				
				$query_device_info = DB::select(DB::raw($sql_device_info));
				if(count($query_device_info) > 0){
					foreach($query_device_info as $row){
						$device_info[] = $row;
					}
				}else{
					$device_info = new stdClass();
					$device_info->status = 'error';
					$device_info->message = 'No data found';					
				}
				
				$query_device_status_info = DB::select(DB::raw($sql_device_status_info));
				if(count($query_device_status_info) > 0){
					foreach($query_device_status_info as $row){
						$device_status_info []  = $row;
					}
				}else{
					$device_status_info = json_encode(array(
					  'status'=>'error',
					  'message'=>'No data found'
					));
				}
				
				$con = DB::connection('ddwrtinfo');
				if($con){
					$query_radius = $con->select(DB::raw($sql_radius));
					if(count($query_radius) > 0){
						foreach($query_radius as $row){
							$radius_device_info[] = $row;
						}
					}else{
						$radius_device_info = json_encode(array(
						  'status'=>'error',
						  'message'=>'No data found'
						));
					}
				}else{
					$radius_device_info = json_encode(array(
						'status'=>'error',
					    'message'=>'Connection error'
					));
				}
				
				$query_social_user_device_info  = DB::select(DB::raw($sql_social_user_device_info));
				if(count($query_social_user_device_info) > 0){
					foreach($query_social_user_device_info as $row){
						$row->oslogo = getOSLogo($row->os_name);
						$social_user_device_info[] = $row;
					}
				}else{
					$social_user_device_info = json_encode(array(
					  'status'=>'error',
					  'message'=>'No data found'
					));
				}
    		}else{
    			$message  = GenerateConfirmationMessage('danger', $validation_error);
    			Session::put('SESSION_MESSAGE',$message);
    		}
    	}
    	
    	$data['device_info'] = $device_info;
    	$data['device_status_info'] = $device_status_info;
    	$data['radius_device_info'] = $radius_device_info;
    	$data['social_user_device_info'] = $social_user_device_info;    		
    	return view('admin.activitylog',$data);
    }
    
    public function modifyuser(Request $request){
    	$users = new stdClass();
    	$users->status = 'error';
    	$users->message = 'No record found';
    	
    	$data = array('search_value'=>'');
    	if($request->isMethod('post')){
    		if($request->has('search_value')){
    			$validation_error = '';
    			if($request->input('search_value') == ''){
    				$validation_error  .="You did not enter any value to search";
    			}
    			
    			if($validation_error == ''){
    				$search_value = $request->input('search_value');
    				$data['search_value'] = $search_value;
    				$users = $this->loadData($search_value);
    			}else{
    				$message = GenerateConfirmationMessage ( 'danger', '<i class="fa fa-info-circle"></i> '.$validation_error, true );
    				Session::put('SESSION_MESSAGE',$message);
    			}
    		}else if($request->has('act') && $request->input('act') == 'update'){    			
    			$validation_error = '';
    			$allowed_qty = $request->input('allowed_quantity');
    			$user_id = $request->input('hdn_user_id');
    			if($request->input('allowed_quantity') == ''){
    				$validation_error .="Allowed quantity should not be null.";
    			}elseif($allowed_qty < 1){
    				$validation_error .="Allowed quantity should minimum 1.";
    			}    		
	    		if($validation_error == ''){
	    			if($request->input('hdn_old_qty') != $allowed_qty){
	    				$nums = SubScriptionDetail::where('user_id',$user_id)->update(array('allowed_quantity'=>$allowed_qty));
	    				if($nums > 0){
	    					$message = GenerateConfirmationMessage ( 'success', '<i class="fa fa-info-circle"></i> Allowed quantity has been successfully updated.', true );
	    				}else{
	    					$message = GenerateConfirmationMessage ( 'danger', '<i class="fa fa-info-circle"></i> Allowed quantity has not been successfully updated.', true );
	    				}
	    			}else{
	    				$message = GenerateConfirmationMessage ( 'warning', '<i class="fa fa-info-circle"></i> Nothing to change.', true );
	    			}
	    		}else{
	    			$message = GenerateConfirmationMessage ( 'danger', '<i class="fa fa-info-circle"></i> '.$validation_error, true );
	    		}
	    		
	    		if($message){
	    			Session::put('SESSION_MESSAGE',	$message);
	    		}    		    
	    		$search_value = $request->input('hdn_email');
	    		$data['search_value'] = $search_value;
	    		$users = $this->loadData($request->input('hdn_email'));
    		}
    	}
    	$data['users'] = $users;   
    	   	
    	return view('admin.modifyuser',$data);
    }
    
    public function loadData($search_value){
    	$sql = "SELECT u.id, u.full_name, u.email_address, sd.plan_name, (SELECT COUNT(d.id) FROM `device` AS d WHERE d.status = 1 AND d.owner = u.id) AS active_device, sd.allowed_quantity FROM `user` AS u
		LEFT JOIN `subscription_detail` AS sd ON u.id = sd.user_id
		WHERE u.first_name LIKE '%$search_value%' OR u.last_name LIKE '%$search_value%' OR u.full_name LIKE '%$search_value%' OR u.email_address LIKE '%$search_value%' ORDER BY u.id DESC";
    	$query = DB::select(DB::raw($sql));    	
    	if(count($query) <= 0){
    		$query = new stdClass();
    		$query->status = "error";
    		$query->message = "No record found";
    	}
    	return $query;
    }
}
