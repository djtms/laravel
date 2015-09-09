<?php

namespace App\Http\Controllers;

use App\UserPermission;

use App\User;

use Illuminate\Support\Facades\Session;
use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
require_once app_path().'/helper/helper.php';

class SubuserController extends Controller
{
    public function __construct(){
    	view()->share('controller','subuser');
    	$this->middleware('auth');
    	$this->middleware('boot');
    }
    
    public function viewsubuser(Request $request){
    	view()->share('action','viewsubuser');
    	$markup = "";
    	$where = " WHERE u.user_type_id = 3 AND remove = 0";
		switch (Session::get('USER_TYPE')) {
			case '2' :
				$where .= " AND created_by = " . Session::get('USER_ID');
				break;
			case '3' :
				$where .= " AND created_by = " . Session::get('USER_CREATED_BY');
				break;
		}
		
		$sql = "SELECT u.id, u.full_name, u.email_address, up.module_ids AS modules, DATE_FORMAT(u.created_at,'%d %b %Y @ %h:%i %p') as created_at, (SELECT GROUP_CONCAT( `name` ) FROM location WHERE FIND_IN_SET(id,up.location_ids)) AS locations, (SELECT GROUP_CONCAT( `name` ) FROM campaign WHERE FIND_IN_SET(id,up.campaign_ids)) AS campaigns FROM `user` AS u LEFT JOIN `user_permission` AS up ON up.user_id = u.id $where";
		
		$result = DB::select(DB::raw($sql));
		if($result){
			foreach($result as $user){
				$markup .= '<tr>
                        		<td style="width: 12%; word-wrap: break-word;">'.$user->full_name.'</td>
                                <td style="width: 15%; word-wrap: break-word;">'.$user->email_address.'</td>
                                <td style="width: 27%; word-wrap: break-word;">'.$this->makeTag("info", $user->modules).'</td>
                                <td style="width: 26%; word-wrap: break-word;">'.$this->makeTag("info", $user->locations).'</td>
                                <td style="width: 26%; word-wrap: break-word;">'.$this->makeTag("info", $user->campaigns).'</td>
                                <td style="width: 10%; word-wrap: break-word;">'.$user->created_at.'</td>
                                <td style="width: 10%; word-wrap: break-word;">
                                	<a class="btn btn-info btn-sm" href="javascript:editsubuser('.$user->id.');" title="Edit this user."><i class="entypo-pencil"></i></a>
                                   	<button id="'.$user->id.'" type="button" class="btn btn-danger btn-sm delete_subuser" title="Delete this user."><i class="entypo-trash"></i></button>
                                </td>
                            </tr>';
			}
		}
	  $data = array('markup'=>$markup);
	  return view('subuser.viewsubuser',$data);
    }
    
    public function makeTag($class, $data) {
		$output = "";
		$my_data = explode(',', $data);
		foreach ($my_data as $value) {
			if ($value != "") {
				$output .= "<label class='badge badge-$class'>".ucfirst($value)."</label>";
			}
		}
	
		return $output != "" ? $output : "<label class='badge badge-danger'>Not Assigned</label>";
	}
	
	public function delete(Request $request){
		$id = $request->input('subuser_id');
		$user = User::where('id',$id)->first();
		$nums = $user->delete();
		if($nums > 0){
			echo $id;
			$nums = UserPermission::where('user_id',$id)->delete();			
		}	
		if($nums > 0){
			$message = GenerateConfirmationMessage('success', '<i class="entypo-info-circled"></i> User has successfully deleted.');
		}else{
			$message = GenerateConfirmationMessage('danger', '<i class="entypo-cancel-circled"></i> User has not successfully deleted.');
		}
		Session::put('SESSION_MESSAGE',	$message);
		return redirect(url('subuser/viewsubuser'));
	}
}
