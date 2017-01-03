<?php
require_once __DIR__ . "/../../include/user.inc.php";
if (!isLoggedIn()) {
    if (registerUser($_POST["register_email"], hash("sha512", $_POST["register_password"]))) {
        die("success");
    } else {
        die("already_exists");
    }
}