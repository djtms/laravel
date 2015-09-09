<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

use App\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
require_once app_path().'/helper/helper.php';

class OverviewController extends Controller
{
    public function __construct(){
    	view()->share('controller','overview');
    	$this->middleware('auth');
    	$this->middleware('boot');
    }
    
    public function view(Request $request){
    	view()->share('action','view');
    	$data = array();
    	$data['time_zones'] = get_timezones();
    	return view('overview.view',$data);
    }
    
    public function deleteuser(Request $request){
    	view()->share('action','deleteuser');
    	$data = array();
    	
    	$uid = $request->input('uid');
    	$nums = User::where('id',$uid)->update(array('remove'=>1));
    	if($nums > 0){
    		Session::put('class','alert alert-success');
    		Session::put('message','<i class="entypo-info-circled"></i> Usfer has been successfully removed.');
    	}else{
    		Session::put('class','alert alert-danger');
    		Session::put('message','<i class="entypo-cancel-circled"></i> User has not been successfully removed.');
    	}
    	
    	return redirect(url('overview/view'));
    }
}
