<?php
session_start();
require_once __DIR__ . "/mysql.inc.php";
require_once __DIR__ . "/json.inc.php";
require_once __DIR__ . "/format.inc.php";
require_once __DIR__ . "/geoip.inc.php";

function loginUser($email, $password)
{
    if (!apc_exists("login_attempts_" . $email))
        apc_store("login_attempts_" . $email, 0);

    if (apc_fetch("login_attempts_" . $email) >= 5)
        return getError("account_locked", "login_user");

    $conn = getMYSQL();
    $ps = $conn->prepare("SELECT * FROM `users` WHERE `EMAIL` = (?)");
    $ps->bind_param("s", $email);
    $succeeded = $ps->execute();
    $result = $ps->get_result();
    $ps->close();
    if ($succeeded) {
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $passwordHash = hash("SHA256", $password . $row["SALT"]);
            if ($passwordHash == $row['PASSWORD']) {
                apc_store("login_attempts_" . $email, 0);
                $_SESSION["email"] = $email;
                $_SESSION["masterPassword"] = hash("SHA256", $password . $row['USERID']);
                $_SESSION["userid"] = $row['USERID'];
                $_SESSION["ip"] = $_SERVER["REMOTE_ADDR"];

                //Log IP Address
                $ps = $conn->prepare("INSERT INTO `iplog` (`USERID`, `IP`, `USERAGENT`, `DATE`) VALUES (?,?,?,?)");
                $ps->bind_param("ssss", $_SESSION["userid"], $_SESSION["ip"], $_SERVER['HTTP_USER_AGENT'], time());
                $succeeded = $ps->execute();
                $ps->close();
                if ($succeeded) {
                    return getSuccess(null, "login_user");
                } else {
                    logoutUser();
                    return getError("database_" . $ps->errno, "login_user");
                }
            }
        }
        $attempts = apc_fetch("login_attempts_" . $email);
        $attempts++;
        apc_store("login_attempts_" . $email, $attempts);
        logoutUser();
        return getError("invalid_credentials", "login_user");
    }
    logoutUser();
    return getError("database_" . $ps->errno, "login_user");
}

function registerUser($email, $password)
{
    $salt = hash("SHA256", microtime());
    $passwordHash = hash("SHA256", $password . $salt);
    $conn = getMYSQL();
    $ps = $conn->prepare("SELECT * FROM `users` WHERE `EMAIL` = (?)");
    $ps->bind_param("s", $email);
    $succeeded = $ps->execute();
    $result = $ps->get_result();
    $ps->close();
    if ($succeeded) {
        if ($result->num_rows == 0) {
            $userId = uniqid("user_");
            $ps = $conn->prepare("INSERT INTO `users` (`EMAIL`, `USERID`, `PASSWORD`, `SALT`) VALUES (?,?,?,?)");
            $ps->bind_param("ssss", $email, $userId, $passwordHash, $salt);
            $succeeded = $ps->execute();
            $ps->close();
            if ($succeeded) {
                return getSuccess(null, "register_user");
            }
            return getError("database_" . $ps->errno, "register_user");
        }
        return getError("already_registered", "register_user");
    }
    return getError("database_" . $ps->errno, "register_user");
}

function getIPLog($userId) {
    $conn = getMYSQL();
    $ps = $conn->prepare("SELECT * FROM `iplog` WHERE `USERID` = (?) ORDER BY `DATE` DESC");
    $ps->bind_param("s", $userId);
    $succeeded = $ps->execute();
    $result = $ps->get_result();
    $ps->close();
    if($succeeded) {
        $data = array();
        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $geoIP = geoIP($row ["IP"]);
                $entry = array(
                    "ip" => $row["IP"],
                    "date" => $row["DATE"],
                    "date_nice" => formatTime($row["DATE"]),
                    "user_agent" => $row["USERAGENT"],
                    "country" => $geoIP["country_name"],
                    "country_code" => $geoIP["country_code"],
                    "region" => $geoIP["region_name"],
                    "region_code" => $geoIP["region_code"],
                    "city" => $geoIP["city"]
                );
                array_push($data, $entry);
            }
        }
        return getSuccess($data, "get_iplog");
    }
    return getError("database_" . $ps->errno, "get_iplog");
}

function logoutUser()
{
    session_destroy();
}

function isLoggedIn()
{
    if (!isset($_SESSION["email"]) || !isset($_SESSION["masterPassword"]) || !isset($_SESSION["ip"]) || !isset($_SESSION["userid"])) {
        return false;
    }
    if ($_SESSION["ip"] != $_SERVER["REMOTE_ADDR"]) {
        logoutUser();
        return false;
    }
    return true;
}
