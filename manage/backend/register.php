<?php
header("Content-Type: text/plain");
require_once __DIR__ . "/../../include/user.inc.php";
require_once __DIR__ . "/../../include/recaptcha.inc.php";

if (!isLoggedIn()) {
    if (!isset($_POST["register_email"]) || !isset($_POST["register_password"]) || !isset($_POST["g-recaptcha-response"]))
        die("invalid_form");

    $recaptcha = new \ReCaptcha\ReCaptcha("6LeUfBEUAAAAANw4SOK1QTk6fTLqeqYbiIJneFfD");

    $gRecaptchaResponse = $_POST["g-recaptcha-response"];
    $remoteIp = $_SERVER["REMOTE_ADDR"];

    $resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);
    if (!$resp->isSuccess())
        die("validation_failed");

    $email = $_POST["register_email"];
    $password = $_POST["register_password"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        die("invalid_email");
    if (registerUser($email, $password)) {
        die("success");
    } else {
        die("already_exists");
    }
}
die("logged_in");
