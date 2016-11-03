<?php
/**
 * Provides an interface for the Halo servers to interact with Discord's webhooks for user reporting.
 *
 * @copyright (c) LaxSlash <https://www.github.com/LaxSlash>
 * @license GNU General Public License, version 3 (GPL-3.0)
 *
 */

// We need to set the headers for the chr() function to properly work.
header('Content-type: text/html; charset=utf-8');

// We're actually in the application. Everything is safe.
define('IN_REPORTER', true);

// Includes go here for things like functions and configuration files.
require('includes/config.php');
require_once('includes/functions.php');

// Check that the requesting IP Address is an allowed IP Address.
if (defined('SUPER_SECURE'))
{
	if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips));
	{
		die('Forbidden.');
	}
}

// Setup variables
$data = array();
$mode = $_GET['mode'];

switch($mode)
{
	case 'report':
		$data['sv_name'] = translate_bytes_string($_GET['sv_name']);
		$data['sv_ip'] = $_GET['sv_ip'];
		$data['sv_reporter'] = translate_bytes_string($_GET['snitch']);
		$data['sv_reportee'] = translate_bytes_string($_GET['defendant']);
		$data['sv_verification_key'] = $_GET['verify_key'];
		$data['snitch_hash'] = $_GET['snitch_hash'];
		$data['snitch_ip'] = $_GET['snitch_ip'];
		$data['snitch_msg'] = translate_bytes_string($_GET['snitch_msg']);
		$data['defendant_hash'] = $_GET['defendant_hash'];
		$data['defendant_ip'] = $_GET['defendant_ip'];

		// Escape all data if not already done
		if (get_magic_quotes_gpc() == false)
		{
			foreach ($data as $key => $value)
			{
				$data[$key] = addslashes($data[$key]);
			}
		}

		// The URL should be formed like this: http://www.site.tld/discord_report.php
		// ?mode="report"
		// &sv_name=120,121,122
		// &sv_ip=123.123.123.123:1234
		// &snitch=120,121,122
		// &defendant=120,121,122
		// &verify_key=thisIsSomeKey
		// &snitch_hash=lettershere
		// &snitch_ip=123.123.123.123
		// &snitch_msg=120,121,122
		// &defendant_hash=someMoreLettersGoHere
		// &defendant_ip=123.123.123.123

		// Basic Verification
		if ($data['sv_verification_key'] != $check_key)
		{
			die('Incorrect verification key.');
		}

		if ($data['sv_name'] == ""
			||
			$data['sv_reporter'] == ""
			||
			$data['sv_reportee'] == ""
			||
			$data['snitch_hash'] == ""
			||
			$data['snitch_ip'] == ""
			||
			$data['defendant_hash'] == ""
			||
			$data['defendant_ip'] == ""
			||
			$data['snitch_msg'] == ""
			)
		{
			die('One or more required fields are missing.');
		}

		// Create the actual webhook payload.
		$payload_ary = array(
			'content'	=>	'Administrator requested on server ' . $data['sv_name'],
			'embeds'	=>	array(
				array(
					'type'			=>	'rich',														// This should always be rich, anyways.
					'title'			=>	'Report from ' . $data['sv_reporter'] . ' against ' . $data['sv_reportee'],
					'color'			=>	get_color_info($data['sv_ip'], $mode),
					'description'	=>	$data['snitch_msg'],
					'fields'		=>	array(
						array(
							'name'			=>	'Server IP Address:',
							'value'			=>	$data['sv_ip'],
						),
						array(
							'name'			=>	'Reporter CD Hash:',
							'value'			=>	$data['snitch_hash'],
						),
						array(
							'name'			=>	'Reporter IP Address:',
							'value'			=>	$data['snitch_ip'],
						),
						array(
							'name'			=>	'Suspect CD Hash:',
							'value'			=>	$data['defendant_hash'],
						),
						array(
							'name'			=>	'Suspect IP Address:',
							'value'			=>	$data['defendant_ip'],
						),
					),
				),
			),
		);
	break;
	case 'notify':
	break;
	default:
		exit('Invalid mode selected.');
	break;
}

$payload = json_encode($payload_ary);
unset($payload_ary);

// Get the proper URL here for the IP:Port and the $mode.
$wh_url = get_wh_url($data['sv_ip'], $mode);

// Send the webhook payload.
$result = send_webhook($wh_url, $payload);

unset($data);


// What to return?
if ($wh_result == false)
{
	print('True');
} else {
	if ($wh_result['http'] == '400')
	{
		print('Discord');
	} else {
		print('HTTP');
	}
}

echo '<br />';

if (defined('DEBUG_INFO'))
{
	if ($result == false)
	{
		exit('Webhook sent and posted successfully.');
	} else {
		// Let's print out the result.
		// The actual payload first
		echo '<b>Payload:</b> ' . $payload;
		echo '<br />';

		//Now the HTTP Result goes here
		echo '<b>HTTP Code:</b> ' . $result['http'];
		echo '<br />';
		echo '<b>Discord Returns:</b> ' . $result['discord'];
		unset($result);

		// Show them what they need to do for support.
		echo '<br />';
		echo '<b>For support, please go <a href="https://github.com/LaxSlash/Halo-CE-Discord-Reporter">here</a>, and read the README, check the Wiki, and if help is still needed, create an Issue.</b>';
	}
}
