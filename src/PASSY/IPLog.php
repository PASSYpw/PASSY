<?php

namespace PASSY;

/**
 * Class IPLog
 * @author Sefa Eyeoglu <contact@scrumplex.net>
 * @package Scrumplex\PASSY
 */
class IPLog
{

	/**
	 * @var Database
	 */
	private $database;

	/**
	 * IPLog constructor.
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 * @param Database $database
	 */
	function __construct(Database $database)
	{
		$this->database = $database;
	}

	/**
	 * Logs ip with given parameters in the database.
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 * @param $ip
	 * @param $userAgent
	 * @param $userId
	 * @return Response
	 * @see \Scrumplex\PASSY\Response
	 */
	function logIP($ip, $userAgent, $userId)
	{
		$now = time();

		$mysql = $this->database->getInstance();

		$ps = $mysql->prepare("INSERT INTO `iplog` (`USERID`, `IP`, `USERAGENT`, `DATE`) VALUES (?, ?, ?, ?)");
		$ps->bind_param("ssss", $userId, $ip, $userAgent, $now);
		$succeeded = $ps->execute();
		$ps->close();
		if ($succeeded)
			return new Response(true, null);
		return new Response(false, "database_error");
	}

	/**
	 * Queries all entries for iplog for specific user
	 * @author Sefa Eyeoglu <contact@scrumplex.net>
	 * @param $userId
	 * @return Response
	 * @see \Scrumplex\PASSY\Response
	 */
	function queryAll($userId)
	{
		$mysql = $this->database->getInstance();

		$ps = $mysql->prepare("SELECT * FROM `iplog` WHERE `USERID` = (?) ORDER BY `DATE` DESC");
		$ps->bind_param("s", $userId);
		$succeeded = $ps->execute();
		$result = $ps->get_result();
		$ps->close();
		if ($succeeded) {
			$data = array();
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$entry = array(
						"ip" => $row["IP"],
						"user_agent" => $row["USERAGENT"],
						"date" => $row["DATE"],
						"date_readable" => Format::formatTime($row["DATE"])
					);
					array_push($data, $entry);
				}
			}
			return new Response(true, $data);
		}
		return new Response(false, "database_error");
	}

}