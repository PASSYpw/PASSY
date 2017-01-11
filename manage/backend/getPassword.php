<?php
header("Content-Type: text/plain");
require_once __DIR__ . "/../../include/passwords.inc.php";
if(isLoggedIn()) {
    if (!isset($_POST["id"]))
        die("invalid_form");

    $result = getPassword($_SESSION["userid"], $_POST["id"], $_SESSION["masterPassword"]);
    if($result != false) {
        die($result);
    }
    die("db_error");
}
die("not_auth");
