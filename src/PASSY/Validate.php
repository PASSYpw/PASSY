<?php
/**
 * Created by PhpStorm.
 * User: sefa
 * Date: 16.11.17
 * Time: 20:31
 */

namespace PASSY;


class Validate
{
    static function validateLoginUsername($username)
    {
        return strlen($username) >= 3;
    }

    static function validateLoginPassword($password)
    {
        return strlen($password) >= 8;
    }
}