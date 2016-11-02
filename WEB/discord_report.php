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

// Includes go here for things like functions and configuration files.
require_once('config.php');
require_once('includes/str_functions.php');

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
		$data['sv_name'] = $_GET['sv_name'];
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
			$data['sv_name'] = addslashes($data['sv_name']);
			$data['sv_ip'] = addslashes($data['sv_ip']);
			$data['sv_reporter'] = addslashes($data['sv_reporter']);
			$data['sv_reportee'] = addslashes($data['sv_reportee']);
			$data['sv_verification_key'] = addslashes($data['sv_verification_key']);
			$data['snitch_hash'] = addslashes($data['snitch_hash']);
			$data['snitch_ip'] = addslashes($data['snitch_ip']);
			$data['snitch_msg'] = addslashes($data['snitch_msg']);
			$data['defendant_hash'] = addslashes($data['defendant_hash']);
			$data['defendant_ip'] = addslashes($data['defendant_ip']);
		}

		// The URL should be formed like this: http://www.site.tld/discord_report.php
		// ?mode="report"
		// &sv_name="SERVER NAME"
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
		if ($sv_verification_key != $data['check_key'])
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
					'color'			=>	get_color_info($data['sv_ip']),
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

// Send the webhook payload.
$wh = curl_init();

$wh_opts = array(
	CURLOPT_URL				=>	($mode == 'report') ? $report_wh_url : $notify_wh_url,
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
unset($data);

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

/**
 * Generate the color that should be used for the embed in the Webhook. If no color is found in the config,
 * use the default setting.
 *
 * @param	string	$sv_ip	The IP of the server to get the embed color information for.
 * @return	int				Return a DEC value for the requested HEX Code.
 */
function get_color_info($sv_ip)
{
	require_once('config.php');

	if (!isset($sv_colors[$sv_ip][$mode]))
	{
		if (isset($sv_colors['default'][$mode]))
		{
			$hex = $sv_colors['default'][$mode];
		} else {
			// No values found for this mode, just use black.
			$hex = '000000';
		}
	} else {
		// Use the defined color.
		$hex = $sv_colors[$sv_ip][$mode];
	}

	$dec = hexdec($hex);

	return $dec;
}

