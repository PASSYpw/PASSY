<?php

namespace PASSY;

require_once __DIR__ . '/../../vendor/autoload.php';

use Defuse\Crypto\Crypto;
use League\Csv\Reader;

/**
 * Class Passwords
 * @author Sefa Eyeoglu <contact@scrumplex.net>
 * @author Liz3(Yann HN) <info@liz3.de>
 * @package PASSY
 */
class Passwords
{

	/**
	 * Passwords constructor.
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 */
	function __construct()
	{
		PASSY::$passwords = $this;
	}

	/**
	 * Creates a password entry in the database with given parameters.
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 * @param $username
	 * @param $password
	 * @param $description
	 * @param $userId
	 * @param $masterPassword
	 * @return Response
	 */
	function _create($username, $password, $description, $userId, $masterPassword)
	{
		$passwordId = uniqid("pass_");
		$encryptedPassword = Crypto::encryptWithPassword($password, $masterPassword);
		$date = time();
		$archivedDate = null;

		$mysql = PASSY::$db->getInstance();
		$ps = $mysql->prepare("INSERT INTO `passwords` (`ID`, `USERID`, `USERNAME`, `PASSWORD`, `DESCRIPTION`, `DATE`, `ARCHIVED_DATE`) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$ps->bind_param("sssssii", $passwordId, $userId, $username, $encryptedPassword, $description, $date, $archivedDate);
		$succeeded = $ps->execute();
		$ps->close();
		$response = new Response($succeeded, $succeeded ? $passwordId : "database_error");
		return $response;
	}

	/**
	 * Updates given password's data in the database.
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 * @param $passwordId
	 * @param $username
	 * @param $password
	 * @param $description
	 * @param $masterPassword
	 * @return Response
	 */
	function _edit($passwordId, $username, $password, $description, $masterPassword)
	{
		$encryptedPassword = Crypto::encryptWithPassword($password, $masterPassword);

		$mysql = PASSY::$db->getInstance();
		$ps = $mysql->prepare("UPDATE `passwords` SET `PASSWORD` = (?), `USERNAME` = (?), `DESCRIPTION` = (?) WHERE `ID` = (?)");
		$ps->bind_param("ssss", $encryptedPassword, $username, $description, $passwordId);
		$succeeded = $ps->execute();
		$ps->close();
		$response = new Response($succeeded, $succeeded ? "" : "database_error");
		return $response;
	}

	/**
	 * Imports passwords, which will be encrypted with $masterPassword and assigned to $userId.
	 * @author Liz3(Yann HN) <info@liz3.de>
	 * @param string $data
	 * @param string $userId
	 * @param string $masterPassword
	 * @param string $type
	 * @return Response|null
	 */
	function _import($data, $userId, $masterPassword, $type = "passy")
	{
		$count = 0;
		$failed = 0;
		if ($type == "passy") {
			$arr = json_decode($data, true);
			$data = $arr["data"];
			foreach ($data as $item) {

				if ($item["pass"] == null) {
					$failed++;
					continue;
				}
				$pass = $item["pass"];
				$this->_create($item["username"], $pass, $item["description"], $userId, $masterPassword);
				$count++;
			}
		} else if ($type == "CSV") {

			$csv = Reader::createFromString($data);
			foreach ($csv->getIterator() as $item) {

				if (count($item) < 4) {
					$failed++;
				}
				$username = $item[2];
				$password = $item[3];
				$description = $item[0];
				$this->_create($username, $password, $description, $userId, $masterPassword);
				$count++;
			}
		} else {
			return null;
		}
		return new Response(true, array(
			"imported" => $count,
			"failed" => $failed
		));
	}

	/**
	 * Export all passwords from $userId, which will be decrypted with $masterPassword.
	 * @author Liz3(Yann HN) <info@liz3.de>
	 * @param string $userId
	 * @param string $masterPassword
	 * @return Response
	 */
	function _exportAll($userId, $masterPassword)
	{
		$data = array();
		$mysql = PASSY::$db->getInstance();
		$query = "SELECT `USERNAME`, `PASSWORD`, `DESCRIPTION`, `DATE`, `ARCHIVED_DATE` FROM `passwords` WHERE `USERID` = (?)";
		$ps = $mysql->prepare($query);
		$ps->bind_param("s", $userId);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded) {
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$username = $row["USERNAME"];
					$description = $row["DESCRIPTION"];
					$date = $row["DATE"];
					$archived = false;
					$archived_date = "";
					if ($row["ARCHIVED_DATE"] != null) {
						$archived = true;
						$archived_date = $row["ARCHIVED_DATE"];
					}
					$decryptedPassword = Crypto::decryptWithPassword($row['PASSWORD'], $masterPassword);

					if ($archived) {
						array_push($data, array(
							"username" => $username,
							"description" => $description,
							"date" => $date,
							"archived" => $archived,
							"date_archived" => $archived_date,
							"pass" => $decryptedPassword

						));
					} else {
						array_push($data, array(
							"username" => $username,
							"description" => $description,
							"date" => $date,
							"archived" => $archived,
							"pass" => $decryptedPassword

						));
					}
				}
			}
			return new Response(true, $data);
		}
		return new Response(false, "database_error");
	}

	/**
	 * Queries info about given password
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 * @param $passwordId
	 * @param mixed $masterPassword used to decrypt passwords. If null it won't decrypt.
	 * @return Response
	 */
	function _query($passwordId, $masterPassword = null)
	{

		$mysql = PASSY::$db->getInstance();
		$query = "SELECT `ID`, `USERID`, `USERNAME`, `DESCRIPTION`, `DATE`, `ARCHIVED_DATE` FROM `passwords` WHERE `ID` = (?)";
		if (isset($masterPassword))
			$query = "SELECT `ID`, `USERID`, `USERNAME`, `PASSWORD`, `DESCRIPTION`, `DATE`, `ARCHIVED_DATE` FROM `passwords` WHERE `ID` = (?)";
		$ps = $mysql->prepare($query);
		$ps->bind_param("s", $passwordId);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded && $result->num_rows > 0) {
			$row = $result->fetch_assoc();

			$username = $row["USERNAME"];
			$description = $row["DESCRIPTION"];
			if (strlen($username) == 0)
				$username = null;

			if (strlen($description) == 0)
				$description = null;

			$entry = array(
				"password" => null,
				"password_safe" => null,
				"password_id" => $row["ID"],
				"username" => $username,
				"username_safe" => Util::filterStrings($username),
				"description" => $description,
				"description_safe" => Util::filterStrings($description),
				"date_added" => $row["DATE"],
				"date_added_readable" => Format::formatTime($row["DATE"]),
				"user_id" => $row["USERID"],
				"archived" => $row["ARCHIVED_DATE"] !== null,
				"date_archived" => $row["ARCHIVED_DATE"],
				"date_archived_readable" => Format::formatTime($row["ARCHIVED_DATE"])
			);

			if (isset($masterPassword)) {
				$decryptedPassword = Crypto::decryptWithPassword($row['PASSWORD'], $masterPassword);
				$entry["password"] = $decryptedPassword;
				$entry["password_safe"] = Util::filterStrings($decryptedPassword);
			}
			return new Response(true, $entry);
		}
		return new Response(false, "database_error");
	}

	/**
	 * Queries all passwords from user.
	 * @param $userId
	 * @param mixed $masterPassword used to decrypt passwords. If null it won't decrypt.
	 * @return Response
	 */
	function _queryAll($userId, $masterPassword = null)
	{

		$mysql = PASSY::$db->getInstance();
		$query = "SELECT `ID`, `USERID`, `USERNAME`, `DESCRIPTION`, `DATE`, `ARCHIVED_DATE` FROM `passwords` WHERE `USERID` = (?)";
		if (isset($masterPassword))
			$query = "SELECT `ID`, `USERID`, `USERNAME`, `PASSWORD`, `DESCRIPTION`, `DATE`, `ARCHIVED_DATE` FROM `passwords` WHERE `USERID` = (?)";
		$ps = $mysql->prepare($query);
		$ps->bind_param("s", $userId);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded) {
			$data = array();
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$username = $row["USERNAME"];
					$description = $row["DESCRIPTION"];
					if (strlen($username) == 0)
						$username = null;

					if (strlen($description) == 0)
						$description = null;

					$entry = array(
						"password" => null,
						"password_safe" => null,
						"password_id" => $row["ID"],
						"username" => $username,
						"username_safe" => Util::filterStrings($username),
						"description" => $description,
						"description_safe" => Util::filterStrings($description),
						"date_added" => $row["DATE"],
						"date_added_readable" => Format::formatTime($row["DATE"]),
						"user_id" => $row["USERID"],
						"archived" => $row["ARCHIVED_DATE"] !== null,
						"date_archived" => $row["ARCHIVED_DATE"],
						"date_archived_readable" => Format::formatTime($row["ARCHIVED_DATE"])
					);

					if (isset($masterPassword)) {
						$decryptedPassword = Crypto::decryptWithPassword($row['PASSWORD'], $masterPassword);
						$entry["password"] = $decryptedPassword;
						$entry["password_safe"] = Util::filterStrings($decryptedPassword);
					}
					array_push($data, $entry);
				}
			}
			return new Response(true, $data);
		}
		return new Response(false, "database_error");
	}

	/**
	 * Undocumented
	 * @param $userId
	 * @param $oldMasterPassword
	 * @param $newMasterPassword
	 * @return Response
	 */
	function _reencryptPasswords($userId, $oldMasterPassword, $newMasterPassword)
	{
		$mysql = PASSY::$db->getInstance();
		$ps = $mysql->prepare("SELECT `ID`, `PASSWORD` FROM `passwords` WHERE `USERID` = (?)");
		$ps->bind_param("s", $userId);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded) {
			if ($result->num_rows > 0) {
				$ps = $mysql->prepare("UPDATE `passwords` SET `PASSWORD` = (?) WHERE `ID` = (?)");
				while ($row = $result->fetch_assoc()) {
					$passwordId = $row["ID"];
					$decryptedPassword = Crypto::decryptWithPassword($row['PASSWORD'], $oldMasterPassword);
					$reencryptedPassword = Crypto::encryptWithPassword($decryptedPassword, $newMasterPassword);
					$ps->bind_param("ss", $reencryptedPassword, $passwordId);
					$succeeded = $ps->execute();
					if (!$succeeded)
						return new Response(false, "database_error");
				}
				$ps->close();
			}
			return new Response(true, null);
		}
		return new Response(false, "database_error");
	}

	/**
	 * Adds the flag archived to a password
	 * @param $passwordId
	 * @return Response
	 */
	function _archive($passwordId)
	{
		$archivedDate = time();

		$mysql = PASSY::$db->getInstance();
		$ps = $mysql->prepare("UPDATE `passwords` SET `ARCHIVED_DATE` = (?) WHERE `ID` = (?)");
		$ps->bind_param("is", $archivedDate, $passwordId);
		$succeeded = $ps->execute();
		$ps->close();
		if ($succeeded)
			return new Response(true, null);
		return new Response(false, "database_error");
	}

	/**
	 * Removes the flag archived to a password
	 * @param $passwordId
	 * @return Response
	 */
	function _restore($passwordId)
	{
		$archivedDate = null;

		$mysql = PASSY::$db->getInstance();
		$ps = $mysql->prepare("UPDATE `passwords` SET `ARCHIVED_DATE` = (?) WHERE `ID` = (?)");
		$ps->bind_param("is", $archivedDate, $passwordId);
		$succeeded = $ps->execute();
		$ps->close();
		if ($succeeded)
			return new Response(true, null);
		return new Response(false, "database_error");
	}

	/**
	 * Permanently deletes password
	 * @param $passwordId
	 * @return Response
	 */
	function _delete($passwordId)
	{

		$mysql = PASSY::$db->getInstance();
		$ps = $mysql->prepare("DELETE FROM `passwords` WHERE `ID` = (?)");
		$ps->bind_param("s", $passwordId);
		$succeeded = $ps->execute();
		$ps->close();
		if ($succeeded)
			return new Response(true, null);
		return new Response(false, "database_error");
	}


}