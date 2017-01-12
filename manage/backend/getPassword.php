<?php
require_once __DIR__ . "/../../include/passwords.inc.php";
header("Content-Type: application/json");

if (!isLoggedIn())
    die(getError("no_login", "get_password"));

if (!isset($_POST["id"]))
    die(getError("missing_arguments", "get_password"));

die(getPassword($_SESSION["userid"], $_POST["id"], $_SESSION["masterPassword"]));
