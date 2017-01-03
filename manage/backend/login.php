<?php
require_once __DIR__ . "/../../include/user.inc.php";
if (!isLoggedIn()) {
    if (loginUser($_POST["login_email"], hash("sha512", $_POST["login_password"]))) {
        die("success");
    } else {
        die("userpass_wrong");
    }
}