<?php

function formatTime($time)
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
        return niceNumber($seconds) . " seconds ago";
    }
    if ($minutes < 60) {
        if ($minutes == 1)
            return "one minute ago";
        return niceNumber($minutes) . " minutes ago";
    }
    if ($hours < 24) {
        if ($hours == 1)
            return "one hour ago";
        return niceNumber($hours) . " hours ago";
    }
    if ($days < 7) {
        if ($days == 1)
            return "one day ago";
        return niceNumber($days) . " days ago";
    }
    if ($weeks < 4.3) {
        if ($weeks == 1)
            return "one week ago";
        return niceNumber($weeks) . " weeks ago";
    }
    if ($months < 12) {
        if ($months == 1)
            return "one month ago";
        return niceNumber($months) . " months ago";
    }
    if ($years == 1)
        return "one year ago";
    return niceNumber($years) . " years ago";
}

function niceNumber($number)
{
    switch ($number) {
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

function replaceCriticalCharacters($string)
{
    if ($string == null)
        return $string;
    $string = str_replace("<", "&lt;", $string);
    $string = str_replace(">", "&gt;", $string);
    return $string;
}
