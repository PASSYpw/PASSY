<?php

namespace PASSY;

/**
 * Class Format
 * @author Sefa Eyeoglu <contact@scrumplex.net>
 * @package PASSY
 */
class Format
{

    /**
     * Returns human readable time formats.
     * @author Sefa Eyeoglu <contact@scrumplex.net>
     * @param $time int timestamp to format
     * @return string formatted string
     */
    static function formatTime($time)
    {
        if ($time == null || $time == 0)
            return "never";

        $now = time();
        $seconds = $now - $time;
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $days = floor($hours / 24);
        $weeks = floor($days / 7);
        $months = floor($weeks / 4.3);
        $years = floor($months / 12);

        if ($seconds < 60) {
            if ($seconds < 20)
                return "just now";
            if ($seconds < 40)
                return "recently";
            return Format::writtenNumber($seconds) . " seconds ago";
        }
        if ($minutes < 60) {
            if ($minutes == 1)
                return "one minute ago";
            return Format::writtenNumber($minutes) . " minutes ago";
        }
        if ($hours < 24) {
            if ($hours == 1)
                return "one hour ago";
            return Format::writtenNumber($hours) . " hours ago";
        }
        if ($days < 7) {
            if ($days == 1)
                return "one day ago";
            return Format::writtenNumber($days) . " days ago";
        }
        if ($weeks < 4.3) {
            if ($weeks == 1)
                return "one week ago";
            return Format::writtenNumber($weeks) . " weeks ago";
        }
        if ($months < 12) {
            if ($months == 1)
                return "one month ago";
            return Format::writtenNumber($months) . " months ago";
        }
        if ($years == 1)
            return "one year ago";
        return Format::writtenNumber($years) . " years ago";
    }

    /**
     * Returns a string of the given number.
     * Numbers from 0 to 12 are written (e.g. 1: one, 2: two...)
     * @author Sefa Eyeoglu <contact@scrumplex.net>
     * @param $number int number to format
     * @return string|int written number (0:12) or $number
     */
    static function writtenNumber($number)
    {
        switch ($number) {
            case 0:
                return "zero";
            case 1:
                return "one";
            case 2:
                return "two";
            case 3:
                return "three";
            case 4:
                return "four";
            case 5:
                return "five";
            case 6:
                return "six";
            case 7:
                return "seven";
            case 8:
                return "eight";
            case 9:
                return "nine";
            case 10:
                return "ten";
            case 11:
                return "eleven";
            case 12:
                return "twelve";
        }
        return $number;
    }

}