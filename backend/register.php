<?php
define("END", "BACK");
require_once __DIR__ . "/../include/user.inc.php";
require_once __DIR__ . "/../include/json.inc.php";
require_once __DIR__ . "/../include/config.inc.php";
require_once __DIR__ . "/../vendor/autoload.php";

if (!$config["general"]["enable_register"])
    die("disabled");

if (isLoggedIn())
    die(getError("already_logged_in", "register_user"));

if (!isset($_POST["register_email"]) || !isset($_POST["register_password"]) || !isset($_POST["register_password2"]) || ($config["recaptcha"]["enabled"] && !isset($_POST["g-recaptcha-response"])))
    die(getError("missing_arguments", "register_user"));

if ($config["recaptcha"]["enabled"]) {
    $recaptcha = new \ReCaptcha\ReCaptcha($config["recaptcha"]["secret_key"]);

    $recaptchaResponse = $_POST["g-recaptcha-response"];
    $remoteIp = $_SERVER["REMOTE_ADDR"];

    $resp = $recaptcha->verify($recaptchaResponse, $remoteIp);
    if (!$resp->isSuccess())
        die(getError("verification_failed", "register_user"));
}

$email = $_POST["register_email"];
$password = $_POST["register_password"];
$password2 = $_POST["register_password2"];
$email = trim($email);
$email = strtolower($email);

if ($password != $password2)
    die(getError("passwords_not_match", "register_user"));

if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    die(getError("invalid_email", "register_user"));

die(registerUser($email, $password));
