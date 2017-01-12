<?php
require_once __DIR__ . "/../../include/passwords.inc.php";
header("Content-Type: application/json");

if (!isLoggedIn())
    die(getError("no_login", "delete_password"));

if (!isset($_POST["id"]))
    die(getError("missing_arguments", "delete_password"));

die(deletePassword($_SESSION["userid"], $_POST["id"]));
