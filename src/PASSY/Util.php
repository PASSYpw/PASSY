<?php

namespace PASSY;

/**
 * Class Util
 * @author Sefa Eyeoglu <contact@scrumplex.net>
 * @package PASSY
 */
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

    static function handleException($exception)
    {
        error_log($exception);
        $response = new Response(false, "server_error");
        die($response->getJSONResponse());
    }

    /**
     * Replaces special characters with HTML codes
     * @param $string string input string
     * @return string output string
     */
    static function filterStrings($string)
    {
        return htmlspecialchars($string);
    }

}
