<?php

namespace PASSY;

/**
 * Class Tasks
 * @author Sefa Eyeoglu <contact@scrumplex.net>
 * @package PASSY
 */
class Tasks
{

    function __construct()
    {
        PASSY::$tasks = $this;
    }

    function run()
    {
        $this->clearArchivedPasswords();
        $this->clearLoginHistory();
    }

    private function clearArchivedPasswords()
    {
        $maxAge = 2 * 7 * 24 * 60 * 60; // two weeks in seconds
        $deleteOlderThan = time() - $maxAge; // Now - 2 weeks

        $mysql = PASSY::$db->getInstance();

        $ps = $mysql->prepare("DELETE FROM `passwords` WHERE ARCHIVED_DATE <= (?)");
        $ps->bind_param("i", $deleteOlderThan);
        return $ps->execute();
    }

    private function clearLoginHistory()
    {
        $maxAge = 2 * 7 * 24 * 60 * 60; // two weeks in seconds
        $deleteOlderThan = time() - $maxAge; // Now - 2 weeks

        $mysql = PASSY::$db->getInstance();

        $ps = $mysql->prepare("DELETE FROM `iplog` WHERE iplog.DATE <= (?)");
        $ps->bind_param("i", $deleteOlderThan);
        return $ps->execute();
    }


}