<?php
require_once __DIR__ . "/../../include/passwords.inc.php";
header("Content-Type: application/json");

if (!isLoggedIn())
    die(getError("no_login", "restore_password"));

if (!isset($_POST["id"]))
    die(getError("missing_arguments", "restore_password"));

die(restorePassword($_SESSION["userid"], $_POST["id"]));
