<?php

namespace app\base;

class Request
{
    public function __construct()
    {

    }

    public function __get($name)
    {
        switch ($name) {
            case '_SERVER':
                return isset($_SERVER) ? $_SERVER : [];
                break;
            case '_GET':
                return isset($_GET) ? $_GET : [];
                break;
            case '_POST':
                return isset($_POST) ? $_POST : [];
                break;
            case '_FILES':
                return isset($_FILES) ? $_FILES : [];
                break;
            case '_COOKIE':
                return isset($_COOKIE) ? $_COOKIE : [];
                break;
            case '_SESSION':
                return isset($_SESSION) ? $_SESSION : [];
                break;
            case '_REQUEST':
                return isset($_REQUEST) ? $_REQUEST : [];
                break;
            case '_ENV':
                return isset($_ENV) ? $_ENV : [];
                break;
        }

        global $$name;
        return isset($$name) ? $$name : [];
    }
}