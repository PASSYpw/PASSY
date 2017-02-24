<?php
define("END", "BACK");
require_once __DIR__ . "/../include/passwords.inc.php";
require_once __DIR__ . "/../include/json.inc.php";

if (!isLoggedIn())
    die(getError("no_login", "get_password"));

if (!isset($_POST["id"]))
    die(getError("missing_arguments", "get_password"));

die(getPassword($_SESSION["userid"], $_POST["id"], $_SESSION["masterPassword"]));
