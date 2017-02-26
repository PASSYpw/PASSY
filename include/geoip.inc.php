<?php
require_once __DIR__ . "/config.inc.php";

function geoIP($ip)
{
    global $global;
    if(!$global["geoip"]["enabled"])
        return array();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://" . $global["geoip"]["ip_and_port"] . "/json/" . $ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    if (!$output) {
        return array();
    }

    return json_decode($output, true);
}
