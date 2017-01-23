<?php
require_once __DIR__ . "/../../include/user.inc.php";
require_once __DIR__ . "/../../include/json.inc.php";
require_once __DIR__ . "/../../include/recaptcha.inc.php";
header("Content-Type: application/json");

if (isLoggedIn())
    die(getError("already_logged_in", "register_user"));

if (!isset($_POST["register_email"]) || !isset($_POST["register_password"]) || !isset($_POST["g-recaptcha-response"]))
    die(getError("missing_arguments", "register_user"));

$recaptcha = new \ReCaptcha\ReCaptcha("6LeUfBEUAAAAANw4SOK1QTk6fTLqeqYbiIJneFfD");

$recaptchaResponse = $_POST["g-recaptcha-response"];
$remoteIp = $_SERVER["REMOTE_ADDR"];

$resp = $recaptcha->verify($recaptchaResponse, $remoteIp);
if (!$resp->isSuccess())
    die(getError("verification_failed", "register_user"));

$email = $_POST["register_email"];
$password = $_POST["register_password"];
$email = trim($email);
$email = strtolower($email);

if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    die(getError("invalid_email", "register_user"));

die(registerUser($email, $password));
