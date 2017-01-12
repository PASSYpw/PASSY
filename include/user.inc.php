<?php
session_start();
require_once __DIR__ . "/mysql.inc.php";
require_once __DIR__ . "/json.inc.php";

function loginUser($email, $password)
{
    $passwordHash = hash("SHA256", $password);
    $conn = getMYSQL();
    $user = userExists($conn, $email);
    $conn->close();
    if ($user != null) {
        if ($passwordHash == $user['PASSWORD']) {
            $_SESSION["email"] = $email;
            $_SESSION["masterPassword"] = hash("SHA256", $password . $user['USERID']);
            $_SESSION["userid"] = $user['USERID'];
            $_SESSION["ip"] = $_SERVER["REMOTE_ADDR"];
            return true;
        }
    }
    return false;
}

function userExists($conn, $email)
{
    $ps = $conn->prepare("SELECT * FROM `users` WHERE `EMAIL` = (?)");
    $ps->bind_param("s", $email);
    $succeeded = $ps->execute();
    $result = $ps->get_result();
    $ps->close();
    if ($succeeded) {
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
    }
    return false;
}

function registerUser($email, $password)
{
    $passwordHash = hash("SHA256", $password);
    $conn = getMYSQL();
    if (!userExists($conn, $email)) {
        $userid = uniqid("user_");
        $ps = $conn->prepare("INSERT INTO `users` (`EMAIL`, `USERID`, `PASSWORD`) VALUES (?,?,?)");
        $ps->bind_param("sss", $email, $userid, $passwordHash);
        $succeeded = $ps->execute();
        $ps->close();
        $conn->close();
        if ($succeeded) {
            return true;
        }
    }
    return false;
}

function logoutUser()
{
    session_destroy();
}

function isLoggedIn()
{
    if (!isset($_SESSION["email"]) || !isset($_SESSION["masterPassword"]) || !isset($_SESSION["ip"]) || !isset($_SESSION["userid"])) {
        return 0;
    }
    if ($_SESSION["ip"] != $_SERVER["REMOTE_ADDR"]) {
        return -1;
    }
    return 1;
}