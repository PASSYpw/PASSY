<?php
session_start();
require_once __DIR__ . "/mysql.inc.php";

function loginUser($email, $passwordHash)
{
    $conn = getMYSQL();
    $user = userExists($conn, $email);
    $conn->close();
    if ($user != null) {
        if ($passwordHash == $user['PASSWORD']) {
            $_SESSION["email"] = $email;
            $_SESSION["passwordHash"] = $passwordHash;
            $_SESSION["fullName"] = $user["FULLNAME"];
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

function registerUser($email, $passwordHash)
{
    $conn = getMYSQL();
    if (!userExists($conn, $email)) {
        $ps = $conn->prepare("INSERT INTO `users` (`EMAIL`, `PASSWORD`) VALUES (?,?)");
        $ps->bind_param("ss", $email, $passwordHash);
        $succeeded = $ps->execute();
        $result = $ps->get_result();
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
    if (!isset($_SESSION["email"]) || !isset($_SESSION["passwordHash"]) || !isset($_SESSION["ip"])) {
        return 0;
    }
    if ($_SESSION["ip"] != $_SERVER["REMOTE_ADDR"]) {
        return -1;
    }
    return 1;
}