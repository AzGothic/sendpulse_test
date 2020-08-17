<?php

namespace app\base;

class RequestCli extends Request
{
    public function __construct()
    {
        parent::__construct();

        if (empty($this->_SERVER['argv']) || count($this->_SERVER['argv']) <= 2) {
            return;
        }

        parse_str(implode('&', array_slice($this->_SERVER['argv'], 2)), $_GET);
    }
}