<?php
require_once __DIR__ . "/../../include/passwords.inc.php";
header("Content-Type: application/json");

if (!isLoggedIn())
    die(getError("no_login", "archive_password"));

if (!isset($_POST["id"]))
    die(getError("missing_arguments", "archive_password"));

die(archivePassword($_SESSION["userid"], $_POST["id"]));
