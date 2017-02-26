<?php
define("END", "BACK");
require_once __DIR__ . "/../include/user.inc.php";
require_once __DIR__ . "/../include/json.inc.php";
require_once __DIR__ . "/../include/config.inc.php";

if (!$global["general"]["enable_login_history"])
    die("disabled");

if (!isLoggedIn())
    die(getError("no_login", "get_iplog"));

echo getIPLog($_SESSION["userid"]);
