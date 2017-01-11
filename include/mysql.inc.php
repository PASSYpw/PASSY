<?php

function getMYSQL()
{
    mysqli_report(MYSQLI_REPORT_STRICT);
    $config = array(
        "mysql" => array(
            "host" => "localhost",
            "db" => "root",
            "user" => "root",
            "pass" => ""
        )
    );

    try {
        $conn = new mysqli($config['mysql']['host'], $config['mysql']['user'], $config['mysql']['pass'], $config['mysql']['db']);
    } catch (Exception $exception) {
        die("database_error: " . $exception->getMessage());
    }

    if ($conn->connect_error)
        die("database_error: " . $conn->connect_error);


    $conn->set_charset('utf8');

    if (!($conn->query("CREATE TABLE IF NOT EXISTS `users` (`EMAIL` VARCHAR(64) NOT NULL, `USERID` VARCHAR(18) NOT NULL, `PASSWORD` VARCHAR(512) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
        die("database_error");

    if (!($conn->query("CREATE TABLE IF NOT EXISTS `passwords` (`ID` VARCHAR(18) NOT NULL, `USERID` VARCHAR(18) NOT NULL, `USERNAME` VARCHAR(64), `PASSWORD` VARCHAR(512) NOT NULL, `WEBSITE` VARCHAR(64), `DATE` VARCHAR(12) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
        die("database_error");

    if (!($conn->query("CREATE TABLE IF NOT EXISTS `iplog` (`USERID` VARCHAR(18) NOT NULL, `IP` VARCHAR(48) NOT NULL, `DATE` VARCHAR(12) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
        die("database_error");
    return $conn;
}