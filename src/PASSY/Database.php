<?php

namespace PASSY;

require_once __DIR__ . "/../../meta.inc.php";

use Exception;
use mysqli;

/**
 * Class Database
 * @author Sefa Eyeoglu <contact@scrumplex.net>
 * @package PASSY
 */
class Database
{

	/**
	 * @var array
	 */
	private $mysqlConfig;
	/**
	 * @var mysqli
	 */
	private $mysql;

	/**
	 * Database constructor.
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 * @param $mysqlConfig
	 */
	function __construct($mysqlConfig)
	{
		PASSY::$db = $this;
		$this->mysqlConfig = $mysqlConfig;
	}

	/**
	 * Connects to mysql server with mysqli.
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 * @throws Exception connect error
	 */
	function connect()
	{
		mysqli_report(MYSQLI_REPORT_STRICT);

		$this->mysql = new mysqli($this->mysqlConfig['host'], $this->mysqlConfig['user'], $this->mysqlConfig['password'], $this->mysqlConfig['db']);
		if ($this->mysql->connect_error)
			throw new Exception($this->mysql->connect_error);

		$this->mysql->set_charset('utf8');

		if (!($this->mysql->query("CREATE TABLE IF NOT EXISTS `users` (`USERNAME` VARCHAR(20) NOT NULL, `USERID` VARCHAR(18) NOT NULL, `PASSWORD` VARCHAR(1000) NOT NULL, `SALT` VARCHAR(128) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = utf8;")))
			throw new Exception("Could not create table \"users\"");

		if (!($this->mysql->query("CREATE TABLE IF NOT EXISTS `passwords` (`ID` VARCHAR(18) NOT NULL, `USERID` VARCHAR(18) NOT NULL, `USERNAME` VARCHAR(64), `PASSWORD` VARCHAR(512) NOT NULL, `DESCRIPTION` VARCHAR(32) NOT NULL, `DATE` INT(11) NOT NULL, `ARCHIVED_DATE` INT(11)) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
			throw new Exception("Could not create table \"passwords\"");

		if (!($this->mysql->query("CREATE TABLE IF NOT EXISTS `iplog` (`USERID` VARCHAR(18) NOT NULL, `IP` VARCHAR(48) NOT NULL, `USERAGENT` VARCHAR(128) NOT NULL, `DATE` INT(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
			throw new Exception("Could not create table \"iplog\"");

		if (!($this->mysql->query("CREATE TABLE IF NOT EXISTS `metadata` (`KEY` VARCHAR(64) NOT NULL, `VALUE` VARCHAR(64)) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
			throw new Exception("Could not create table \"metadata\"");

		if (!($this->mysql->query("CREATE TABLE IF NOT EXISTS `twofactor` (`USERID` VARCHAR(18) NOT NULL, `SECRETKEY` TEXT, `DATE` INT(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
			throw new Exception("Could not create table \"metadata\"");

		if (!($this->containsMetadata("db_version"))) {
			$this->addMetadata("db_version", PASSY_BUILD);
		}
	}

	/**
	 * This method simply help to shorted mysql commands, on success an array is returned containing:
	 * true
	 * if available the results object
	 * boolean if the result row count is not null e.g 0 = false not 0 = true
	 * the prepared error
	 * and the mysql connection
	 *
	 * on a error also an array is returned with
	 * false
	 * the prepared error
	 * the mysql connection error
	 *
	 * @author Liz3
	 * @param string $statement
	 * @param string $keys
	 * @param array ...$args
	 * @return array
	 */
	function easy_exec($statement, $keys, ...$args)
	{
		$arr = array();
		array_push($arr, $keys);
		foreach ($args as $arg) {
			array_push($arr, $arg);
		}
		$tmp = array();
		foreach ($arr as $key => $value) $tmp[$key] = &$arr[$key];
		$prepared = $this->mysql->prepare($statement);
		call_user_func_array(array($prepared, 'bind_param'), $tmp);
		$success = $prepared->execute();
		if (!$success)
			return array(false, $prepared->error, $this->mysql->error);

		$arr = array(true);
		$result = $prepared->get_result();
		array_push($arr, $result);
		array_push($arr, is_object($result) ? $result->num_rows : 0);
		array_push($arr, is_object($prepared) ? $prepared->insert_id : 0);
		array_push($arr, $prepared->error);
		array_push($arr, $this->mysql->error);

		$prepared->close();
		return $arr;
	}
	/**
	 * Getter function for current mysqli instance. Connects to database if not initialized.
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 * @return mysqli current mysqli instance.
	 */
	function getInstance()
	{
		if (!isset($this->mysql))
			$this->connect();

		return $this->mysql;
	}

	function upgrade()
	{
		return; //TODO
		$this->getInstance();
		$db_version = $this->getMetadata("db_version");
		if ($db_version < PASSY_BUILD) {
			switch ($db_version) {
				case 202: // Upgrade from 2.0.2

					break;
			}
			$this->updateMetadata("db_version", PASSY_BUILD);
		}
	}

	/**
	 * @param $key
	 * @return string|bool Value. If key does not exist it returns false.
	 */
	function getMetadata($key)
	{
		$this->getInstance();
		$ps = $this->mysql->prepare("SELECT `VALUE` FROM  `metadata` WHERE `KEY` = (?)");
		$ps->bind_param("s", $key);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded && $result->num_rows > 0) {
			$row = $result->fetch_assoc();
			return $row["VALUE"];
		}
		return false;
	}

	/**
	 * Checks if database contains metadata with key.
	 * @param $key
	 * @return bool if exists or not
	 */
	function containsMetadata($key)
	{
		return $this->getMetadata($key) !== false;
	}

	/**
	 * @param $key
	 * @param $value
	 * @return boolean if succeeded or not
	 */
	function addMetadata($key, $value)
	{
		$this->getInstance();
		$ps = $this->mysql->prepare("INSERT INTO `metadata` (`KEY`, `VALUE`) VALUES (?, ?)");
		$ps->bind_param("ss", $key, $value);
		$succeeded = $ps->execute();
		$ps->close();
		return $succeeded;
	}

	/**
	 * @param $key
	 * @param $value
	 * @return boolean if succeeded or not
	 */
	function updateMetadata($key, $value)
	{
		$this->getInstance();
		$ps = $this->mysql->prepare("UPDATE `metadata` SET `VALUE` = (?) WHERE `KEY` = (?)");
		$ps->bind_param("ss", $value, $key);
		$succeeded = $ps->execute();
		$ps->close();
		return $succeeded;
	}

}