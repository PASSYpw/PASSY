<?php
require_once __DIR__ . "/mysql.inc.php";
require_once __DIR__ . "/user.inc.php";
require_once __DIR__ . "/format.inc.php";

function addPassword($userId, $password, $masterPassword, $username, $website)
{
    $keySize = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($ivSize, MCRYPT_DEV_URANDOM);
    $key = mb_substr(hash('SHA256', $masterPassword), 0, $keySize);
    $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $password, MCRYPT_MODE_CBC, $iv);
    $base64 = base64_encode($iv . $encrypted);
    $id = uniqid("pass_");
    $date = time();
    $conn = getMYSQL();
    $ps = $conn->prepare("INSERT INTO `passwords` (`ID`, `USERID`, `PASSWORD`, `USERNAME`, `WEBSITE`, `DATE`) VALUES (?, ?, ?, ?, ?, ?)");
    $ps->bind_param("ssssss", $id, $userId, $base64, $username, $website, $date);
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
            $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $encrypted, MCRYPT_MODE_CBC, $iv);
            return getSuccess(array("password" => $decrypted), "get_password");
        }
    }
    return getError("database_" . $ps->errno, "get_password");
}

function deletePassword($userId, $id) {
    $conn = getMYSQL();
    $ps = $conn->prepare("DELETE FROM `passwords` WHERE `USERID` = (?) AND `ID` = (?)");
    $ps->bind_param("ss", $userId, $id);
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
    $ps = $conn->prepare("SELECT `ID`,`USERNAME`,`WEBSITE`,`DATE` FROM `passwords` WHERE `USERID` = (?)");
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

                $tableRow = array(
                    "password_id" => $row["ID"],
                    "username" => $username,
                    "website" => $website,
                    "date_added" => $row["DATE"],
                    "date_added_nice" => formatTime($row["DATE"]),
                    "user_id" => $userId
                );
                array_push($data, $tableRow);
            }
        }
        $json = getSuccess($data, "get_password_list");
    } else {
        $json = getError("database_" . $ps->errno, "get_password_list");
    }
    return $json;
}