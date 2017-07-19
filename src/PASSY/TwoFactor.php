<?php

namespace PASSY;

require_once __DIR__ . "/../../vendor/autoload.php";

use PragmaRX\Google2FA\Google2FA;
use Defuse\Crypto\Crypto;


class TwoFactor
{

	private $google2fa;
	/**
	 * @var Database
	 */
	private $database;

	function __construct(Database $database)
	{
		$this->google2fa = new Google2FA();
		$this->database = $database;
	}

	function generateSecretKey($displayName)
	{
		$privateKey = $this->google2fa->generateSecretKey();
		$qrUrl = $this->google2fa->getQRCodeGoogleUrl('PASSY', $displayName, $privateKey);
		return new Response(true, array(
			"privateKey" => $privateKey,
			"qrCodeUrl" => $qrUrl
		));
	}

	function enable2FA($userId, $masterPassword, $secretKey, $enteredCode)
	{
		if (!$this->google2fa->verifyKey($secretKey, $enteredCode)) {
			return new Response(false, "invalid_code");
		}
		$encryptedSecretKey = Crypto::encryptWithPassword($secretKey, $masterPassword);
		$now = time();
		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("INSERT INTO `twofactor` (`USERID`, `SECRETKEY`, `DATE`) VALUES (?, ?, ?)");
		$ps->bind_param("ssi", $userId, $encryptedSecretKey, $now);
		$succeeded = $ps->execute();
		$ps->close();
		return new Response($succeeded, ($succeeded ? array() : "database_error"));
	}

	function disable2FA($userId)
	{
		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("DELETE FROM `twofactor` WHERE `USERID` = (?)");
		$ps->bind_param("s", $userId);
		$succeeded = $ps->execute();
		$ps->close();
		return new Response($succeeded, ($succeeded ? array() : "database_error"));
	}

	function _enabled($userId) {
		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("SELECT `DATE` FROM `twofactor` WHERE `USERID` = (?)");
		$ps->bind_param("s", $userId);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded) {
			$enabled = $result->num_rows > 0;
			$date = null;
			return $enabled;
		}
		return false;
	}

	function checkCode($userId, $masterPassword, $enteredCode)
	{
		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("SELECT `SECRETKEY` FROM `twofactor` WHERE `USERID` = (?)");
		$ps->bind_param("s", $userId);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded) {
			$row = $result->fetch_assoc();
			$encryptedSecretKey = $row["SECRETKEY"];
			$secretKey = Crypto::decryptWithPassword($encryptedSecretKey, $masterPassword);

			if ($this->google2fa->verifyKey($secretKey, $enteredCode)) {
				return new Response(true, array());
			}
			return new Response(false, "invalid_code");
		}
		return new Response(false, "database_error");
	}

	function _checkPrivateKey($userId, $masterPassword, $privateKey)
	{
		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("SELECT `SECRETKEY` FROM `twofactor` WHERE `USERID` = (?)");
		$ps->bind_param("s", $userId);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded) {
			$row = $result->fetch_assoc();
			$encryptedSecretKey = $row["SECRETKEY"];
			$secretKey = Crypto::decryptWithPassword($encryptedSecretKey, $masterPassword);
			return $secretKey == $privateKey;
		}
		return false;
	}

}