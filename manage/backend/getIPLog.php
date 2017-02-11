<?php
define("END", "BACK");
require_once __DIR__ . "/../../include/user.inc.php";
require_once __DIR__ . "/../../include/json.inc.php";

if (!isLoggedIn())
    die(getError("no_login", "get_iplog"));

echo getIPLog($_SESSION["userid"]);
