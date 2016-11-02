<?php
/**
 * Provides an interface for the Halo servers to interact with Discord's webhooks for user reporting.
 *
 * @copyright (c) LaxSlash <https://www.github.com/LaxSlash>
 * @license GNU General Public License, version 3 (GPL-3.0)
 *
 */

if (!defined('IN_REPORTER'))
{
	exit();
}

/**
 * Send a webhook to Discord.
 *
 * @param	string		$wh_url		The target URL for the webhook.
 * @param	string		$payload	The JSON encoded payload to send off to the webhook.
 * @return	bool|array				Result information to send back to the calling script. True if a successful send (HTTP Status Code: 204)
 */
function send_webhook($wh_url, $payload)
{
	// Initialize cURL.
	$wh = curl_init();

	// Set the options.
	$wh_opts = array(
		CURLOPT_URL				=>	$wh_url,
		CURLOPT_POST			=>	true,
		CURLOPT_POSTFIELDS		=>	$payload,
		CURLOPT_RETURNTRANSFER	=>	true,
		CURLOPT_HTTPHEADER		=>	array('Content-type: application/json'),
	);
	curlopt_set_array($wh, $wh_opts);

	// Execute.
	$wh_result = curl_exec($wh); // This is what comes back from Discord.
	$http_result = curl_getinfo($wh, CURLINFO_HTTP_CODE); // Expect HTTP code 204 for success.

	// Close the cURL session.
	curl_close($wh);

	// Form the return.
	if ($http_result == '204')
	{
		$rtrn = true;
	} else {
		$rtrn = array(
			'discord'	=>	json_decode($wh_result),
			'http'		=>	$http_result,
		);
	}

	// Unset some stuff.
	unset($wh_opts);
	unset($wh);
	unset($wh_result);

	// And now return.
	return $rtrn;
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
	require_once('../config.php');

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