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

	static function filterStrings($string) {
		return htmlspecialchars($string);
	}

	static function getSize($arr) {
		$tot = 0;
		foreach($arr as $a) {
			if (is_array($a)) {
				$tot += Util::getSize($a);
			}
			if (is_string($a)) {
				$tot += strlen($a);
			}
			if (is_int($a)) {
				$tot += PHP_INT_SIZE;
			}
		}
		return $tot;
	}
}
