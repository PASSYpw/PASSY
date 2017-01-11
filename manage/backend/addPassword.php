<?php
header("Content-Type: text/plain");
require_once __DIR__ . "/../../include/passwords.inc.php";
if(isLoggedIn()) {
    if(!isset($_POST["password"]))
        die("invalid_form");

    $website = null;
    if(isset($_POST["website"])) {
        $website = $_POST["website"];
        if (!filter_var($website, FILTER_VALIDATE_URL))
            $website = "http://" . $website;
    }

    $username = null;
    if(isset($_POST["username"]))
        $username = $_POST["username"];


    $result = addPassword($_SESSION["userid"], $_POST["password"], $_SESSION["masterPassword"], $username ,$website);
    if($result != false) {
        die($result);
    }
    die("database_error");
}
die("not_auth");
