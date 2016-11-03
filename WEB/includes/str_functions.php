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
 * Function to take a string of comma seperated bytes, and transform it into an actual text.
 * @param		string		$string		A string of comma seperated bytes.
 * @return		string					A textual string.
 */
function translate_bytes_string($string)
{
	// http://stackoverflow.com/questions/14148054/php-get-string-text-from-bytes
	// http://stackoverflow.com/questions/40356291/array-map-causing-issues-with-extended-ascii-for-chr-php
	// http://stackoverflow.com/questions/32864975/php-chr-function-issue-with-special-characters

	// String to array.
	$str_bytes_ary = explode(',', $string);

	// Array map each value to chr.
	$str_bytes_ary = array_map('chr', $str_bytes_ary);

	// Array back to string, and unset.
	$new_string = implode($str_bytes_ary);
	unset($str_bytes_ary);

	// UTF-8 Encode.
	$new_string = utf8_encode($new_string);

	return $new_string;
}

/**
 * Function to escape Discord syntax.
 * @param		string		$string		The string to escape.
 * @return		string					The escaped string.
 */
function escape_discord_string($string)
{
	$string_ary = str_split($string);

	foreach ($string_ary as $key => $value)
	{
		if ($value == '`')
		{
			// Escape it with a backslash.
			$string_ary[$key] = '\\' . $value;
		}
	}

	// Reset the string.
	$string = implode($string_ary);
	unset($string_ary);		// Unset the string array.

	return $string;
}
