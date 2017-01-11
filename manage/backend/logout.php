<?php
header("Content-Type: text/plain");
require_once __DIR__ . "/../../include/user.inc.php";
logoutUser();
header("Location: /");
