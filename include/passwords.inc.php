<?php
require_once __DIR__ . "/mysql.inc.php";
require_once __DIR__ . "/user.inc.php";
require_once __DIR__ . "/format.inc.php";

function addPassword($userId, $password, $masterPassword, $username, $website)
{
    $password = replaceCriticalCharacters($password);
    $username = replaceCriticalCharacters($username);
    $website = replaceCriticalCharacters($website);

    $keySize = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($ivSize, MCRYPT_DEV_URANDOM);
    $key = mb_substr(hash('SHA256', $masterPassword), 0, $keySize);
    $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $password, MCRYPT_MODE_CBC, $iv);
    $base64 = base64_encode($iv . $encrypted);
    $id = uniqid("pass_");
    $date = time();
    $archivedDate = 0;
    $archived = 0;
    $conn = getMYSQL();
    $ps = $conn->prepare("INSERT INTO `passwords` (`ID`, `USERID`, `PASSWORD`, `USERNAME`, `WEBSITE`, `DATE`, `ARCHIVED`, `ARCHIVED_DATE`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $ps->bind_param("ssssssis", $id, $userId, $base64, $username, $website, $date, $archived, $archivedDate);
    $succeeded = $ps->execute();
    $ps->close();
    if ($succeeded)
        return getSuccess(array("password_id" => $id), "add_password");

    return getError("database_" . $ps->errno, "add_password");
}

function getPassword($userId, $id, $masterPassword)
{
    $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $keySize = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $conn = getMYSQL();
    $key = mb_substr(hash('SHA256', $masterPassword), 0, $keySize);
    $ps = $conn->prepare("SELECT `PASSWORD` FROM `passwords` WHERE `USERID` = (?) AND `ID` = (?)");
    $ps->bind_param("ss", $userId, $id);
    $succeeded = $ps->execute();
    $result = $ps->get_result();
    $ps->close();
    if ($succeeded) {
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $base64 = $row["PASSWORD"];
            $data = base64_decode($base64);
            $iv = substr($data, 0, $ivSize);
            $encrypted = substr($data, $ivSize, strlen($data));
            $decrypted = htmlspecialchars(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $encrypted, MCRYPT_MODE_CBC, $iv));
            return getSuccess(array("password" => $decrypted), "get_password");
        }
    }
    return getError("database_" . $ps->errno, "get_password");
}

function archivePassword($userId, $id)
{
    $conn = getMYSQL();
    $ps = $conn->prepare("SELECT `ARCHIVED` FROM `passwords` WHERE `USERID` = (?) AND `ID` = (?)");
    $ps->bind_param("ss", $userId, $id);
    $succeeded = $ps->execute();
    $result = $ps->get_result();
    $ps->close();
    if ($succeeded) {
        if ($result->num_rows == 1) {
            if ($result->fetch_assoc()["ARCHIVED"] == 1)
                return getSuccess(null, "archive_password");
            $archivedDate = time();
            $archived = 1;
            $ps = $conn->prepare("UPDATE `passwords` SET `ARCHIVED` = (?), `ARCHIVED_DATE` = (?) WHERE `USERID` = (?) AND `ID` = (?)");
            $ps->bind_param("isss", $archived, $archivedDate, $userId, $id);
            $succeeded = $ps->execute();
            $ps->close();
            if ($succeeded) {
                return getSuccess(null, "archive_password");
            }
            return getError("database_" . $ps->errno, "archive_password");
        }
    }
    return getError("database_" . $ps->errno, "archive_password");
}

function restorePassword($userId, $id)
{
    $conn = getMYSQL();
    $ps = $conn->prepare("SELECT `ARCHIVED` FROM `passwords` WHERE `USERID` = (?) AND `ID` = (?)");
    $ps->bind_param("ss", $userId, $id);
    $succeeded = $ps->execute();
    $result = $ps->get_result();
    $ps->close();
    if ($succeeded) {
        if ($result->num_rows == 1) {
            if ($result->fetch_assoc()["ARCHIVED"] == 0)
                return getSuccess(null, "restore_password");

            $archived = 0;
            $ps = $conn->prepare("UPDATE `passwords` SET `ARCHIVED` = (?) WHERE `USERID` = (?) AND `ID` = (?)");
            $ps->bind_param("iss", $archived, $userId, $id);
            $succeeded = $ps->execute();
            $ps->close();
            if ($succeeded) {
                return getSuccess(null, "restore_password");
            }
            return getError("database_" . $ps->errno, "restore_password");
        }
    }
    return getError("database_" . $ps->errno, "restore_password");
}

function deletePassword($userId, $id)
{
    $conn = getMYSQL();
    $archived = 1;
    $ps = $conn->prepare("DELETE FROM `passwords` WHERE `USERID` = (?) AND `ID` = (?) AND `ARCHIVED` = (?)");
    $ps->bind_param("ssi", $userId, $id, $archived);
    $succeeded = $ps->execute();
    $ps->close();
    if ($succeeded) {
        return getSuccess(null, "delete_password");
    }
    return getError("database_" . $ps->errno, "delete_password");
}

function getPasswordList($userId)
{
    $conn = getMYSQL();
    $ps = $conn->prepare("SELECT `ID`, `USERNAME`, `WEBSITE`, `DATE`, `ARCHIVED`, `ARCHIVED_DATE` FROM `passwords` WHERE `USERID` = (?) ORDER BY `DATE`");
    $ps->bind_param("s", $userId);
    $succeeded = $ps->execute();
    $result = $ps->get_result();
    $ps->close();
    if ($succeeded) {
        $data = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $username = $row["USERNAME"];
                if ($username != null && strlen($username) == 0)
                    $username = null;

                $website = $row["WEBSITE"];
                if ($website != null && strlen($website) <= 8) {
                    $website = null;
                }

                $entry = array(
                    "password_id" => $row["ID"],
                    "username" => replaceCriticalCharacters($username),
                    "website" => replaceCriticalCharacters($website),
                    "date_added" => $row["DATE"],
                    "date_added_nice" => formatTime($row["DATE"]),
                    "user_id" => $userId,
                    "archived" => (bool) $row["ARCHIVED"],
                    "date_archived" => $row["ARCHIVED_DATE"],
                    "date_archived_nice" => formatTime($row["ARCHIVED_DATE"])
                );
                array_push($data, $entry);
            }
        }
        return getSuccess($data, "get_password_list");
    }
    return getError("database_" . $ps->errno, "get_password_list");
}
