<?php

namespace Scrumplex\PASSY;

use Exception;
use mysqli;

/**
 * Class Database
 * @author Sefa Eyeoglu <contact@scrumplex.net>
 * @package Scrumplex\PASSY
 */
class Database
{

	private $mysqlConfig;
	private $mysql;

	/**
	 * Database constructor.
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 * @param $mysqlConfig
	 */
	function __construct($mysqlConfig)
	{
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

		if (!($this->mysql->query("CREATE TABLE IF NOT EXISTS `users` (`USERNAME` VARCHAR(20) NOT NULL, `USERID` VARCHAR(18) NOT NULL, `PASSWORD` VARCHAR(128) NOT NULL, `SALT` VARCHAR(128) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = utf8;")))
			throw new Exception("Could not create table \"users\"");

		if (!($this->mysql->query("CREATE TABLE IF NOT EXISTS `passwords` (`ID` VARCHAR(18) NOT NULL, `USERID` VARCHAR(18) NOT NULL, `USERNAME` VARCHAR(64), `PASSWORD` VARCHAR(512) NOT NULL, `DESCRIPTION` VARCHAR(32) NOT NULL, `DATE` INT(11) NOT NULL, `ARCHIVED_DATE` INT(11)) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
			throw new Exception("Could not create table \"passwords\"");

		if (!($this->mysql->query("CREATE TABLE IF NOT EXISTS `iplog` (`USERID` VARCHAR(18) NOT NULL, `IP` VARCHAR(48) NOT NULL, `USERAGENT` VARCHAR(128) NOT NULL, `DATE` INT(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
			throw new Exception("Could not create table \"iplog\"");
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

}