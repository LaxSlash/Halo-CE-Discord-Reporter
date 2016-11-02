<?php
/**
 * Provides an interface for the Halo servers to interact with Discord's webhooks for user reporting.
 *
 * @copyright (c) LaxSlash <https://www.github.com/LaxSlash>
 * @license GNU General Public License, version 3 (GPL-3.0)
 *
 */

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

// Server colors. The first value should be the IP and Port of the oincoming server. The second value should be the HEX that you want to use (no prefixed # sign).
// Remove the examples and set your own if desired. The default color will be generated based off of the incoming server IP Address.
$sv_colors = array(
	'123.123.123.123:2300'	=>	'000000',
	'123.123.123.123:2301'	=>	'FFFFFF',
);

// Debug info enable/disable. Reccommended disabled for production use. Uncomment to enable.
//define('DEBUG_INFO', true);

?>