<?php
/**
This Example shows how to Subscribe a New Member to a List using the MCAPI.php 
class and do some basic error checking.
**/
require_once 'MailChimp.php';


$MailChimp = new MailChimp('55baf9365d11ca3ea7f0c5534d9f2962-us10');

#Geting List
/*$results = $MailChimp->call("lists/list"); 
echo "<pre>"; print_r($results['data']); echo "</pre>";*/

#Subscribe a batch of emails.
$results = $MailChimp->call("lists/batch-subscribe", array(
	"id"=>"1737b63b5f", // required, the list id to pull abuse reports for (can be gathered using lists/list())
	"batch"=>array( // required, an array of structs for each address
		array(
				"email" => array( // required, a struct with one of the following keys
				"email" => "mizanur.rahman@smartwebsource.com" // an email address
			),
			"email_type" => "html", // optional, for the email type option (html or text)
			"merge_vars" => array( // optional, data for the various list specific and special merge vars documented in lists/subscribe
				"fname" => "Mizanur",
				"lname" => "Rahman"
			)
		)
	),
	"double_optin" => false, // optional, flag to control whether to send an opt-in confirmation email - defaults to true
	"update_existing" => true, // optional, flag to control whether to update members that are already subscribed to the list or to return an error, defaults to false (return error)
	"replace_interests" => true // optional, flag to determine whether we replace the interest groups with the updated groups provided, or we add the provided groups to the member's interest groups
)); 
echo "<pre>"; print_r($results); echo "</pre>";