<?php

# Demonstrates how to add new contact to campaign.

# JSON::RPC module is required
# available at http://github.com/GetResponse/DevZone/blob/master/API/lib/jsonRPCClient.php
require_once 'jsonRPCClient.php';

# your API key is available at
# https://app.getresponse.com/my_api_key.html
$api_key = 'e44f3ce9d79276ec9c2f41b16eee8723';

# API 2.x URL
$api_url = 'http://api2.getresponse.com';

# initialize JSON-RPC client
$client = new jsonRPCClient($api_url);

# find campaign named 'test'
$campaigns = $client->get_campaigns(
    $api_key,
    array (
        # find by name literally
        'name' => array ( 'EQUALS' => 'fev_campaign' )
    )
);
echo "<pre>";print_r($campaigns);
# uncomment following line to preview Response
# print_r($campaigns);

# because there can be only one campaign of this name
# first key is the CAMPAIGN_ID required by next method
# (this ID is constant and should be cached for future use)
//$CAMPAIGN_ID = array_pop(array_keys($campaigns));

# add contact to the campaign
$result = $client->add_contact(
    $api_key,
    array (
        # identifier of 'test' campaign
        'campaign'  => 'VjyfP',
        # basic info
        'name'      => 'Mizanur Rahman',
        'email'     => 'mizanur.rahman@smartwebsource.com',
		'cycle_day' => '0'
    )
);

# uncomment following line to preview Response
# print_r($result);

print("Contact added\n");
echo "<pre>";print_r($result);
# Pawel Pabian http://implix.com

?>
