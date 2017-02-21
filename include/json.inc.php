<?php

if (defined("END") && END == "BACK")
    header("Content-Type: application/json");

function getSuccess($data, $context)
{
    $json = array(
        "success" => true,
        "msg" => "success",
        "response" => $context,
        "data" => $data
    );
    return json_encode($json, JSON_UNESCAPED_UNICODE);
}

function getError($errorString, $context)
{
    $json = array(
        "success" => false,
        "msg" => $errorString,
        "response" => $context,
        "data" => array()
    );
    return json_encode($json);
}
