<?php
require_once __DIR__ . "/mysql.inc.php";
require_once __DIR__ . "/user.inc.php";

function addPassword($userid, $password, $masterPassword, $username, $website)
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
    $ps->bind_param("ssssss", $id, $userid, $base64, $username, $website, $date);
    $succeeded = $ps->execute();
    $ps->close();
    $conn->close();
    if ($succeeded)
        return $id;
    else
        return false;
}

function getPassword($userid, $id, $masterPassword)
{
    $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $keySize = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $conn = getMYSQL();
    $key = mb_substr(hash('SHA256', $masterPassword), 0, $keySize);
    $ps = $conn->prepare("SELECT `PASSWORD` FROM `passwords` WHERE `USERID` = (?) AND `ID` = (?)");
    $ps->bind_param("ss", $userid, $id);
    $succeeded = $ps->execute();
    $result = $ps->get_result();
    $ps->close();
    $conn->close();
    if ($succeeded) {
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $base64 = $row["PASSWORD"];
            $data = base64_decode($base64);
            $iv = substr($data, 0, $ivSize);
            $encrypted = substr($data, $ivSize, strlen($data));
            $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $encrypted, MCRYPT_MODE_CBC, $iv);
            return $decrypted;
        }
    }
    return false;
}

function getPasswordList($userid) {

}