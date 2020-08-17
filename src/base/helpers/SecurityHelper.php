<?php

namespace app\base\helpers;

class SecurityHelper
{
    public static function passToHash($password)
    {
        return MD5($password);
    }

    public static function randomString($length = 32)
    {
        $length = ($length < 4) ? 4 : $length;
        return bin2hex(random_bytes(($length-($length%2))/2));
    }
}