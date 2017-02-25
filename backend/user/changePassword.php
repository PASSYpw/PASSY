<?php
define("END", "BACK");
require_once __DIR__ . "/../../include/user.inc.php";
require_once __DIR__ . "/../../include/json.inc.php";

if (!isLoggedIn())
    die(getError("no_login", "change_password"));

if (!isset($_POST["newPassword"]) || !isset($_POST["newPassword2"]) || !isset($_POST["oldPassword"]))
    die(getError("missing_arguments", "change_password"));

if($_POST["newPassword"] != $_POST["newPassword2"])
    die(getError("invalid_email", "change_password"));

die(changePassword($_SESSION["userid"], $_POST["oldPassword"], $_POST["newPassword"]));
