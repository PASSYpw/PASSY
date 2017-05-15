<?php

namespace Scrumplex\PASSY;

class Util
{
	static function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return $length == 0 ? true : (substr($haystack, 0, $length) === $needle);
	}

	static function endsWith($haystack, $needle)
	{
		$length = strlen($needle);

		return $length == 0 ? true : (substr($haystack, -$length) === $needle);
	}

	static function validateURL($string) {
		return filter_var($string, FILTER_VALIDATE_URL) !== false;
	}

	static function filterBadChars($string) {
		return htmlspecialchars($string);
	}

}
