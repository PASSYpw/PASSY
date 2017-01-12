<?php
function getSuccess($data, $context)
{
    $json = array(
        "success" => true,
        "msg" => "success",
        "response" => $context,
        "data" => $data
    );
    return json_encode($json);
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