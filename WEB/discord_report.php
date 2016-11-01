<?php
/**
 * Provides an interface for the Halo servers to interact with Discord's webhooks for user reporting.
 *
 */

// Includes go here for things like functions and configuration files.
require_once('config.php');

// Check that the requesting IP Address is an allowed IP Address.
if (defined('SUPER_SECURE'))
{
	if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips));
	{
		die('Forbidden.');
	}
}

// Setup variables
$sv_name = $_GET['name'];
$sv_ip = $_GET['sv_ip'];
$sv_reporter = $_GET['snitch'];
$sv_reportee = $_GET['defendant'];
$sv_verification_key = $_GET['verify_key'];
$snitch_hash = $_GET['snitch_hash'];
$snitch_ip = $_GET['snitch_ip'];
$snitch_msg = $_GET['snitch_msg'];
$defendant_hash = $_GET['defendant_hash'];
$defendant_ip = $_GET['defendant_ip'];

// Escape all data if not already done
if (get_magic_quotes_gpc() == false)
{
	$sv_name = addslashes($sv_name);
	$sv_ip = addslashes($sv_ip);
	$sv_reporter = addslashes($sv_reporter);
	$sv_reportee = addslashes($sv_reportee);
	$sv_verification_key = addslashes($sv_verification_key);
	$snitch_hash = addslashes($snitch_hash);
	$snitch_ip = addslashes($snitch_ip);
	$snich_msg = addslashes($snitch_msg);
	$defendant_hash = addslashes($defendant_hash);
	$defendant_ip = addslashes($defendant_ip);
}

// The URL should look like this: http://www.site.tld/discord_report.php
// ?name="SERVER NAME"
// &sv_ip=123.123.123.123:1234
// &snitch="New001"
// &defendant="New002"
// &verify_key=thisIsSomeKey
// &snitch_hash=lettershere
// &snitch_ip=123.123.123.123
// &snitch_msg="Some message goes here."
// &defendant_hash=someMoreLettersGoHere
// &defendant_ip=123.123.123.123

// Basic Verification
if ($sv_verification_key != $check_key)
{
	die('Incorrect verification key.');
}

if ($sv_name == "" || $sv_reporter == "" || $sv_reportee == "" || $snitch_hash == "" || $snitch_ip == "" || $defendant_hash == "" || $defendant_ip == "")
{
	die('One or more required fields are missing.');
}

// Create the actual webhook payload.
$payload_ary = array(
	'content'	=>	'Administrator requested on server ' . $sv_name,
	'embeds'	=>	array(
		array(
			'type'			=>	'rich',														// This should always be rich, anyways.
			'title'			=>	'Report from ' . $sv_reporter . ' against ' . $sv_reportee,
			'description'	=>	$snitch_msg,
			'fields'		=>	array(
				array(
					'name'			=>	'Server IP Address:',
					'value'			=>	$sv_ip,
				),
				array(
					'name'			=>	'Reporter CD Hash:',
					'value'			=>	$snitch_hash,
				),
				array(
					'name'			=>	'Reporter IP Address:',
					'value'			=>	$snitch_ip,
				),
				array(
					'name'			=>	'Reported CD Hash:',
					'value'			=>	$defendant_hash,
				),
				array(
					'name'			=>	'Reported IP Address:',
					'value'			=>	$defendant_ip,
				),
			),
		),
	),
);
$payload = json_encode($payload_ary);
unset($payload_ary);

// Send the webhook payload.
$wh = curl_init();

$wh_opts = array(
	CURLOPT_URL				=>	$wh_url,
	CURLOPT_POST			=>	true,
	CURLOPT_RETURNTRANSFER	=>	true,
	CURLOPT_POSTFIELDS		=>	$payload,
	CURLOPT_HTTPHEADER		=>	array('Content-type: application/json'),
);

curl_setopt_array($wh, $wh_opts);

$wh_result = curl_exec($wh);
$http_result = curl_getinfo($wh, CURLINFO_HTTP_CODE);

unset($wh_opts);
unset($wh);

if (defined('DEBUG_INFO'))
{
	// Let's print out the result.
	// The actual payload first
	print($payload);

	echo '<br />';

	//Now the HTTP Result goes here
	print($http_result);

	$wh_result = json_decode($wh_result);

	echo '<br />';

	print_r($wh_result);

	unset($wh_result);
}

