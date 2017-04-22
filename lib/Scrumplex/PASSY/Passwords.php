<?php

namespace Scrumplex\PASSY;

use Defuse\Crypto\Crypto;

require_once __DIR__ . '/../../../vendor/autoload.php';

class Passwords
{
	private $database;

	/**
	 * Passwords constructor.
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 * @param Database $database
	 */
	function __construct(Database $database)
	{
		$this->database = $database;
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
	function create($username, $password, $description, $userId, $masterPassword)
	{
		$passwordId = uniqid("pass_");
		$encryptedPassword = Crypto::encryptWithPassword($password, $masterPassword);
		$date = time();
		$archivedDate = null;

		$mysql = $this->database->getInstance();
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
	function edit($passwordId, $username, $password, $description, $masterPassword)
	{
		$encryptedPassword = Crypto::encryptWithPassword($password, $masterPassword);

		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("UPDATE `passwords` SET `PASSWORD` = (?), `USERNAME` = (?), `DESCRIPTION` = (?) WHERE `ID` = (?)");
		$ps->bind_param("ssss", $encryptedPassword, $username, $description, $passwordId);
		$succeeded = $ps->execute();
		$ps->close();
		$response = new Response($succeeded, $succeeded ? "" : "database_error");
		return $response;
	}

	/**
	 * Queries info about given password
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 * @param $passwordId
	 * @param mixed $masterPassword used to decrypt passwords. If false or null it won't decrypt.
	 * @return Response
	 */
	function query($passwordId, $masterPassword = false)
	{

		$mysql = $this->database->getInstance();
		$query = "SELECT `ID`, `USERID`, `USERNAME`, `DESCRIPTION`, `DATE`, `ARCHIVED_DATE` FROM `passwords` WHERE `ID` = (?)";
		if ($masterPassword !== null && $masterPassword !== false)
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
				"password_id" => $row["ID"],
				"username" => $username,
				"description" => $description,
				"date_added" => $row["DATE"],
				"date_added_readable" => Format::formatTime($row["DATE"]),
				"user_id" => $row["USERID"],
				"archived" => $row["ARCHIVED_DATE"] !== null,
				"date_archived" => $row["ARCHIVED_DATE"],
				"date_archived_readable" => Format::formatTime($row["ARCHIVED_DATE"])
			);

			if ($masterPassword !== null && $masterPassword !== false) {
				$decryptedPassword = Crypto::decryptWithPassword($row['PASSWORD'], $masterPassword);
				$entry["password"] = $decryptedPassword;
			}
			return new Response(true, $entry);
		}
		return new Response(false, "database_error");
	}

	function queryAll($userId, $masterPassword = null)
	{

		$mysql = $this->database->getInstance();
		$query = "SELECT `ID`, `USERID`, `USERNAME`, `DESCRIPTION`, `DATE`, `ARCHIVED_DATE` FROM `passwords` WHERE `USERID` = (?)";
		if ($masterPassword !== false)
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
						"password_id" => $row["ID"],
						"username" => $username,
						"description" => $description,
						"date_added" => $row["DATE"],
						"date_added_readable" => Format::formatTime($row["DATE"]),
						"user_id" => $row["USERID"],
						"archived" => $row["ARCHIVED_DATE"] !== null,
						"date_archived" => $row["ARCHIVED_DATE"],
						"date_archived_readable" => Format::formatTime($row["ARCHIVED_DATE"])
					);

					if ($masterPassword !== false) {
						$decryptedPassword = Crypto::decryptWithPassword($row['PASSWORD'], $masterPassword);
						$entry["password"] = $decryptedPassword;
					}
					array_push($data, $entry);
				}
			}
			return new Response(true, $data);
		}
		return new Response(false, "database_error");
	}

	function reencryptPasswords($userId, $oldMasterPassword, $newMasterPassword)
	{
		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("SELECT `ID`, `PASSWORD` FROM `passwords` WHERE `USERID` = (?)");
		$ps->bind_param("s", $userId);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded) {
			if ($result->num_rows > 0) {
				$ps->prepare("UPDATE `passwords` SET `PASSWORD` = (?) WHERE `ID` = (?)");
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

	function archive($passwordId)
	{
		$archivedDate = time();

		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("UPDATE `passwords` SET `ARCHIVED_DATE` = (?) WHERE `ID` = (?)");
		$ps->bind_param("is", $archivedDate, $passwordId);
		$succeeded = $ps->execute();
		$ps->close();
		if ($succeeded)
			return new Response(true, null);
		return new Response(false, "database_error");
	}

	function restore($passwordId)
	{
		$archivedDate = null;

		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("UPDATE `passwords` SET `ARCHIVED_DATE` = (?) WHERE `ID` = (?)");
		$ps->bind_param("is", $archivedDate, $passwordId);
		$succeeded = $ps->execute();
		$ps->close();
		if ($succeeded)
			return new Response(true, null);
		return new Response(false, "database_error");
	}

	function delete($passwordId)
	{

		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("DELETE FROM `passwords` WHERE `ID` = (?)");
		$ps->bind_param("s", $passwordId);
		$succeeded = $ps->execute();
		$ps->close();
		if ($succeeded)
			return new Response(true, null);
		return new Response(false, "database_error");
	}
}