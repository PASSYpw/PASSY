<?php

function getMYSQL()
{
    $config = array(
        "mysql" => array(
            "host" => "localhost",
            "db" => "root",
            "user" => "root",
            "pass" => ""
        )
    );

    $conn = new mysqli($config['mysql']['host'], $config['mysql']['user'], $config['mysql']['pass'], $config['mysql']['db']);

    if ($conn->connect_error)
        die("database_connection_error: " . $conn->connect_error);


    $conn->set_charset('utf8');

    if (!($conn->query("CREATE TABLE IF NOT EXISTS `users` (`EMAIL` VARCHAR(64) NOT NULL, `PASSWORD` VARCHAR(512) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
        die("database_error");

    if (!($conn->query("CREATE TABLE IF NOT EXISTS `passwords` (`ID` VARCHAR(18) NOT NULL, `EMAIL` VARCHAR(64) NOT NULL, `PASSWORD` VARCHAR(512) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
        die("database_error");

    if (!($conn->query("CREATE TABLE IF NOT EXISTS `iplog` (`EMAIL` VARCHAR(64) NOT NULL, `IP` VARCHAR(48) NOT NULL, `DATE` VARCHAR(12) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;")))
        die("database_error");
    return $conn;
}