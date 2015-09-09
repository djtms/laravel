<?php
require_once('lib/recurly.php');

// Required for the API
Recurly_Client::$subdomain = 'mywifi';
Recurly_Client::$apiKey = '5abeced3c6bc44c8ac971f96a22aceb0';


try{
	//$subscription = Recurly_Subscription::get('abcdef01234567890abcdef01234567890');
	/* fetch the account */
	//$account = $subscription->account->get();
	//print $account->account_code;
	
	/*$subscription = new Recurly_Subscription();
	$subscription->plan_code = 'mywifi-2';
	$subscription->currency = 'USD';

	$account = new Recurly_Account();
	$account->account_code = '1';
	$account->username = 'verena@example.com';
	$account->first_name = 'Verena';
	$account->last_name = 'Example';
	$account->email = 'verena@example.com';
	
	$billing_info = new Recurly_BillingInfo();
	$billing_info->address1 = "6th floor, Shahjalal Complex, Malibugh";
	$billing_info->address2 = "NA";
	$billing_info->city = "Dhaka";
	$billing_info->state = "Malibugh";
	$billing_info->country = "BD";
	$billing_info->zip = "1217";
	$billing_info->phone = "01750000540";
	$billing_info->ip_address = $_SERVER ['REMOTE_ADDR'];
	$billing_info->number = '4111-1111-1111-1111';
	$billing_info->month = 10;
	$billing_info->year = 2015;

	$account->billing_info = $billing_info;
	
	$subscription->account = $account;
	$subscription->create();
	echo "<pre>";print_r($subscription);*/
	/*$plans_data = '{"mywifi-2":{"plan_code":"mywifi-2","name":"MyWiFi-2","accounting_code":"mywifi-2","unit_amount_in_cents":3000,"display_quantity":2},"mywifi-3":{"plan_code":"mywifi-3","name":"MyWiFi-3","accounting_code":"mywifi-3","unit_amount_in_cents":6000,"display_quantity":3},"mywifi-4":{"plan_code":"mywifi-4","name":"MyWiFi-4","accounting_code":"mywifi-4","unit_amount_in_cents":9000,"display_quantity":4},"mywifi-5":{"plan_code":"mywifi-5","name":"MyWiFi-5","accounting_code":"mywifi-5","unit_amount_in_cents":12000,"display_quantity":5},"mywifi-6":{"plan_code":"mywifi-6","name":"MyWiFi-6","accounting_code":"mywifi-6","unit_amount_in_cents":15000,"display_quantity":6},"mywifi-7":{"plan_code":"mywifi-7","name":"MyWiFi-7","accounting_code":"mywifi-7","unit_amount_in_cents":18000,"display_quantity":7},"mywifi-8":{"plan_code":"mywifi-8","name":"MyWiFi-8","accounting_code":"mywifi-8","unit_amount_in_cents":21000,"display_quantity":8},"mywifi-9":{"plan_code":"mywifi-9","name":"MyWiFi-9","accounting_code":"mywifi-9","unit_amount_in_cents":24000,"display_quantity":9},"mywifi-10":{"plan_code":"mywifi-10","name":"MyWiFi-10","accounting_code":"mywifi-10","unit_amount_in_cents":27000,"display_quantity":10},"mywifi-11":{"plan_code":"mywifi-11","name":"MyWiFi-11","accounting_code":"mywifi-11","unit_amount_in_cents":30000,"display_quantity":11},"mywifi-12":{"plan_code":"mywifi-12","name":"MyWiFi-12","accounting_code":"mywifi-12","unit_amount_in_cents":33000,"display_quantity":12},"mywifi-13":{"plan_code":"mywifi-13","name":"MyWiFi-13","accounting_code":"mywifi-13","unit_amount_in_cents":36000,"display_quantity":13},"mywifi-14":{"plan_code":"mywifi-14","name":"MyWiFi-14","accounting_code":"mywifi-14","unit_amount_in_cents":39000,"display_quantity":14},"mywifi-15":{"plan_code":"mywifi-15","name":"MyWiFi-15","accounting_code":"mywifi-15","unit_amount_in_cents":42000,"display_quantity":15},"mywifi-16":{"plan_code":"mywifi-16","name":"MyWiFi-16","accounting_code":"mywifi-16","unit_amount_in_cents":45000,"display_quantity":16},"mywifi-17":{"plan_code":"mywifi-17","name":"MyWiFi-17","accounting_code":"mywifi-17","unit_amount_in_cents":48000,"display_quantity":17},"mywifi-18":{"plan_code":"mywifi-18","name":"MyWiFi-18","accounting_code":"mywifi-18","unit_amount_in_cents":51000,"display_quantity":18},"mywifi-19":{"plan_code":"mywifi-19","name":"MyWiFi-19","accounting_code":"mywifi-19","unit_amount_in_cents":54000,"display_quantity":19},"mywifi-20":{"plan_code":"mywifi-20","name":"MyWiFi-20","accounting_code":"mywifi-20","unit_amount_in_cents":57000,"display_quantity":20},"mywifi-21":{"plan_code":"mywifi-21","name":"MyWiFi-21","accounting_code":"mywifi-21","unit_amount_in_cents":60000,"display_quantity":21},"mywifi-22":{"plan_code":"mywifi-22","name":"MyWiFi-22","accounting_code":"mywifi-22","unit_amount_in_cents":63000,"display_quantity":22},"mywifi-23":{"plan_code":"mywifi-23","name":"MyWiFi-23","accounting_code":"mywifi-23","unit_amount_in_cents":66000,"display_quantity":23},"mywifi-24":{"plan_code":"mywifi-24","name":"MyWiFi-24","accounting_code":"mywifi-24","unit_amount_in_cents":69000,"display_quantity":24},"mywifi-25":{"plan_code":"mywifi-25","name":"MyWiFi-25","accounting_code":"mywifi-25","unit_amount_in_cents":72000,"display_quantity":25},"mywifi-26":{"plan_code":"mywifi-26","name":"MyWiFi-26","accounting_code":"mywifi-26","unit_amount_in_cents":75000,"display_quantity":26},"mywifi-27":{"plan_code":"mywifi-27","name":"MyWiFi-27","accounting_code":"mywifi-27","unit_amount_in_cents":78000,"display_quantity":27},"mywifi-28":{"plan_code":"mywifi-28","name":"MyWiFi-28","accounting_code":"mywifi-28","unit_amount_in_cents":81000,"display_quantity":28},"mywifi-29":{"plan_code":"mywifi-29","name":"MyWiFi-29","accounting_code":"mywifi-29","unit_amount_in_cents":84000,"display_quantity":29},"mywifi-30":{"plan_code":"mywifi-30","name":"MyWiFi-30","accounting_code":"mywifi-30","unit_amount_in_cents":87000,"display_quantity":30},"mywifi-31":{"plan_code":"mywifi-31","name":"MyWiFi-31","accounting_code":"mywifi-31","unit_amount_in_cents":90000,"display_quantity":31},"mywifi-32":{"plan_code":"mywifi-32","name":"MyWiFi-32","accounting_code":"mywifi-32","unit_amount_in_cents":93000,"display_quantity":32},"mywifi-33":{"plan_code":"mywifi-33","name":"MyWiFi-33","accounting_code":"mywifi-33","unit_amount_in_cents":96000,"display_quantity":33},"mywifi-34":{"plan_code":"mywifi-34","name":"MyWiFi-34","accounting_code":"mywifi-34","unit_amount_in_cents":99000,"display_quantity":34},"mywifi-35":{"plan_code":"mywifi-35","name":"MyWiFi-35","accounting_code":"mywifi-35","unit_amount_in_cents":102000,"display_quantity":35},"mywifi-36":{"plan_code":"mywifi-36","name":"MyWiFi-36","accounting_code":"mywifi-36","unit_amount_in_cents":105000,"display_quantity":36},"mywifi-37":{"plan_code":"mywifi-37","name":"MyWiFi-37","accounting_code":"mywifi-37","unit_amount_in_cents":108000,"display_quantity":37},"mywifi-38":{"plan_code":"mywifi-38","name":"MyWiFi-38","accounting_code":"mywifi-38","unit_amount_in_cents":111000,"display_quantity":38},"mywifi-39":{"plan_code":"mywifi-39","name":"MyWiFi-39","accounting_code":"mywifi-39","unit_amount_in_cents":114000,"display_quantity":39},"mywifi-40":{"plan_code":"mywifi-40","name":"MyWiFi-40","accounting_code":"mywifi-40","unit_amount_in_cents":117000,"display_quantity":40},"mywifi-41":{"plan_code":"mywifi-41","name":"MyWiFi-41","accounting_code":"mywifi-41","unit_amount_in_cents":120000,"display_quantity":41},"mywifi-42":{"plan_code":"mywifi-42","name":"MyWiFi-42","accounting_code":"mywifi-42","unit_amount_in_cents":123000,"display_quantity":42},"mywifi-43":{"plan_code":"mywifi-43","name":"MyWiFi-43","accounting_code":"mywifi-43","unit_amount_in_cents":126000,"display_quantity":43},"mywifi-44":{"plan_code":"mywifi-44","name":"MyWiFi-44","accounting_code":"mywifi-44","unit_amount_in_cents":129000,"display_quantity":44},"mywifi-45":{"plan_code":"mywifi-45","name":"MyWiFi-45","accounting_code":"mywifi-45","unit_amount_in_cents":132000,"display_quantity":45},"mywifi-46":{"plan_code":"mywifi-46","name":"MyWiFi-46","accounting_code":"mywifi-46","unit_amount_in_cents":135000,"display_quantity":46},"mywifi-47":{"plan_code":"mywifi-47","name":"MyWiFi-47","accounting_code":"mywifi-47","unit_amount_in_cents":138000,"display_quantity":47},"mywifi-48":{"plan_code":"mywifi-48","name":"MyWiFi-48","accounting_code":"mywifi-48","unit_amount_in_cents":141000,"display_quantity":48},"mywifi-49":{"plan_code":"mywifi-49","name":"MyWiFi-49","accounting_code":"mywifi-49","unit_amount_in_cents":144000,"display_quantity":49},"mywifi-50":{"plan_code":"mywifi-50","name":"MyWiFi-50","accounting_code":"mywifi-50","unit_amount_in_cents":147000,"display_quantity":50},"mywifi-51":{"plan_code":"mywifi-51","name":"MyWiFi-51","accounting_code":"mywifi-51","unit_amount_in_cents":150000,"display_quantity":51}}';
	//echo "<pre>"; print_r(); echo "</pre>";
	$plans = json_decode($plans_data);
	$count = 0;
	foreach($plans as $my_plan){
		$plan = new Recurly_Plan();
		$plan->plan_code = $my_plan->plan_code;
		$plan->name = $my_plan->name;
		$plan->unit_amount_in_cents->addCurrency('USD', $my_plan->unit_amount_in_cents); // USD 10.00 month
		$plan->plan_interval_unit = 'months';
		$plan->accounting_code = $my_plan->accounting_code;
		$plan->display_quantity = $my_plan->display_quantity;
		$plan->tax_exempt = false;
		$plan->create();
		$count ++;
	}
	echo "Total Plan Created: ".$count;*/
	
	//$account = Recurly_Account::get('write2me@example.com');
	//echo "<pre>"; print_r($account); echo "</pre>";
	//$plan = Recurly_Plan::get('mywifi-founders');
	//echo "<pre>"; print_r($plan); echo "</pre>";
	
	/*$subscriptions = Recurly_SubscriptionList::getForAccount('write2me@example.com');
	foreach ($subscriptions as $subscription) {
		//
		//echo $subscription->plan->state;
		if($subscription->state == "active"){
			$created_at = json_decode(json_encode($subscription->activated_at));
			
			echo "<pre>"; print_r($created_at->date); echo "</pre>";
			//
			//
			//echo $created_at->date;
			//echo "SubscriptionID: ".$subscription->uuid;
			//echo "<br>activated_at: ".$subscription->activated_at->date;
			//echo "<br>current_period_started_at: ".isset($subscription->current_period_started_at->date) ? $subscription->current_period_started_at->date : "Noting found.";
			//echo "<br>current_period_ends_at: ".$subscription->current_period_ends_at->date;
			//echo "<pre>"; print_r($subscription->activated_at->date); echo "</pre>";
		}
	}*/
	/*$plans = Recurly_PlanList::get();
	$my_plan = array();
	foreach ($plans as $plan) {
	  //echo "<pre>"; print_r($plan); echo "</pre>";
	  $my_plan[str_replace('-', '_', $plan->plan_code)] = $plan->unit_amount_in_cents->USD->amount_in_cents;
	  //str_replace('-', '_', $plan->plan_code)
	}
	echo "<pre>"; print_r($my_plan); echo "</pre>";*/
	$subscription = Recurly_Subscription::get('2d95b050c9544e00de09bd4412931792');
	$subscription->plan_code = 'mywifi-3';
	$subscription->updateImmediately();     // Update immediately.
	
	echo "<pre>"; print_r($subscription); echo "</pre>";
}catch (Recurly_NotFoundError $e) {
  print "Account not found.\n";
}
catch (Recurly_ValidationError $e) {
  // If there are multiple errors, they are comma delimited:
  $messages = explode(',', $e->getMessage());
  echo "<pre>"; print_r($messages); echo "</pre>";
  //print 'Validation problems: ' . implode("\n", $messages);
}
catch (Recurly_ServerError $e) {
  print 'Problem communicating with Recurly';
}
catch (Exception $e) {
  // You could use send these messages to a log for later analysis.
  print $e->getMessage();
}