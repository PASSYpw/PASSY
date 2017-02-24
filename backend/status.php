<?php
define("END", "BACK");
define("TRACK_ACTIVITY", false);
require_once __DIR__ . "/../include/user.inc.php";
require_once __DIR__ . "/../include/json.inc.php";

if (isLoggedIn()) {
    $arr = array(
        "user_id" => $_SESSION["userid"],
        "user_email" => $_SESSION["email"],
        "inactivity" => time() - $_SESSION["last_activity"]
    );
    die(getSuccess($arr, "status"));
} else {
    die(getError("no_login", "status"));
}
