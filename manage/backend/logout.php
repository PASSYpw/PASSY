<?php
require_once __DIR__ . "/../../include/user.inc.php";
require_once __DIR__ . "/../../include/json.inc.php";
header("Content-Type: application/json");

logoutUser();
die(getSuccess(null, "logout_user"));
