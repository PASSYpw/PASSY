<?php
require_once __DIR__ . "/../../include/passwords.inc.php";
header("Content-Type: application/json");

if (!isLoggedIn())
    die(getError("no_login", "get_password_list"));

echo getPasswordList($_SESSION["userid"]);
