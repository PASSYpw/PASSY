<?php
define("END", "BACK");
require_once __DIR__ . "/../include/passwords.inc.php";
require_once __DIR__ . "/../include/json.inc.php";

if (!isLoggedIn())
    die(getError("no_login", "add_password"));

if (!isset($_POST["password"]))
    die(getError("missing_arguments", "add_password"));

$website = null;
if (isset($_POST["website"]) && strlen($_POST["website"]) > 0) {
    $website = trim($_POST["website"]);
    if (!filter_var($website, FILTER_VALIDATE_URL))
        $website = "http://" . $website;
}

$username = null;
if (isset($_POST["username"]) && strlen($_POST["username"]) > 0)
    $username = trim($_POST["username"]);

$_SESSION["last_request_addPassword"] = time();

die(addPassword($_SESSION["userid"], trim($_POST["password"]), $_SESSION["masterPassword"], $username, $website));
