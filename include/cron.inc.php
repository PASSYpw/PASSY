<?php
require_once __DIR__ . "/mysql.inc.php";

$conn = getMYSQL();

//Clear ip log

$now = time();
$twoWeeksAgo = $now - (60 * 60 * 24 * 14); // 60s * 60min * 24h * 14d

$ps = $conn->prepare("DELETE FROM `iplog` WHERE `DATE` < (?)");
$ps->bind_param("i", $twoWeeksAgo);
$ps->execute();

$ps = $conn->prepare("DELETE FROM `passwords` WHERE `ARCHIVED` = 1 AND `ARCHIVED_DATE` < (?)");
$ps->bind_param("i", $twoWeeksAgo);
$ps->execute();
