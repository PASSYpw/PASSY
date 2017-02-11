<?php
define("END", "BACK");
require_once __DIR__ . "/../../include/user.inc.php";
require_once __DIR__ . "/../../include/json.inc.php";

logoutUser();
die(getSuccess(null, "logout_user"));
