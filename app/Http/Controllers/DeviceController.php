<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

use App\Location;

use App\SubScriptionDetail;

use App\Device;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
require_once app_path().'/helper/deviceinfo.php';
require_once app_path().'/helper/helper.php';

class DeviceController extends Controller
{
   public function __construct(){
   	  view()->share('controller','device');
   	  $this->middleware('auth');
   	  $this->middleware('boot');
   }
   public function view(Request $request){
   	 view()->share('action','view');
   	 $device_limit_msg = $message  = "";
   	 $active_device = Device::countActiveDevice();
   	 $allowed_device = SubScriptionDetail::countAllowedDevice();
   	 if($request->input('USER_TYPE') != '1' && $active_device >= $allowed_device){
   	 	$message = "You are currently using $allowed_device out of the $allowed_device active devices included in your plan.";
   	 	if(Session::get('USER_TYPE') == '2'){
   	 		$message .= " To add more active devices to your account, <a href=".url('user/editprofile&get=device').">click here</a>";
   	 	}
   	 }
   	 
   	 if($message != ''){
   	 	$device_limit_msg = GenerateConfirmationMessage('danger', $message);
   	 }   
   	 $data  = array();
   	 $data['device_limit_msg'] = $device_limit_msg;	 
   	 $data['device_list'] = get_device_info();
   	 $data['location_list'] = Location::RetrieveLocationDropdown();
   	 $data['active_device'] = $active_device;
   	 $data['allowed_device'] = $allowed_device;
   	 return view('device.view',$data);
   } 
   
   public function update(Request $request){
   	    $message = "";
        $device = Device::find($request->input('device_id'));
        
        if($request->has('mac_address') && $request->input('mac_address') != ""){
        	if(checkDuplicateMAC($request->input('device_id'), $request->input('mac_address'))){
        		$message = GenerateConfirmationMessage('danger', 'Device has not been successfully updated.');
        		return redirect(url('device/view'));
        	}else{
        		$device->mac_address = trim( $request->input('mac_address'));
        	}
        }
        
        $device->name = $request->input('device_name');
        
        if($device->status == '1'){
            $device->location_id = $request->input('location_id');
        }
        $device->update_date = date("Y-m-d H:i:s");
        
        if (!$device->save()) {
            $message = GenerateConfirmationMessage('danger', 'Device has not been successfully updated.');
        }
        else{
            $message = GenerateConfirmationMessage('success', 'Device has been successfully updated.');
        }
        Session::put('SESSION_MESSAGE', $message);
        return redirect(url('device/view'));
        $message = "";
        $device = Device::find($request->input('device_id'));
        
        if($request->has('mac_address') && $request->input('mac_address') != ""){
        	if(checkDuplicateMAC($request->input('device_id'), $request->input('mac_address'))){
        		$message = GenerateConfirmationMessage('danger', 'Device has not been successfully updated.');
        		return redirect(url('device/view'));
        	}else{
        		$device->mac_address  = trim( $request->input('mac_address'));
        	}
        }
        
        $device->name = $request->input('device_name');
        
        if($device->status == '1'){
            $device->location_id = $request->input('location_id');
        }
        $device->update_date  = date("Y-m-d H:i:s");
        
        if (!$device->save()) {
            $message = GenerateConfirmationMessage('danger', 'Device has not been successfully updated.');
        }
        else{
            $message = GenerateConfirmationMessage('success', 'Device has been successfully updated.');
        }
        Session::put('SESSION_MESSAGE', $message);
        return redirect(url('device/view'));
   }
   
   public function create(Request $request){
   	   $message = "";
        $device_name = $request->has('device_name') ? $request->input('device_name') : "";
        $supported_routers = $request->has('supported_routers') ? $request->input('supported_routers') : "";
        $mac_address = $request->has('mac_address') ? $request->input('mac_address') : "";
        $location = $request->has('location') ? $request->input('location') : "";
        
        if($device_name != "" && $supported_routers != "" && $mac_address != "" && $location != ""){
        	$owner = Session::get('USER_TYPE') == '3' ? Session::get('USER_CREATED_BY') : Session::get('USER_ID');
        	
        	if(Device::checkDuplicateMAC("", $mac_address)){
        		$message = GenerateConfirmationMessage('danger', "Given MAC '<b>".$mac_address."</b>' already exists in our database. Please contact with our support team for help.");
        	}else{
        		$device = new Device;
        		$device->name = $request->input('device_name');
        		$device->model = $request->input('supported_routers');
        		$device->mac_address = trim( $mac_address );
        		$device->create_date = date("Y-m-d h:i:s");
        		$device->owner = $owner;
        		 
        		if(Session::get('USER_TYPE') != "1"){
        			$active_device = Device::countActiveDevice();
        			$allowed_device = SubScriptionDetail::countAllowedDevice();
        			if ($active_device < $allowed_device) {
        				$device->status = 1;
        				$device->location_id = $request->input('location');
        			} else {
        				$device->status = 0;
        			}
        		}else{
        			$device->location_id = $request->input('location');
        			$device->status = 1;
        		}
        		 
        		if (!$device->save()) {
        			$message = GenerateConfirmationMessage('danger', 'Device has not been successfully added.');
        		}
        		else{
        			$message = GenerateConfirmationMessage('success', 'Device has been successfully added.');
        		}
        	}
        }else{
        	$message = GenerateConfirmationMessage('danger', 'All fields are required.');
        }        
        Session::put('SESSION_MESSAGE', $message);
        return redirect(url('device/view'));
   }
}
