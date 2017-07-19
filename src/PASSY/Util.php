<?php

namespace PASSY;


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

	static function filterStrings($string) {
		return htmlspecialchars($string);
	}

}
