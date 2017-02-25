<?php
define("END", "BACK");
require_once __DIR__ . "/../../include/user.inc.php";
require_once __DIR__ . "/../../include/json.inc.php";

if (!isLoggedIn())
    die(getError("no_login", "change_email"));

if (!isset($_POST["newemail"]) || !isset($_POST["password"]))
    die(getError("missing_arguments", "change_email"));

$email = trim($_POST["newemail"]);
$email = strtolower($email);

if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    die(getError("invalid_email", "change_email"));

die(changeEmail($_SESSION["userid"], $_POST["password"], $email));
