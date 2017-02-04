<?php
require_once __DIR__ . "/../../include/user.inc.php";
header("Content-Type: application/json");

if (!isLoggedIn())
    die(getError("no_login", "get_iplog"));

echo getIPLog($_SESSION["userid"]);
