<?php

function geoIP($ip)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/json/" . $ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    if (!$output) {
        return array();
    }

    return json_decode($output, true);
}
