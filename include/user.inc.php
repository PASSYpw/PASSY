<?php
session_start();
require_once __DIR__ . "/config.inc.php";
require_once __DIR__ . "/mysql.inc.php";
require_once __DIR__ . "/json.inc.php";
require_once __DIR__ . "/format.inc.php";
require_once __DIR__ . "/geoip.inc.php";
require_once __DIR__ . "/passwords.inc.php";
require_once __DIR__ . "/tasks.inc.php";

if (!defined("TRACK_ACTIVITY") || TRACK_ACTIVITY)
    $_SESSION["last_activity"] = time();

if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"]) >= 300)
    logoutUser();

function loginUser($email, $password)
{
    global $config;
    if ($config["general"]["enable_account_lock_on_failed_logins"]) {
        if (apc_fetch("login_attempts_" . $email) >= 5)
            return getError("account_locked", "login_user");
    }

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
                if ($config["general"]["enable_account_lock_on_failed_logins"])
                    apc_store("login_attempts_" . $email, 0);
                $_SESSION["email"] = $email;
                $_SESSION["masterPassword"] = hash("SHA256", $password . $row['USERID']);
                $_SESSION["userid"] = $row['USERID'];
                $_SESSION["ip"] = $_SERVER["REMOTE_ADDR"];
                $_SESSION["last_activity"] = time();

                $userAgent = replaceCriticalCharacters($_SERVER['HTTP_USER_AGENT']);

                if (!$config["general"]["enable_login_history"])
                    return getSuccess(array(), "login_user");

                //Log IP Address
                $ps = $conn->prepare("INSERT INTO `iplog` (`USERID`, `IP`, `USERAGENT`, `DATE`) VALUES (?,?,?,?)");
                $ps->bind_param("sssi", $_SESSION["userid"], $_SESSION["ip"], $userAgent, time());
                $succeeded = $ps->execute();
                $ps->close();
                if ($succeeded) {
                    //Run task on login

                    runTask();
                    return getSuccess(array(), "login_user");
                } else {
                    logoutUser();
                    return getError("database_" . $ps->errno, "login_user");
                }
            }
        }
        if ($config["general"]["enable_account_lock_on_failed_logins"]) {
            $attempts = apc_fetch("login_attempts_" . $email);
            $attempts++;
            apc_store("login_attempts_" . $email, $attempts);
        }
        logoutUser();
        return getError("invalid_credentials", "login_user");
    }
    logoutUser();
    return getError("database_" . $ps->errno, "login_user");
}

function registerUser($email, $password)
{
    $salt = hash("SHA256", uniqid());
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
                return getSuccess(array(), "register_user");
            }
            return getError("database_" . $ps->errno, "register_user");
        }
        return getError("already_registered", "register_user");
    }
    return getError("database_" . $ps->errno, "register_user");
}

function getIPLog($userId)
{
    $conn = getMYSQL();
    $ps = $conn->prepare("SELECT * FROM `iplog` WHERE `USERID` = (?) ORDER BY `DATE` DESC");
    $ps->bind_param("s", $userId);
    $succeeded = $ps->execute();
    $result = $ps->get_result();
    $ps->close();
    if ($succeeded) {
        $data = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $geoIP = geoIP($row["IP"]);
                $entry = array(
                    "ip" => $row["IP"],
                    "date" => $row["DATE"],
                    "date_nice" => formatTime($row["DATE"]),
                    "user_agent" => replaceCriticalCharacters($row["USERAGENT"]),
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

function changeEmail($userId, $password, $newEmail)
{
    $conn = getMYSQL();
    $ps = $conn->prepare("SELECT PASSWORD,SALT FROM `users` WHERE `USERID` = (?)");
    $ps->bind_param("s", $userId);
    $succeeded = $ps->execute();
    $result = $ps->get_result();
    $ps->close();
    if ($succeeded) {
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $passwordHash = hash("SHA256", $password . $row["SALT"]);
            if ($passwordHash == $row['PASSWORD']) {
                $ps = $conn->prepare("SELECT * FROM `users` WHERE `EMAIL` = (?)");
                $ps->bind_param("s", $newEmail);
                $succeeded = $ps->execute();
                $result = $ps->get_result();
                $ps->close();
                if ($succeeded) {
                    if ($result->num_rows == 0) {
                        $ps = $conn->prepare("UPDATE `users` SET `EMAIL` = (?) WHERE `USERID` = (?)");
                        $ps->bind_param("ss", $newEmail, $userId);
                        $succeeded = $ps->execute();
                        $ps->close();
                        if ($succeeded) {
                            logoutUser();
                            return getSuccess(array(), "change_email");
                        }
                    } else {
                        return getError("email_in_use", "change_email");
                    }
                }
            } else {
                return getError("invalid_credentials", "change_email");
            }
        }
    }
    return getError("database_" . $ps->errno, "change_email");
}

function changePassword($userId, $oldPassword, $newPassword)
{
    $conn = getMYSQL();
    $ps = $conn->prepare("SELECT PASSWORD,SALT FROM `users` WHERE `USERID` = (?)");
    $ps->bind_param("s", $userId);
    $succeeded = $ps->execute();
    $result = $ps->get_result();
    $ps->close();
    if ($succeeded) {
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $passwordHash = hash("SHA256", $oldPassword . $row["SALT"]);
            if ($passwordHash == $row['PASSWORD']) {
                $newPasswordHash = hash("SHA256", $newPassword . $row["SALT"]);
                $ps = $conn->prepare("UPDATE `users` SET `PASSWORD` = (?) WHERE `USERID` = (?)");
                $ps->bind_param("ss", $newPasswordHash, $userId);
                $succeeded = $ps->execute();
                $ps->close();
                if ($succeeded) {
                    $ps = $conn->prepare("SELECT `ID`, `USERNAME`, `WEBSITE` FROM `passwords` WHERE `USERID` = (?) ORDER BY `DATE`");
                    $ps->bind_param("s", $userId);
                    $succeeded = $ps->execute();
                    $result = $ps->get_result();
                    $ps->close();
                    if ($succeeded) {
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $passId = $row["ID"];
                                $username = $row["USERNAME"];
                                $website = $row["WEBSITE"];
                                $password = json_decode(getPassword($userId, $passId, $_SESSION["masterPassword"]), true)["data"]["password"];
                                editPassword($userId, $passId, $password, hash("SHA256", $newPassword . $userId), $username, $website);
                            }
                            logoutUser();
                            return getSuccess(array(), "change_password");
                        }
                    }
                }
            } else {
                return getError("invalid_credentials", "change_password");
            }
        }
    }
    return getError("database_" . $ps->errno, "change_password");
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
