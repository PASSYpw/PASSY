<?php
header("Content-Type: text/plain");
require_once __DIR__ . "/../../include/user.inc.php";
if (!isLoggedIn()) {
    if (!isset($_POST["login_email"]) || !isset($_POST["login_password"]))
        die("invalid_form");

    $email = $_POST["login_email"];
    $password = $_POST["login_password"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        die("invalid_email");

    if (loginUser($email, $password)) {
        die("success");
    } else {
        die("userpass_wrong");
    }
}
die("logged_in");
