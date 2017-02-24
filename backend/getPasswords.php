<?php
define("END", "BACK");
require_once __DIR__ . "/../include/passwords.inc.php";
require_once __DIR__ . "/../include/json.inc.php";

if (!isLoggedIn())
    die(getError("no_login", "get_password_list"));

echo getPasswordList($_SESSION["userid"]);
