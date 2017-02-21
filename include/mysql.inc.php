<?php

function getMYSQL()
{
    mysqli_report(MYSQLI_REPORT_STRICT);
    $config = array(
        "mysql" => array(
            "host" => "localhost",
            "db" => "passy",
            "user" => "passy",
            "pass" => "5JMdQuopENRMVRSz"
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

    if (!($conn->query("CREATE TABLE IF NOT EXISTS `users` (`EMAIL` VARCHAR(64) NOT NULL, `USERID` VARCHAR(18) NOT NULL, `PASSWORD` VARCHAR(64) NOT NULL, `SALT` VARCHAR(64) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
        die("database_error");

    if (!($conn->query("CREATE TABLE IF NOT EXISTS `passwords` (`ID` VARCHAR(18) NOT NULL, `USERID` VARCHAR(18) NOT NULL, `USERNAME` VARCHAR(64), `PASSWORD` VARCHAR(512) NOT NULL, `WEBSITE` VARCHAR(64), `DATE` INT(11) NOT NULL, `ARCHIVED` TINYINT(1), `ARCHIVED_DATE` INT(11)) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
        die("database_error");

    if (!($conn->query("CREATE TABLE IF NOT EXISTS `iplog` (`USERID` VARCHAR(18) NOT NULL, `IP` VARCHAR(48) NOT NULL, `USERAGENT` VARCHAR(128) NOT NULL, `DATE` INT(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
        die("database_error");
    return $conn;
}
