<?php
// Configuration for the Halo CE Reporting application

// Set a server verification key here that will be checked to verify incoming input.
// DO NOT SHARE THIS WITH ANYONE!!!!!
$check_key = '';

// Extra security. Uncomment the SUPER_SECURE constant definition, and enter allowed IP Addresses into the array that follows. Most of the time, just the check_key should be secure enough.
// Use this if that extra security is desired. Be sure to disable this when testing out the script, otherwise you'll just get a forbidden error. Make sure to remove the examples in the
// array before using. Wildcards and ranges are not possible yet.

// define('SUPER_SECURE', true);
$allowed_ips = array(
	'111.222.333.444',
	'8.8.8.8',
	'8.8.4.4',
);

// URL for the webhook.
$wh_url = '';
?>