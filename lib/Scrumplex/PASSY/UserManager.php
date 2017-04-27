<?php

namespace Scrumplex\PASSY;


class UserManager
{
	private $database;
	private $passwords;

	/**
	 * UserManager constructor.
	 * @param Database $database
	 * @param Passwords $passwords
	 */
	function __construct(Database $database, Passwords $passwords)
	{
		$this->database = $database;
		$this->passwords = $passwords;
		session_start();
	}

	/**
	 * Tracks user activity. Used for logging user out after 300s.
	 */
	function trackActivity()
	{
		$_SESSION["last_activity"] = time();
	}

	/**
	 * If user is inactive too long he will be logged out
	 */
	function checkSessionExpiration()
	{
		if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"]) >= 300) {
			$this->logout();
		}
	}

	/**
	 * Checks user credentials. If credentials are correct it will create a valid session.
	 * @param $username string
	 * @param $password string
	 * @return Response
	 */
	function login($username, $password)
	{
		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("SELECT * FROM `users` WHERE `USERNAME` = (?)");
		$ps->bind_param("s", $username);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded) {
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$hashedPassword = hash("SHA512", $password . $row["SALT"]);
				if ($hashedPassword == $row['PASSWORD']) {
					$_SESSION["username"] = $username;
					$_SESSION["master_password"] = $password;
					$_SESSION["userId"] = $row['USERID'];
					$_SESSION["ip"] = $_SERVER["REMOTE_ADDR"];
					$this->trackActivity();
					return new Response(true, null);
				}
			}
			return new Response(false, "invalid_credentials");
		}
		return new Response(false, "database_error");
	}

	/**
	 * @param $username string
	 * @param $password string
	 * @return Response
	 */
	function register($username, $password)
	{
		$userId = uniqid("user_");
		$salt = hash("SHA512", uniqid());
		$hashedPassword = hash("SHA512", $password . $salt);

		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("SELECT * FROM `users` WHERE `USERNAME` = (?)");
		$ps->bind_param("s", $username);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded) {
			if ($result->num_rows == 0) {
				$ps = $mysql->prepare("INSERT INTO `users` (`USERNAME`, `USERID`, `PASSWORD`, `SALT`) VALUES (?, ?, ?, ?)");
				$ps->bind_param("ssss", $username, $userId, $hashedPassword, $salt);
				$succeeded = $ps->execute();
				$ps->close();
				if ($succeeded)
					return new Response(true, null);
				return new Response(false, "database_error");
			}
			return new Response(false, "username_exists");
		}
		return new Response(false, "database_error");
	}

	function changeUsername($userId, $newUsername)
	{
		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("SELECT `USERID` FROM `users` WHERE `USERNAME` = (?)");
		$ps->bind_param("s", $newUsername);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded) {
			if ($result->num_rows == 0) {
				$ps = $mysql->prepare("UPDATE `users` SET `USERNAME` = (?) WHERE `USERID` = (?)");
				$ps->bind_param("ss", $newUsername, $userId);
				$succeeded = $ps->execute();
				$ps->close();
				if ($succeeded)
					return new Response(true, null);
				return new Response(false, "database_error");
			}
			return new Response(false, "username_exists");
		}
		return new Response(false, "database_error");
	}

	function changePassword($userId, $masterPassword, $newPassword)
	{
		return new Response(false, "not_implemented");
		//TODO: Not working.

		$mysql = $this->database->getInstance();
		$ps = $mysql->prepare("SELECT `SALT` FROM `users` WHERE `USERID` = (?)");
		$ps->bind_param("s", $userId);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded && $result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$salt = $row["SALT"];
			$hashedPassword = hash("SHA512", $newPassword . $salt);

			$ps = $mysql->prepare("UPDATE `users` SET `PASSWORD` = (?) WHERE `USERID` = (?)");
			$ps->bind_param("ss", $hashedPassword, $userId);
			$succeeded = $ps->execute();
			if ($succeeded)
				return $this->passwords->reencryptPasswords($userId, $masterPassword, $newPassword);
			return new Response(true, null);
		}
		return new Response(false, "database_error");
	}

	function logout()
	{
		if (session_status() != PHP_SESSION_NONE)
			session_destroy();

		return new Response(true, null);
	}

	function getUserID()
	{
		if ($this->isAuthenticated())
			return $_SESSION["userId"];
		return null;
	}

	function getLastActivity()
	{
		if ($this->isAuthenticated())
			return $_SESSION["last_activity"];
		return null;
	}

	function getMasterPassword()
	{
		if ($this->isAuthenticated())
			return $_SESSION["master_password"];
		return null;
	}

	function isAuthenticated()
	{
		if (session_status() == PHP_SESSION_NONE)
			return false;
		if (!isset($_SESSION["username"]) || !isset($_SESSION["master_password"]) || !isset($_SESSION["userId"]) || !isset($_SESSION["ip"]) || !isset($_SESSION["last_activity"]))
			return false;
		if ($_SESSION["ip"] != $_SERVER["REMOTE_ADDR"])
			return false;
		return true;
	}

}