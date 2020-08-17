<?php

namespace app\base\db\drivers;

interface DbDriverInterface
{
    public function __construct($config);
}