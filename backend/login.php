<?php
define("END", "BACK");
require_once __DIR__ . "/../include/user.inc.php";
require_once __DIR__ . "/../include/json.inc.php";
require_once __DIR__ . "/../include/recaptcha.inc.php";

if (isLoggedIn())
    die(getError("already_logged_in", "login_user"));

if (!isset($_POST["login_email"]) || !isset($_POST["login_password"]))
    die(getError("missing_arguments", "login_user"));

$email = $_POST["login_email"];
$password = $_POST["login_password"];
$email = trim($email);
$email = strtolower($email);

if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    die(getError("invalid_email", "login_user"));

die(loginUser($email, $password));
