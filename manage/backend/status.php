<?php
require_once __DIR__ . "/../../include/user.inc.php";
require_once __DIR__ . "/../../include/json.inc.php";

if(isLoggedIn()) {
    $arr = array(
        "user_id" => $_SESSION["userid"],
        "user_email" => $_SESSION["email"]
    );
    die(getSuccess($arr, "status"));
} else {
    die(getError("no_login", "status"));
}
