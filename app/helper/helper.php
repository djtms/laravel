<?php

use App\SubscriptionPlan;
use App\SubscriptionLog;
use App\Http\Requests\Request;
use App\Device;
use App\Option;
use App\UserPermission;
use Illuminate\Support\Facades\Cookie;
use App\SubScriptionDetail;
use App\Http\Requests;
use Illuminate\Support\Facades\Config;

require_once app_path().'/helper/payment_gateway/recurly/lib/recurly.php';
require_once app_path().'/helper/device_detector/DeviceDetector.php';
require_once app_path().'/helper/payment_gateway/stripe/lib/Stripe.php';


function GenerateConfirmationMessage($class, $text, $cls_btn = false) {
	$close_button = $cls_btn == true ? "<button data-dismiss='alert' class='close' type='button'><span aria-hidden='true'>x</span><span class='sr-only'>Close</span></button>" : "";
	return "<div class='row'><div class='col-md-12'><div class='alert alert-$class'>" . $close_button . $text . "</div></div></div>";
}

function get_timezones(){
		return array (
			'' => '-- Select One --',
			'eniwetok/kwajalein' => '(GMT -12:00) Eniwetok, Kwajalein',
			'pacific/midway' => '(GMT -11:00) Midway Island, Samoa',
			'america/adak' => '(GMT -10:00) Hawaii',
			'america/yakutat' => '(GMT -9:00) Alaska',
			'america/dawson' => '(GMT -8:00) Pacific Time (US &amp; Canada)',
			'america/boise' => '(GMT -7:00) Mountain Time (US &amp; Canada)',
			'america/belize' => '(GMT -6:00) Central Time (US &amp; Canada), Mexico City',
			'america/atikokan' => '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima',
			'america/anguilla' => '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz',
			'america/st_johns' => '(GMT -3:30) Newfoundland',
			'america/argentina/cordoba' => '(GMT -3:00) Brazil, Buenos Aires, Georgetown',
			'america/araguaina' => '(GMT -2:00) Mid-Atlantic',
			'atlantic/azores' => '(GMT -1:00) Azores, Cape Verde Islands',
			'europe/london' => '(GMT) Western Europe Time, London, Lisbon, Casablanca',
			'africa/algiers' => '(GMT +1:00) Brussels, Copenhagen, Madrid, Paris',
			'africa/blantyre' => '(GMT +2:00) Kaliningrad, South Africa',
			'africa/addis_ababa' => '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg',
			'asia/tehran' => '(GMT +3:30) Tehran',
			'asia/baku' => '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi',
			'asia/kabul' => '(GMT +4:30) Kabul',
			'asia/aqtau' => '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
			'asia/colombo' => '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi',
			'asia/kathmandu' => '(GMT +5:45) Kathmandu',
			'asia/dhaka' => '(GMT +6:00) Almaty, Dhaka, Colombo',
			'asia/bangkok' => '(GMT +7:00) Bangkok, Hanoi, Jakarta',
			'asia/hong_kong' => '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong',
			'asia/dili' => '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
			'australia/darwin' => '(GMT +9:30) Adelaide, Darwin',
			'australia/brisbane' => '(GMT +10:00) Eastern Australia, Guam, Vladivostok',
			'antarctica/macquarie' => '(GMT +11:00) Magadan, Solomon Islands, New Caledonia',
			'asia/anadyr' => '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka' 
	);
}

function convertTimeBasedOnTimezone($server_timezone,$local_timezone,$local_time,$format,$timestamp = false){
	$server_timezone = $server_timezone == 'Bangladesh Standard Time' ? 'BST' : $server_timezone;
	$datetime = new DateTime ( $local_time, new DateTimeZone ( $server_timezone ) );
	$datetime->setTimezone ( new DateTimeZone ( $local_timezone ) );
	$my_datetime = $datetime->format ( $format );
	$my_datetime = $timestamp == true ? strtotime ( $my_datetime ) : $my_datetime;
	return $my_datetime;
}

function trimSentence($sentence, $length) {
	$output = $sentence;
	if (strlen ( $sentence ) > $length) {
		$output = substr ( $sentence, 0, $length ) . '...';
	}
	return $output;
}

function getSocialMediaIcon($param) {
	$output = "";
	switch ($param) {
		case 'FBuser' :
			$output = "<i class='fa fa-facebook-square'></i>";
			break;
		case 'TWuser' :
			$output = "<i class='fa fa-twitter-square'></i>";
			break;
		case 'LIuser' :
			$output = "<i class='fa fa-linkedin-square'></i>";
			break;
		case 'GPuser' :
			$output = "<i class='fa fa-google-plus-square'></i>";
			break;
		case 'IGuser' :
			$output = "<i class='fa fa-instagram'></i>";
			break;
		case 'Cuser' :
			$output = "<i class='fa fa-envelope-square'></i>";
			break;
	}
	return $output;
}
function getOSLogo($os) {
	$output = $os;
	if ($os != "") {
		$os_logo = array (
		'mac' => url ( 'themes/neon/assets/os_logo/apple.png' ),
		'android' => url ( 'themes/neon/assets/os_logo/android.png' ),
		'windows' => url ( 'themes/neon/assets/os_logo/windows.png' ),
		'ios' => url ( 'themes/neon/assets/os_logo/apple.png' ),
		'meego' => url ( 'themes/neon/assets/os_logo/windows.png' ),
		'symbian' => url ( 'themes/neon/assets/os_logo/symbian.png' ),
		'windowsphone' => url ( 'themes/neon/assets/os_logo/windows.png' ),
		'ubuntu' => url ( 'themes/neon/assets/os_logo/ubuntu.png' ),
		'blackberryos' => url ( 'themes/neon/assets/os_logo/blackberry.png' ),
		'gnu/linux' => url ( 'themes/neon/assets/os_logo/linux.png' ),
		'windowsrt' => url ( 'themes/neon/assets/os_logo/windows.png' ),
		'firefoxos' => url ( 'themes/neon/assets/os_logo/firefox.png' ),
		'chromeos' => url ( 'themes/neon/assets/os_logo/chrome.png' ),
		'windowsmobile' => url ( 'themes/neon/assets/os_logo/windows.png' ) 
        );
		$os_name = strtolower ( str_replace ( ' ', '', $os ) );
		$output = "<img src='' alt='$os'/>";
		if (array_key_exists ( $os_name, $os_logo )) {
			$logo_path = $os_logo [$os_name];
			$output = "<img src='$logo_path' alt='$os'/>";
		}
	} else {
		$output = "<img src=" . url( 'themes/neon/assets/os_logo/unknown.png' ) . " alt='Unknown'/>";
	}
	return $output;
}

function getRecurlyData($user_id){
   	    $subscription_detail = array ();
		$plan_list = array ();
		$billing_info = array ();
		$account_code = "";
		
		$result = SubScriptionDetail::select('subscription_id','account_code','plan_name','allowed_quantity','plan_price','plan_interval')->where('user_id',$user_id)->first();
		if($result){
			$subscription_detail ['subscription_id'] = $result->subscription_id;
			$subscription_detail ['plan_code'] = $result->plan_code;
			$subscription_detail ['plan_name'] = $result->plan_name;
			$subscription_detail ['allowed_quantity'] = $result->allowed_quantity;
			$subscription_detail ['plan_price'] = $result->plan_price / 100;
			$subscription_detail ['plan_interval'] = $result->plan_interval;
			$account_code = $result->account_code;
		}
		Recurly_Client::$subdomain = Config::get('constants.RECURLY_SUBDOMAIN');
	    Recurly_Client::$apiKey = Config::get('constants.RECURLY_API_KEY');
	    /* Get Plan List From Recurly */
		try {
			$plans = Recurly_PlanList::get ();
			foreach ( $plans as $plan ) {
				$plan_list [str_replace ( '-', '_', $plan->plan_code )] = $plan->unit_amount_in_cents->USD->amount_in_cents / 100;
			}
		} catch ( Recurly_NotFoundError $e ) {
		}
		/* END */
		
		/*Get BillingInformation From Recurly*/
		try {
			$bdata = Recurly_BillingInfo::get ( $account_code );
			$billing_info ['first_name'] = isset ( $bdata->first_name ) ? $bdata->first_name : "";
			$billing_info ['last_name'] = isset ( $bdata->last_name ) ? $bdata->last_name : "";
			$billing_info ['address'] = isset ( $bdata->address1 ) ? $bdata->address1 : "";
			$billing_info ['city'] = isset ( $bdata->city ) ? $bdata->city : "";
			$billing_info ['state'] = isset ( $bdata->state ) ? $bdata->state : "";
			$billing_info ['zip'] = isset ( $bdata->zip ) ? $bdata->zip : "";
			$billing_info ['country'] = isset ( $bdata->country ) ? $bdata->country : "";
			$billing_info ['card_type'] = isset ( $bdata->card_type ) ? $bdata->card_type : "";
			$billing_info ['last_four'] = isset ( $bdata->last_four ) ? $bdata->last_four : "";
		} catch ( Recurly_NotFoundError $e ) {
		}
		$recurly_data = array (
				'subscription_detail' => $subscription_detail,
				'plan_list' => $plan_list,
				'billing_info' => $billing_info 
		);
		/* END */
		
		return $recurly_data;
}

function process_unsetcookie(){
	Cookie::make("EMAIL","",time() - 3600);
	Cookie::make("FULL_NAME","",time() - 3600);
	Cookie::make("USER_ID","",time() - 3600);
	Cookie::make("PASSWORD","",time() - 3600);
	Cookie::make("IS_ADMIN","",time() - 3600);
}

function secure($string) {
    // This really is deprecated but some servers still use magic quotes
    $escape = get_magic_quotes_gpc() ? "stripslashes" : "mysql_real_escape_string";

    if (!is_array($string)) :
        $string = $escape(trim($string));
    else :
        foreach ($string as $key => $value) :
            $string[$key] = $escape(trim($value));
        endforeach;
    endif;

    return $string;
}
function trimDeviceMac($orginal_mac) {
	return substr ( $orginal_mac, 0, 16 );
}

function getLanguageArray(Request $request) {
	$filename = $request->server('DOCUMENT_ROOT').'/language/lang.txt';
	$contents = file_get_contents ( $filename );
	return json_decode ( $contents, true );
}

function getDeviceDetails(Request $request) {	
	
	$userAgent = $request->has('HTTP_USER_AGENT')? $request->server('HTTP_USER_AGENT') : "";
	$dd = new DeviceDetector ( $userAgent );
	
	$dd->parse ();
	
	$clientInfo = $dd->getClient (); // holds information about browser, feed reader, media player, ...
	$osInfo = $dd->getOs ();
	return array (
			'user_agent' => $userAgent,
			'browser_type' => isset ( $clientInfo ['type'] ) ? $clientInfo ['type'] : "",
			'browser_name' => isset ( $clientInfo ['name'] ) ? $clientInfo ['name'] : "",
			'browser_version' => isset ( $clientInfo ['version'] ) ? $clientInfo ['version'] : "",
			'browser_engine' => isset ( $clientInfo ['engine'] ) ? $clientInfo ['engine'] : "",
			'os_name' => isset ( $osInfo ['name'] ) ? $osInfo ['name'] : "",
			'os_version' => isset ( $osInfo ['version'] ) ? $osInfo ['version'] : "",
			'device' => $dd->isMobile () == true ? "Mobile" : "Desktop",
			'brand' => $dd->getBrand (),
			'model' => $dd->getModel () 
	);
}

function processpayment(Request $request){
$output = array ();
		$validation_error = "";
		$flug = true;
		$paid = false;
		$attemped = false;
		$payment_type = $request->input('payment_type');
		$existing_device = $request->input ('hdn_current_device');
		$number_of_device = $request->input('number_of_device');
		if ($payment_type == '1') {
			$expiration = explode ( '/', $request->input('expiration'));
			$month = isset ( $expiration [0] ) ? $expiration [0] : "";
			$year = isset ( $expiration [1] ) ? $expiration [1] : "";
			$card_number = str_replace ( ' ', '', $request->input('card_number'));
			
			if (! $request->has('b_first_name') || empty ( $request->input('b_first_name'))) {
				$validation_error .= "First Name is required.<br>";
				$flug = false;
			}
			
			if (!$request->has('b_last_name') || empty ( $request->input('b_last_name'))) {
				$validation_error .= "Last Name is required.<br>";
				$flug = false;
			}
			
			if (! $request->has('card_number') || $request->input('card_number') == "") {
				$validation_error .= "Card Number is required.<br>";
				$flug = false;
			} else if (! is_numeric ( $request->input('card_number'))) {
				$validation_error .= "Invalid card number.<br>";
				$flug = false;
			}
			if (!$request->input('cvc') || empty ( $request->input('cvc'))) {
				$validation_error .= "CVC is required.<br>";
				$flug = false;
			}
			if (! $request->has('expiration') || empty ( $request->input('expiration'))) {
				$validation_error .= "Expiration is required.<br>";
				$flug = false;
			} else if (empty ($month) || ! is_numeric ( $month ) || ! is_numeric ( $year) || empty ( $year)) {
				$validation_error .= "Invalid Date.<br>";
				$flug = false;
			}
			
			if (! $request->input('b_address') || empty ( $request->input('b_address'))) {
				$validation_error .= "Address is required.<br>";
				$flug = false;
			}
			
			if (! $request->has('b_city') || empty ( $request->input('b_city'))) {
				$validation_error .= "City is required.<br>";
				$flug = false;
			}
			
			if (! $request->has('b_state') || empty ( $request->input('b_state'))) {
				$validation_error .= "State is required.<br>";
				$flug = false;
			}
			
			if (!$request->has('b_zip') || empty ( $request->input('b_zip'))) {
				$validation_error .= "Zip is required.<br>";
				$flug = false;
			}
			
			if (! $request->has('b_country') || empty ( $request->input('b_country'))) {
				$validation_error .= "Country is required.<br>";
				$flug = false;
			}
		}
		
		if (! $request->has('number_of_device') || $request->input('number_of_device') == "") {
			$validation_error .= "Number of device is required.<br>";
			$flug = false;
		} else {
			if ($number_of_device < 1 || $number_of_device > 51) {
				$validation_error .= "Device quantity should be 1 to 51.<br>";
				$flug = false;
			}
		}
		
		if ($flug == true) {
			if ($number_of_device == $existing_device) {
				$validation_error .= "Nothing to update.<br>";
				$flug = false;
			}
		}
		
		if ($flug == true) {
			
			include_once 'libs/payment_gateway/recurly/lib/recurly.php';
			Recurly_Client::$subdomain = RECURLY_SUBDOMAIN;
			Recurly_Client::$apiKey = RECURLY_API_KEY;
			
			$subscription_id = $request->input('hdn_subscription_plan_id');
			
			if ($payment_type == '1') {
				try {
					/* Update Billing Information */
					$user_id = Session::get('USER_TYPE') == '3' ? Session::get('USER_CREATED_BY') : Session::get('USER_ID');
					$account_code = UserMeta::getUserMeta( 'recurly_account_code', $user_id );
					$billing_info = new Recurly_BillingInfo ();
					$billing_info->account_code = $account_code;
					$billing_info->first_name = $request->input('b_first_name');
					$billing_info->last_name = $request->input('b_last_name');
					$billing_info->address1 = $request->input('b_address');
					$billing_info->city = $request->input('b_city');
					$billing_info->state = $request->input('b_state');
					$billing_info->country = $request->input('b_country');
					$billing_info->zip = $request->input('b_zip');
					$billing_info->number = $request->input('card_number');
					$billing_info->verification_value = $request->input('cvc');
					$billing_info->month = $request->input('month');
					$billing_info->year = $request->input('year');
					$billing_info->update ();
					
					/* Update Subscription */
					$subscription = Recurly_Subscription::get ( $subscription_id );
					$subscription->plan_code = 'mywifi-' . $number_of_device;
					$subscription->updateImmediately (); // Update immediately.
					
					/* Update Subscription Detail */
					$plan_code = $subscription->plan->plan_code;
					$plan_name = $subscription->plan->name;
					$allowed_quantity = $number_of_device;
					$plan_price = $subscription->unit_amount_in_cents;
					$plan_interval = 'monthly';
					$obj_active_at = json_decode ( json_encode ( $subscription->activated_at ) );
					$activated_at = $obj_active_at->date;
					$obj_current_period_started = json_decode ( json_encode ( $subscription->current_period_started_at ) );
					$current_period_started_at = $obj_current_period_started->date;
					$obj_current_period_ends = json_decode ( json_encode ( $subscription->current_period_ends_at ) );
					$current_period_ends_at = $obj_current_period_ends->date;
					
					$sql = "UPDATE `subscription_detail` SET
				`plan_code` = '$plan_code',
				`plan_name` = '$plan_name',
				`allowed_quantity` = '$allowed_quantity',
				`plan_price` = '$plan_price',
				`plan_interval` = '$plan_interval',
				`activated_at` = '$activated_at',
				`current_period_started_at` = '$current_period_started_at',
				`current_period_ends_at` = '$current_period_ends_at' WHERE `subscription_id` = '$subscription_id'";
					Database::query ( $sql );
				} catch ( Exception $ex ) {
					$gateway_error = $e->getMessage ();
				}
			} else {
				try {
					$subscription = Recurly_Subscription::get ( $subscription_id );
					$subscription->plan_code = 'mywifi-' . $number_of_device;
					$subscription->updateImmediately (); // Update immediately.
					
					$plan_code = $subscription->plan->plan_code;
					$plan_name = $subscription->plan->name;
					$allowed_quantity = $number_of_device;
					$plan_price = $subscription->unit_amount_in_cents;
					$plan_interval = 'monthly';
					$obj_active_at = json_decode ( json_encode ( $subscription->activated_at ) );
					$activated_at = $obj_active_at->date;
					$obj_current_period_started = json_decode ( json_encode ( $subscription->current_period_started_at ) );
					$current_period_started_at = $obj_current_period_started->date;
					$obj_current_period_ends = json_decode ( json_encode ( $subscription->current_period_ends_at ) );
					$current_period_ends_at = $obj_current_period_ends->date;
					
					$sql = "UPDATE `subscription_detail` SET 
					`plan_code` = '$plan_code', 
					`plan_name` = '$plan_name', 
					`allowed_quantity` = '$allowed_quantity', 
					`plan_price` = '$plan_price', 
					`plan_interval` = '$plan_interval', 
					`activated_at` = '$activated_at', 
					`current_period_started_at` = '$current_period_started_at', 
					`current_period_ends_at` = '$current_period_ends_at' WHERE `subscription_id` = '$subscription_id'";
					Database::query ( $sql );
				} catch ( Exception $ex ) {
					$gateway_error = $ex->getMessage ();
				}
			}
			
			if (empty ( $gateway_error )) {
				$output ['style'] = "success";
				$output ['message'] = "Approved! Please check your email for a detailed invoice.";
			} else {
				$output ['style'] = "danger";
				$output ['message'] = $gateway_error;
			}
			
			/*
			 * // Get Strip Information $stripe_obj = get_option ( 'stripe_settings' ); if ($stripe_obj) { $stripe_info = json_decode ( $stripe_obj ); $token = ""; $customer = array (); $stripe_error = ""; Stripe::setApiKey ( $stripe_info->api_key ); $subscription = NULL; if ($payment_type == '1') { Make transaction via new card // Create Token if (empty ( $stripe_error )) { try { $response = Stripe_Token::create ( array ( "card" => array ( "number" => $_POST ['card_number'], "exp_month" => $_POST ['month'], "exp_year" => $_POST ['year'], "cvc" => $_POST ['cvc'] ) ) ); $token = $response->id; } catch ( Exception $ex ) { $body = $ex->getJsonBody (); $err = $body ['error']; $stripe_error = $err ['message']; } } // Create Card if (empty ( $stripe_error )) { try { $customer = Stripe_Customer::retrieve ( $_POST ['hdn_customer_id'] ); $customer->cards->create ( array ( "card" => $token ) ); } catch ( Exception $ex ) { $customer = Stripe_Customer::create ( array ( "email" => Session::get ( EMAIL_ADDRESS ), "card" => $token ) ); } } // Update Subscription if (empty ( $stripe_error )) { try { $subscription_id = $customer->subscriptions->data ['0']->id; $subscription = $customer->subscriptions->retrieve ( $subscription_id ); $subscription->plan = 'mywifi-' . $number_of_device; $subscription->prorate = true; $subscription->save (); } catch ( Exception $ex ) { $subscription = $customer->subscriptions->create ( array ( "plan" => 'mywifi-' . $number_of_device ) ); } } Create the invoice item if (empty ( $stripe_error )) { try { $invoice_item = Stripe_InvoiceItem::create ( array ( 'customer' => $subscription->customer, // the customer to apply the fee to 'amount' => $subscription->plan->amount, // amount in cents 'currency' => $subscription->plan->currency ) ); } catch ( Exception $ex ) { $body = $ex->getJsonBody (); $err = $body ['error']; $stripe_error = $err ['message']; } } Create Invoice, Retrive Invoice and payment against this invoice if (empty ( $stripe_error )) { try { $invoice = Stripe_Invoice::create ( array ( "customer" => $subscription->customer ) ); $invoice = Stripe_Invoice::retrieve ( $invoice->id ); $invoice->pay (); } catch ( Exception $ex ) { $body = $ex->getJsonBody (); $err = $body ['error']; $stripe_error = $err ['message']; } } } else { Make transaction via old card // Create Subscription if (empty ( $stripe_error )) { try { $customer = Stripe_Customer::retrieve ( $_POST ['hdn_customer_id'] ); $subscription_id = isset ( $customer->subscriptions->data ['0']->id ) ? $customer->subscriptions->data ['0']->id : ""; $subscription = $customer->subscriptions->retrieve ( $subscription_id ); $subscription->plan = 'mywifi-' . $number_of_device; $subscription->prorate = true; $subscription->save (); } catch ( Exception $ex ) { $body = $ex->getJsonBody (); $err = $body ['error']; $stripe_error = $err ['message']; } } Create the invoice item if (empty ( $stripe_error )) { try { $invoice_item = Stripe_InvoiceItem::create ( array ( 'customer' => $subscription->customer, // the customer to apply the fee to 'amount' => $subscription->plan->amount, // amount in cents 'currency' => $subscription->plan->currency ) ); } catch ( Exception $ex ) { $body = $ex->getJsonBody (); $err = $body ['error']; $stripe_error = $err ['message']; } } Create Invoice, Retrive Invoice and payment against this invoice if (empty ( $stripe_error )) { try { $invoice = Stripe_Invoice::create ( array ( "customer" => $subscription->customer ) ); $invoice = Stripe_Invoice::retrieve ( $invoice->id ); $invoice->pay (); } catch ( Exception $ex ) { $body = $ex->getJsonBody (); $err = $body ['error']; $stripe_error = $err ['message']; } } } // Generate Confirmation Message if (empty ( $stripe_error )) { $paid = $invoice->paid; $attemped = $invoice->attempted; if ($paid == true && $attemped == true) { Update data to subscription_plan and Insert data to subscription_log table $plan = $subscription->plan; $strip_data = array ( 'customer_id' => $customer->id, 'subscription_plan_id' => $_POST ['hdn_subscription_plan_id'], 'plan_id' => $plan->id, 'interval' => $plan->interval, 'plan_name' => $plan->name, 'amount' => $plan->amount, 'currency' => '$', 'current_period_start' => date ( 'Y-m-d', $subscription->current_period_start ), 'current_period_end' => date ( 'Y-m-d', $subscription->current_period_end ), 'trial_period_days' => $plan->trial_period_days, 'is_card' => 1, 'device_quantity' => $number_of_device, 'created_at' => date ( 'Y-m-d' ), 'modified_at' => date ( 'Y-m-d' ), 'existing_device' => $existing_device, 'current_ammount' => $_POST ['hdn_current_ammount'] ); $this->UpdateSubscriptionPlan ( $strip_data ); $output ['style'] = "success"; $output ['message'] = "Approved! Please check your email for a detailed invoice."; } else { $output ['style'] = "warning"; $output ['message'] = "We are unable to process your request due to payment gateway delay. We'll process your request soon."; } } else { $output ['style'] = "danger"; $output ['message'] = $stripe_error; } } else { $output ['style'] = "danger"; $output ['message'] = UNDEFINED_STRIP_SETTINGS; }
			 */
		} else {
			$output ['style'] = "warning";
			$output ['message'] = $validation_error;
		}
		return $output;
}

function mywifi_cdn($s3_url) {
	$mywifi_cdn = str_ireplace ( Config::get('aws.AWS_CDN_RAW'), Config::get('aws.AWS_CDN'), $s3_url );
	return $mywifi_cdn;
}

function generate_strong_password($length = 8) {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
	$password = substr ( str_shuffle ( $chars ), 0, $length );
	return $password;
}

function set_user_permission($user_id){
	$module_array = array (									
								'device' => 'Devices',
								'location' => 'Locations',
								'campaign' => 'Campaigns',									
								'report' => 'Analytics & Reports',
								'social_app' => 'Connect Social Accounts',
								'subuser' => 'Sub Users');
	$module_ids = '';
	foreach($module_array as $key=>$val){
		$module_ids.=$key.',';
	}
	$module_ids = rtrim($module_ids,',');
	$record = new UserPermission;
	$record->user_id = $user_id;
	$record->module_ids = $module_ids;
	$record->save();
	return true;
}

function create_stripe_subscriber($user_id,$user_email){
	$stripe_obj  = Option::getOption('stripe_settings');
	if($stripe_obj){
		$stripe_info = json_decode($stripe_obj);
		Stripe::setApiKey($stripe_info->api_key);
		$output = null;
		$stripe_error = '';
		$subscription = null;
		try{
			$customer = Stripe_Customer::create(
				array(
					'email'=>$user_email
				)
			);
		}catch(Exception $ex){
			$body = $ex->getJsonBody();
			$err = $body['error'];
			$stripe_error = $err['message'];
		}
	
		if (empty ( $stripe_error )) {
			try {
				$subscription = $customer->subscriptions->create ( array (
						"plan" => "mywifi-1" 
				) );
			} catch ( Exception $ex ) {
				$body = $ex->getJsonBody ();
				$err = $body ['error'];
				$stripe_error = $err ['message'];
			}
		}
		
		if (empty ( $stripe_error )) {
			$plan = $subscription->plan;
			$sql = "INSERT INTO `subscription_plan` (`user_id`, `plan_id`, `customer_id`, `number_of_allowed_device`, `interval`, `plan_name`, `amount`, `currency`, `current_period_start`, `current_period_end`, `trial_period_days`, `is_card`) " . "VALUES('" . $user_id . "', '" . $plan->id . "', '" . $customer->id . "', '1', '" . $plan->interval . "', '" . $plan->name . "', '" . $plan->amount . "', '$', '" . date ( 'Y-m-d', $subscription->current_period_start ) . "', '" . date ( 'Y-m-d', $subscription->current_period_end ) . "', '" . $plan->trial_period_days . "', 0)";
			Database::query ( $sql );
			$insert_id = Database::getInsertId ();
			$sql = "INSERT INTO `subscription_log` (`subscription_plan_id`, `plan_id`, `device_quantity`, `interval`, `plan_name`, `amount`, `currency`, `trial_period_days`, `current_period_start`, `current_period_end`)" . "VALUES('" . $insert_id . "', '" . $plan->id . "', '1', '" . $plan->interval . "', '" . $plan->name . "', '" . $plan->amount . "', '$', '" . $plan->trial_period_days . "', '" . date ( 'Y-m-d', $subscription->current_period_start ) . "', '" . date ( 'Y-m-d', $subscription->current_period_end ) . "')";
			Database::query ( $sql );
			
			$output ['status'] = '1';
			$output ['message'] = "success";
		} else {
			$output ['status'] = '0';
			$output ['message'] = $stripe_error;
		}
	} else {
		$output ['status'] = '0';
		$output ['message'] = UNDEFINED_STRIP_SETTINGS;
	}
	
	return json_encode ( $output );
}

function processstripenotification(){
	$stripe_obj = Option::getOption('stripe_settings');
	if($stripe_obj){
		$stripe_obj = json_decode($stripe_obj);
		Stripe::setApiKey($stripe_obj->api_key);
		
		$response = @file_get_contents('php://input');
		$response_data = json_decode($response);
		$event = $response_data->type;
		
		if($event == 'invoice.payment_succeeded'){
			try{
				$customer = Stripe_Customer::retrieve($response_data->data->object->customer);
				$subscription = $customer->subscriptions;
					$plan = $customer->subscriptions->data [0]->plan;
					$temp = explode ( "-", $plan->id );
					$quantity = $temp [1];
					
					$cards = (isset ( $customer->cards->data [0]->id ) && $customer->cards->data [0]->id != "") ? 1 : 0;
					
					$data = array (
							'customer_id' => $customer->id,
							'plan_id' => $plan->id,
							'interval' => $plan->interval,
							'plan_name' => $plan->name,
							'amount' => $plan->amount,
							'currency' => '$',
							'current_period_start' => date ( 'Y-m-d', $subscription->data [0]->current_period_start ),
							'current_period_end' => date ( 'Y-m-d', $subscription->data [0]->current_period_end ),
							'trial_period_days' => $plan->trial_period_days,
							'is_card' => $cards,
							'device_quantity' => $quantity,
							'created_at' => date ( 'Y-m-d' ) 
					);
					
					return updateSubscriptionPlan($data);			
			}catch(Exception $e){
				$body = $e->getJsonBody();
				$err  = $body['error'];
				return $err['message'];
			}
		}else if($event =='invoice.payment_failed'){
			try{
				$customer_id = $response_data->data->object->customer;
					
					$customer = Stripe_Customer::retrieve ( $customer_id );
					$subscription_id = $customer->subscriptions->data ['0']->id;
					$subscription = $customer->subscriptions->retrieve ( $subscription_id );
					$subscription->plan = 'mywifi-1';
					$subscription->prorate = true;
					$subscription->save ();
					
					$plan = $subscription->plan;
					
					$cards = isset ( $customer->cards ) ? 1 : 0;
					
					$data = array (
							'customer_id' => $customer_id,
							'plan_id' => $plan->id,
							'interval' => $plan->interval,
							'plan_name' => $plan->name,
							'amount' => $plan->amount,
							'currency' => '$',
							'current_period_start' => date ( 'Y-m-d', $subscription->data [0]->current_period_start ),
							'current_period_end' => date ( 'Y-m-d', $subscription->data [0]->current_period_end ),
							'trial_period_days' => $plan->trial_period_days,
							'is_card' => $cards,
							'device_quantity' => 1,
							'created_at' => date ( 'Y-m-d' ) 
					);
					
					updateSubscriptionPlan ( $data );
					
					$sql = "SELECT `id` FROM `device` WHERE `status` = 1 AND `owner` = (SELECT `user_id` FROM `subscription_plan` WHERE `customer_id` = '$customer_id' LIMIT 1) LIMIT 18446744073709551615 OFFSET 1";
					$query = DB::select(DB::raw($sql));
					if(count($query) > 0){
						$device_ids = '';
						foreach($query as $row){
							$device_ids .=$row->id.',';
						}
						Device::whereIn('id',explode($device_ids,','))->update(array('status'=>0));
						
					}
			}catch(Exception $ex){
				print_r($ex);
			}
		}else if($event == 'customer.subscription.deleted'){
			$customer_id = $response_data->data->object->customer;
				$customer = Stripe_Customer::retrieve ( $customer_id );
				$subscription = $customer->subscriptions->create ( array (
						"plan" => "mywifi-1" 
				) );
				
				$plan = $subscription->plan;
				$cards = isset ( $customer->cards ) ? 1 : 0;
				
				$data = array (
						'customer_id' => $customer_id,
						'plan_id' => $plan->id,
						'interval' => $plan->interval,
						'plan_name' => $plan->name,
						'amount' => $plan->amount,
						'currency' => '$',
						'current_period_start' => date ( 'Y-m-d', $subscription->current_period_start ),
						'current_period_end' => date ( 'Y-m-d', $subscription->current_period_end ),
						'trial_period_days' => $plan->trial_period_days,
						'is_card' => $cards,
						'device_quantity' => 1,
						'created_at' => date ( 'Y-m-d' ) 
				);
				
				updateSubscriptionPlan ( $data );
				
				$sql = "SELECT `user_id` FROM `subscription_plan` WHERE `customer_id` = '$customer_id' LIMIT 1";
				$query = DB::select(DB::raw($sql));
				if(count($query) > 0){
					$result = $query[0];
					$device_ids = '';
					foreach ($query as $row){
						$device_ids.=$row->id.',';
					}
					Device::whereIn('id',explode(rtrim($device_ids,','),','))->update(array('status'=>0));
				}				
		}
	}	
	
}

function processrecurlynotification(){
	$key = trim(Request::input('key'));
	$identifier = Option::getOption('app_secret_key','0');
	if($key == $identifier){
		Recurly_Client::$subdomain = Config::get('constants.RECURLY_SUBDOMAIN');
		Recurly_Client::$apiKey = Config::get('constants.RECURLY_API_KEY');
		
		$post_xml =  file_get_contents('php://input');
		
		$url = "https://collector.leaddyno.com/recurly_push_notification?key=1328a1af21fba752058551aa278651d2a5375063";
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, "xmlRequest=" . $post_xml );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 300 );
		$data = curl_exec ( $ch );
		curl_close ( $ch );
		
		// Zapier Call
		$url = "https://zapier.com/hooks/catch/byse3u/";
		// setting the curl parameters.
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, "xmlRequest=" . $post_xml );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 300 );
		$data = curl_exec ( $ch );
		curl_close ( $ch );
		try{
			$xml = simplexml_load_string ( $post_xml );
			$event = $xml->getName ();
			$account_code = $xml->account->account_code;
			$data = json_encode ( $xml );
			
			switch ($event) {
					// Fire when an account is closed.':
					case 'canceled_account_notification' :
						// code here
						break;
					// Fire when a subscription is canceled. This means the subscription will not renew.
					case 'canceled_subscription_notification' :
						// code here
						break;
					// Fire when a payment attempt is declined by the payment gateway.
					case 'failed_payment_notification' :
						  SubScriptionDetail::where('account_code',$account_code)->update(array('allowed_quantity'=>'1'));
						  $sql = "SELECT `id` FROM `device` WHERE `status` = 1 AND `owner` = (SELECT `user_id` FROM `subscription_detail` WHERE `account_code` = '$account_code' LIMIT 1) LIMIT 18446744073709551615 OFFSET 1";
						  $query = DB::select(DB::raw($sql));
						  if(count($query) > 0){
						  	$device_ids = 0;
						  	foreach($query as $row){
						  		$device_ids .=$row->id.',';
						  	}
						  	
						  	Device::whereIn('id',explode($device_ids,','))->update(array('status'=>0));
			
						  }
						  break;
					case 'expired_subscription_notification' :
						SubScriptionDetail::where('account_code',$account_code)->update(array('allowed_quantity'=>0));
						$user = SubScriptionDetail::where('account_code',$account_code)->select('user_id')->first();
						$user_id = $user->user_id;
						Device::where('owner',$user_id)->update(array('status'=>0));
						break;
			}
			addSubscriptionLog($account_code,$event,$data);
		}catch(Exception $e){
			
		}	
	}else{
		return;
	}
}

function addSubscriptionLog($account_code,$event,$data){
	$record = new SubscriptionLog;
	$record->account_code = $account_code;
	$reocrd->event = $event;
	$record->data = $data;
	return $record->save();
}

function updateSubscriptionPlan($data){
	$output = array();
	
	$customer_id = $data['customer_id'];
	$subscrpition_plan_id = 0;
	
    $result = SubscriptionPlan::select('subscription_plan_id')->where('customer_id',$customer_id)->first();
    if($result){
    	$subscrpition_plan_id = $result->subscription_plan_id;
    }
    $data['currency'] ='$';
    $nums = SubscriptionPlan::where('subscription_plan_id',$subscrpition_plan_id)->update($data);
    $output['update_subscription_plan'] = $num > 0?'succeeded':'failed';
    $data['created_at']  = date('Y-m-d H:m:s');
    $data['subscription_id'] = $subscrpition_plan_id;
    $id = SubscriptionPlan::insert($data);
    $output['add_subscription_log'] = $id > 0? 'succeeded' : 'failed';
    return json_encode($output);    
}
?>





























