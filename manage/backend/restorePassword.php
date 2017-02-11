<?php
define("END", "BACK");
require_once __DIR__ . "/../../include/passwords.inc.php";
require_once __DIR__ . "/../../include/json.inc.php";

if (!isLoggedIn())
    die(getError("no_login", "restore_password"));

if (!isset($_POST["id"]))
    die(getError("missing_arguments", "restore_password"));

die(restorePassword($_SESSION["userid"], $_POST["id"]));
