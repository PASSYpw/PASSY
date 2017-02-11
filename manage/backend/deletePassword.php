<?php
define("END", "BACK");
require_once __DIR__ . "/../../include/passwords.inc.php";
require_once __DIR__ . "/../../include/json.inc.php";

if (!isLoggedIn())
    die(getError("no_login", "delete_password"));

if (!isset($_POST["id"]))
    die(getError("missing_arguments", "delete_password"));

die(deletePassword($_SESSION["userid"], $_POST["id"]));
